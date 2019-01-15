<?php

namespace App\Controller;

use App\Form\Profile as Form;
use App\Response\InvalidDataResponse;
use App\Service\Client;
use App\Traits\RestFormsTrait;
use Symfony\Component\HttpFoundation as Http;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class ProfileController
 * @package App\Controller
 *
 * @Route("/profile")
 */
class ProfileController extends RestController
{
    use RestFormsTrait;

    /**
     * @var string 
     */
    protected $repository = \App\Entity\User\User::class;

    /**
     * @var Client
     */
    private $client;

    /**
     * ProfileController constructor.
     *
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * List methods which can be executed WITH OUT permission checks.
     * @return array
     */
    public function skipPermissionChecks(): array
    {
        return ['reset', 'password', 'register', 'confirm', 'connect'];
    }

    /**
     * @Route("/reset", methods={"POST"})
     *
     * @param Http\Request $request
     * @param Form\ResetHandler $handler
     *
     * @return Http\Response
     */
    public function reset(Http\Request $request, Form\ResetHandler $handler): Http\Response
    {
        $model = new Form\Reset();
        $form = $this->createForm(Form\ResetForm::class, $model);

        $this->processForm($request, $form);

        if ($form->isValid()) {
            if ($handler->reset($model)) {
                return new Http\JsonResponse(['username' => $model->getEmail()]);
            }
        }

        return new InvalidDataResponse($this->getFormErrors($form));
    }

    /**
     * @Route("/password", methods={"POST"})
     *
     * @param Http\Request $request
     * @param Form\PasswordHandler $handler
     *
     * @return Http\Response
     */
    public function password(Http\Request $request, Form\PasswordHandler $handler): Http\Response
    {
        $model = new Form\Password();
        $form = $this->createForm(Form\PasswordForm::class, $model);

        $this->processForm($request, $form);

        if ($form->isValid()) {
            if ($handler->change($model)) {
                return new Http\JsonResponse($handler->getUser());
            }
        }

        return new InvalidDataResponse($this->getFormErrors($form));
    }

    /**
     * @Route("/register", methods={"POST"})
     *
     * @param Http\Request $request
     * @param Form\RegisterHandler $handler
     *
     * @param Client $client
     *
     * @return Http\Response
     */
    public function register(Http\Request $request, Form\RegisterHandler $handler, Client $client): Http\Response
    {
        $model = new Form\Register();
        $model->setRoles(['USER']);
        $model->setSites([$client->getUuid()]);

        $form = $this->createForm(Form\RegisterForm::class, $model);
        $this->processForm($request, $form);

        if ($form->isValid()) {
            if ($handler->register($model, $form)) {
                return new Http\JsonResponse($handler->getUser());
            }
        }

        return new InvalidDataResponse($this->getFormErrors($form));
    }

    /**
     * @Route("/confirm", methods={"POST"})
     *
     * @param Http\Request $request
     * @param Form\ConfirmHandler $handler
     *
     * @return Http\Response
     */
    public function confirm(Http\Request $request, Form\ConfirmHandler $handler): Http\Response
    {
        $model = new Form\Confirm();
        $form = $this->createForm(Form\ConfirmForm::class, $model);

        $this->processForm($request, $form);

        if ($form->isValid()) {
            if ($handler->confirm($model, $form)) {
                return new Http\JsonResponse($handler->getUser());
            }
        }

        return new InvalidDataResponse($this->getFormErrors($form));
    }

    /**
     * @Route("/connect", methods={"PUT"})
     *
     * @param Http\Request $request
     * @param Form\ConnectHandler $handler
     *
     * @param Client $client
     *
     * @return Http\Response
     */
    public function connect(Http\Request $request, Form\ConnectHandler $handler, Client $client): Http\Response
    {
        $model = new Form\Connect();
        $model->setRoles(['USER']);
        $model->setSites([$client->getUuid()]);

        $form = $this->createForm(Form\ConnectForm::class, $model);
        $this->processForm($request, $form);

        if ($form->isValid()) {
            if ($handler->connect($model, $form)) {
                return new Http\JsonResponse($handler->getUser());
            }
        }

        return new InvalidDataResponse($this->getFormErrors($form));
    }
}
