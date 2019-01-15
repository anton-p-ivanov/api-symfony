<?php

namespace App\Listener\User;

use App\Entity\User\User;
use App\Entity\User\Checkword;
use App\Entity\User\Password;
use App\Security\Encoder\PasswordEncoder;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

/**
 * Class PasswordListener
 *
 * @package App\Listener\User
 */
class PasswordListener
{
    /**
     * @var PasswordEncoder
     */
    private $encoder;

    /**
     * UserCheckwordListener constructor.
     *
     * @param PasswordEncoder $encoder
     */
    public function __construct(PasswordEncoder $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param Password $password
     * @param LifecycleEventArgs $event
     */
    public function prePersist(Password $password, LifecycleEventArgs $event)
    {
        // Storing non-secured password for user notification
        $password->nonSecuredPassword = $password->getPassword();

        // Encode password
        $password->setPassword($this->encoder->encodePassword(
            $password->getPassword(),
            $password->getSalt()
        ));

        // Expire all previous user passwords
        $event
            ->getObjectManager()
            ->getRepository('App:User\Password')
            ->expireUserPasswords($password->getUser());

        // Expire all previous user checkwords
        $event
            ->getObjectManager()
            ->getRepository('App:User\Checkword')
            ->expireUserCheckwords($password->getUser());
    }

    /**
     * @param Password $userPassword
     * @param LifecycleEventArgs $event
     */
    public function postPersist(Password $userPassword, LifecycleEventArgs $event)
    {
        $user = $userPassword->getUser();

        if ($user->scenario === User::SCENARIO_USER_REGISTER) {
            $userCheckword = new Checkword();
            $userCheckword->setUser($user);

            $manager = $event->getObjectManager();
            $manager->persist($userCheckword);
            $manager->flush();
        }
    }
}