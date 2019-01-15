<?php

namespace App\Form;

use App\Entity\Form\Form;
use App\Entity\Form\Result;
use App\Entity\Form\Status;
use App\Entity\Mail\Template;
use App\Exceptions\OAuth\InvalidClientException;
use App\Service\Mail;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * Class BaseHandler
 *
 * @package App\Form
 */
class BaseHandler
{
    /**
     * @var EntityManagerInterface
     */
    protected $manager;

    /**
     * @var Mail
     */
    private $mailer;

    /**
     * @var Result
     */
    private $result;

    /**
     * @var \Symfony\Component\HttpFoundation\Request|null
     */
    private $request;

    /**
     * ResetHandler constructor.
     *
     * @param RequestStack $requestStack
     * @param Mail $mailer
     * @param EntityManagerInterface $manager
     */
    public function __construct(RequestStack $requestStack, Mail $mailer, EntityManagerInterface $manager)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->mailer = $mailer;
        $this->manager = $manager;
    }

    /**
     * @param array $data
     * @param Form $form
     *
     * @return bool
     */
    public function process(array $data, Form $form): bool
    {
        $status = $this->manager->getRepository(Status::class)->findOneBy([
            'isDefault' => 1,
            'form' => $form
        ]);

        if (!$status) {
            throw new BadRequestHttpException('Could not find default status.');
        }

        $this->result = new Result();
        $attributes = [
            'form' => $form,
            'status' => $status,
            'data' => $data
        ];

        foreach ($attributes as $name => $value) {
            $this->result->{'set' . ucfirst($name)}($value);
        }

        $this->manager->persist($this->result);
        $this->manager->flush();

        $isSuccess = $this->result->getUuid() !== null;

        if ($isSuccess) {
            if ($template = $form->getMailTemplate()) {
                $this->sendMail($template);
            }

            if ($template = $status->getMailTemplate()) {
                $this->sendMail($template);
            }
        }

        return $isSuccess;
    }

    /**
     * @return Result
     */
    public function getResult(): Result
    {
        return $this->result;
    }

    /**
     * @param Template $template
     *
     * @return bool
     */
    protected function sendMail(Template $template): bool
    {
        if (!$template->isActive()) {
            return false;
        }

        $params = [
            'FORM_RESULT_UUID' => $this->result->getUuid(),
            'CLIENT_ID' => $this->getClientID()
        ];

        foreach ($this->result->getRawData() as $attribute => $value) {
            $params[strtoupper($attribute)] = is_array($value) ? implode(";\n", $value) : $value;
        }

        return $this->mailer
            ->template($template->getCode())
            ->params($params)
            ->send();
    }

    /**
     * @return string
     * @throws InvalidClientException
     */
    private function getClientID(): string
    {
        $header = $this->request->headers->get('Authorization');

        list($type, $data) = preg_split('/\s/', $header);
        if (strtolower($type) !== 'basic') {
            throw new InvalidClientException('Invalid authorization header.');
        }

        $data = base64_decode($data);
        if ($data === false) {
            throw new InvalidClientException('Invalid authorization data.');
        }

        list($client_id) = preg_split('/:/', $data);

        return $client_id;
    }
}