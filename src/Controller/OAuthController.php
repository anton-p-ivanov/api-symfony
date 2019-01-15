<?php

namespace App\Controller;

use App\Entity\OAuth\AccessToken;
use App\Entity\OAuth\Client;
use App\Entity\OAuth\RefreshToken;
use App\Entity\User\User;
use App\Exceptions\OAuth as Exception;
use App\Security\Encoder\PasswordEncoder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation as Http;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class OAuthController
 * @package App\Controller
 */
class OAuthController extends Controller
{
    const GRANT_TYPE_CLIENT = 'client_credentials';
    const GRANT_TYPE_PASSWORD = 'password';
    const GRANT_TYPE_REFRESH = 'refresh_token';

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var PasswordEncoder
     */
    private $encoder;

    /**
     * @var User|null
     */
    private $user;

    /**
     * @var AccessToken|null
     */
    private $accessToken;

    /**
     * @var RefreshToken|null
     */
    private $refreshToken;

    /**
     * @var Client|null
     */
    private $client;

    /**
     * @var array
     */
    protected $supportedGrantTypes = [
        self::GRANT_TYPE_CLIENT,
        self::GRANT_TYPE_PASSWORD,
        self::GRANT_TYPE_REFRESH
    ];

    /**
     * @param Http\Request $request
     * @param PasswordEncoder $encoder
     * @param TranslatorInterface|Translator $translator
     *
     * @return Http\Response
     * @throws Exception\UnsupportedGrantTypeException
     */
    public function token(
        Http\Request $request,
        PasswordEncoder $encoder,
        TranslatorInterface $translator): Http\Response
    {
        $this->encoder = $encoder;
        $this->translator = $translator;

        $requestParams = json_decode($request->getContent(), true);
        $request->attributes->add($requestParams);

        $grantType = $request->get('grant_type', self::GRANT_TYPE_CLIENT);

        if (!$this->isGrantTypeValid($grantType)) {
            throw new Exception\UnsupportedGrantTypeException();
        }

        // Remove underscores and camelize
        $strGrantType = lcfirst(implode(array_map('ucfirst', explode('_', $grantType))));

        // Validating request
        $this->validateRequest($request);

        // Validating client
        $this->validateClient();

        $params = [];

        if ($grantType === self::GRANT_TYPE_PASSWORD) {
            $this->validateUser($request);
        }
        else if ($grantType === self::GRANT_TYPE_REFRESH) {
            $params['refreshToken'] = $this->validateRefreshToken($request);
        }

        $response = call_user_func_array([$this, $strGrantType . 'GrantTypeHandler'], $params);

        $headers = [
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache'
        ];

        return Http\JsonResponse::create($response, Http\Response::HTTP_CREATED, $headers);
    }

    /**
     * @param Http\Request $request
     *
     * @throws Exception\InvalidRequestException
     */
    protected function validateRequest(Http\Request $request)
    {
        $authorization = $request->headers->get('authorization');
        if (!$authorization) {
            throw new Exception\InvalidRequestException('No authorization header found in request headers.');
        }

        $username = $_SERVER['PHP_AUTH_USER'] ?? null;
        $password = $_SERVER['PHP_AUTH_PW'] ?? null;

        if ('Basic ' . base64_encode("$username:$password") !== $authorization) {
            throw new Exception\InvalidRequestException('Invalid authorization request.');
        }

        $grantType = $request->get('grant_type');

        if ($grantType === self::GRANT_TYPE_PASSWORD) {
            $username = $request->get('username');
            $password = $request->get('password');

            if (!$username || !$password) {
                throw new Exception\InvalidRequestException('No username or password found in request body.');
            }
        }
        else if ($grantType === self::GRANT_TYPE_REFRESH) {
            $refreshToken = $request->get('refresh_token');

            if (!$refreshToken) {
                throw new Exception\InvalidRequestException('No refresh_token found in request body.');
            }
        }
    }

    /**
     * @throws Exception\InvalidClientException
     */
    protected function validateClient()
    {
        $username = $_SERVER['PHP_AUTH_USER'];
        $password = $_SERVER['PHP_AUTH_PW'];

        $client = $this->getClient($username);
        if (!$client) {
            throw new Exception\InvalidClientException(sprintf('Client with identifier `%s` not found.', $username));
        }

        $this->client = $client;

        if ($this->client->getSecret() !== $password) {
            throw new Exception\InvalidClientException(sprintf('Invalid secret for client `%s`.', $username));
        }
    }

    /**
     * @param Http\Request $request
     *
     * @throws Exception\InvalidGrantException
     * @throws Exception\InvalidUserException
     */
    protected function validateUser(Http\Request $request)
    {
        $username = $request->get('username');
        $password = $request->get('password');

        $user = $this->getOAuthUser($username);

        // Validate user
        if (!$user) {
            throw new Exception\InvalidUserException($this->trans('exceptions.oauth.user_not_found'));
        }

        // Validate user password
        if (!$this->encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {
            throw new Exception\InvalidGrantException($this->trans('exceptions.oauth.invalid_password'));
        }

        $this->user = $user;
    }

    /**
     * @param string $messageId
     * @param array $params
     *
     * @return string
     */
    protected function trans(string $messageId, $params = [])
    {
        return $this->translator->trans($messageId, $params);
    }

    /**
     * @param Http\Request $request
     *
     * @return RefreshToken
     * @throws Exception\InvalidGrantException
     */
    protected function validateRefreshToken(Http\Request $request): RefreshToken
    {
        $refreshToken = $request->get('refresh_token');

        $entity = $this->getDoctrine()
            ->getRepository('App:OAuth\RefreshToken')
            ->findOneBy(['token' => $refreshToken]);

        if (!$entity || $entity->getExpiresAt() < new \DateTime()) {
            throw new Exception\InvalidGrantException('Refresh token is invalid or expired.');
        }

        return $entity;
    }

    /**
     * @param string $client_uuid
     *
     * @return Client|null
     */
    protected function getClient(string $client_uuid): ?Client
    {
        return $this->getDoctrine()->getRepository('App:OAuth\Client')->find($client_uuid);
    }

    /**
     * @param string $user_uuid
     *
     * @return User|null
     */
    protected function getOAuthUser(string $user_uuid): ?User
    {
        return $this->getDoctrine()->getRepository('App:User\User')->findOneBy(['email' => $user_uuid]);
    }

    /**
     * @return array
     */
    protected function clientCredentialsGrantTypeHandler(): array
    {
        $manager = $this->getDoctrine()->getManager();
        $tokens = [
            'access' => AccessToken::class,
            'refresh' => RefreshToken::class
        ];

        foreach ($tokens as $name => $className) {
            $tokenName = $name . 'Token';
            $this->$tokenName = new $className();
            $this->$tokenName->setClient($this->client);

            $manager->persist($this->$tokenName);
        }

        $manager->flush();

        return $this->getResponse();
    }

    /**
     * @return array
     */
    protected function passwordGrantTypeHandler(): array
    {
        $manager = $this->getDoctrine()->getManager();
        $tokens = [
            'access' => AccessToken::class,
            'refresh' => RefreshToken::class
        ];

        foreach ($tokens as $name => $className) {
            $tokenName = $name . 'Token';
            $this->$tokenName = new $className();
            $this->$tokenName->setClient($this->client);
            $this->$tokenName->setUser($this->user);

            $manager->persist($this->$tokenName);
        }

        $manager->flush();

        return $this->getResponse();
    }

    /**
     * @param RefreshToken $refreshToken
     *
     * @return array
     */
    protected function refreshTokenGrantTypeHandler(RefreshToken $refreshToken): array
    {
        $manager = $this->getDoctrine()->getManager();
        $this->refreshToken = $refreshToken;
        $tokens = [
            'access' => AccessToken::class,
        ];

        foreach ($tokens as $name => $className) {
            $tokenName = $name . 'Token';
            $this->$tokenName = new $className();
            $this->$tokenName->setClient($refreshToken->getClient());
            $this->$tokenName->setUser($refreshToken->getUser());

            $manager->persist($this->$tokenName);
        }

        $manager->flush();

        return $this->getResponse();
    }

    /**
     * @return array
     */
    protected function getResponse(): array
    {
        return [
            'access_token' => $this->accessToken->getToken(),
            'token_type' => 'Bearer',
            'expires_in' => $this->accessToken->getExpiresAt()->format('c'),
            'refresh_token' => $this->refreshToken->getToken()
        ];
    }

    /**
     * @param string $grantType
     *
     * @return bool
     */
    protected function isGrantTypeValid(string $grantType): bool
    {
        return in_array($grantType, $this->supportedGrantTypes);
    }
}
