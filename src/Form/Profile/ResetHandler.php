<?php

namespace App\Form\Profile;

use App\Entity\User\Checkword;
use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class ResetHandler
 *
 * @package App\Form\Profile
 */
class ResetHandler
{
    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * ResetHandler constructor.
     *
     * @param EntityManagerInterface $manager
     */
    public function __construct(EntityManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @param Reset $model
     *
     * @return bool
     */
    public function reset(Reset $model): bool
    {
        $user = $this->manager
            ->getRepository('App:User\User')
            ->findOneBy(['email' => $model->getEmail(), 'isActive' => true]);

        if (!$user) {
            return false;
        }

        $user->scenario = User::SCENARIO_USER_RESET;

        $checkword = (new Checkword())->setUser($user);

        $this->manager->persist($checkword);
        $this->manager->flush();

        return $checkword->getUuid() !== null;
    }
}