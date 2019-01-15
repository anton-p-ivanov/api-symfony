<?php

namespace App\Listener\User;

use App\Entity\User\Checkword;
use App\Service\Client;
use App\Service\Mail;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class CheckwordListener
 *
 * @package App\Listener\User
 */
class CheckwordListener
{
    /**
     * @var Mail
     */
    private $mailer;

    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @var Client
     */
    private $client;

    /**
     * UserCheckwordListener constructor.
     *
     * @param Mail $mailer
     * @param UserPasswordEncoderInterface $encoder
     * @param Client $client
     */
    public function __construct(Mail $mailer, UserPasswordEncoderInterface $encoder, Client $client)
    {
        $this->mailer = $mailer;
        $this->encoder = $encoder;
        $this->client = $client;
    }

    /**
     * @param Checkword $checkword
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Checkword $checkword, LifecycleEventArgs $event)
    {
        $checkword->setCheckword($checkword->nonSecuredCheckword);

        $event
            ->getObjectManager()
            ->getRepository('App:User\Checkword')
            ->expireUserCheckwords($checkword->getUser());
    }

    /**
     * @param Checkword $checkword
     */
    public function postPersist(Checkword $checkword)
    {
        // Skip user notification
        if ($checkword->isNotificationSent) {
            return;
        }

        $params = [
            'USER_EMAIL' => $checkword->getUser()->getUsername(),
            'USER_CHECKWORD' => $checkword->nonSecuredCheckword,
            'USER_PASSWORD' => $checkword->getUser()->getUserPassword()->nonSecuredPassword,
            'CLIENT_ID' => $this->client->getUuid()
        ];

        $checkword->isNotificationSent = $this->mailer
            ->template($checkword->templates[$checkword->getUser()->scenario])
            ->params($params)
            ->send();
    }
}