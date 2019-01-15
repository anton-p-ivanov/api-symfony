<?php

namespace App\Security;

use App\Entity\Role;
use App\Entity\User\User;
use App\Exceptions\OAuth\InvalidAccessTokenException;
use App\Exceptions\OAuth\InvalidClientException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;

/**
 * Class TokenAuthenticator
 * @package App\Security
 */
class TokenAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    /**
     * TokenAuthenticator constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->entityManager = $em;
    }

    /**
     * Called on every request to decide if this authenticator should be
     * used for the request. Returning false will cause this authenticator
     * to be skipped.
     *
     * @param Request $request
     *
     * @return bool
     */
    public function supports(Request $request): bool
    {
        return $request->headers->has('Access-Token');
    }

    /**
     * Called on every request. Return whatever credentials you want to
     * be passed to getUser() as $credentials.
     *
     * @param Request $request
     *
     * @return array
     */
    public function getCredentials(Request $request): array
    {
        return [
            'authorization' => $request->headers->get('Authorization'),
            'token' => $request->headers->get('Access-Token'),
        ];
    }

    /**
     * @param mixed $credentials
     * @param UserProviderInterface $userProvider
     *
     * @return null|UserInterface
     * @throws InvalidAccessTokenException
     */
    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        $apiToken = $credentials['token'];

        if (null === $apiToken) {
            throw new InvalidAccessTokenException('No Access-Token header found in request.');
        }

        // if a User object, checkCredentials() is called
        $accessToken = $this->entityManager->getRepository("App:OAuth\AccessToken")
            ->findOneBy(['token' => $apiToken]);

        if ($accessToken === null || $accessToken->isExpired()) {
            throw new InvalidAccessTokenException();
        }

        $user = $accessToken->getUser();
        if ($user === null) {
            $user = new User();
        }

        return $user;
    }

    /**
     * @param mixed $credentials
     * @param UserInterface $user
     *
     * @return bool
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        // Validate client
        $this->validateClient($credentials['authorization']);

        // Validate access_token
        $this->validateToken($credentials['token']);

        return true;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @param string $providerKey
     *
     * @return null|Response
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // on success, let the request continue
        return null;
    }

    /**
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return null|JsonResponse|Response
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $data = [
            'message' => strtr($exception->getMessageKey(), $exception->getMessageData())

            // or to translate this message
            // $this->translator->trans($exception->getMessageKey(), $exception->getMessageData())
        ];

        return new JsonResponse($data, Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     *
     * @param Request $request
     * @param AuthenticationException|null $authException
     *
     * @return JsonResponse
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        $data = [
            // you might translate this message
            'message' => 'Authentication Required'
        ];

        return new JsonResponse($data, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * @return bool
     */
    public function supportsRememberMe(): bool
    {
        return false;
    }

    /**
     * @param UserInterface|User $user
     * @param $providerKey
     *
     * @return PostAuthenticationGuardToken
     */
    public function createAuthenticatedToken(UserInterface $user, $providerKey)
    {
        $roles = $user->getRoles()
            ->map(function (Role $role) { return $role->getCode(); })
            ->getValues();

        return new PostAuthenticationGuardToken(
            $user,
            $providerKey,
            $roles
        );
    }

    /**
     * @param null|string $header
     *
     * @throws InvalidClientException
     */
    protected function validateClient(?string $header)
    {
        if (!$header) {
            throw new InvalidClientException('No authorization header found in request.');
        }

        list($type, $data) = preg_split('/\s/', $header);
        if (strtolower($type) !== 'basic') {
            throw new InvalidClientException('Invalid authorization header.');
        }

        $data = base64_decode($data);
        if ($data === false) {
            throw new InvalidClientException('Invalid authorization data.');
        }

        list($client_id, $client_secret) = preg_split('/:/', $data);

        $client = $this->entityManager->getRepository('App:OAuth\Client')->findOneBy(
            ['uuid' => $client_id, 'secret' => $client_secret]
        );

        if (!$client) {
            throw new InvalidClientException('Invalid client credentials provided.');
        }
    }

    /**
     * @param null|string $header
     *
     * @throws InvalidAccessTokenException
     */
    protected function validateToken(?string $header)
    {
        if (!$header) {
            throw new InvalidAccessTokenException('No Access-Token header found in request.');
        }

        $token = $this->entityManager
            ->getRepository('App:OAuth\AccessToken')
            ->findOneBy(['token' => $header]);

        if ($token === null || $token->isExpired()) {
            throw new InvalidAccessTokenException();
        }
    }
}