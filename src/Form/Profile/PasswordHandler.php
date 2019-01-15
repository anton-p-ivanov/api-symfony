<?php

namespace App\Form\Profile;

use App\Entity\User\User;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class PasswordHandler
 *
 * @package App\Form\Profile
 */
class PasswordHandler
{
    /**
     * @var null|User
     */
    private $user;

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
     * @param Password $model
     *
     * @return bool
     */
    public function change(Password $model): bool
    {
        $queryBuilder = $this->manager->createQueryBuilder();

        $this->user = $queryBuilder->select(['u'])
            ->from('App:User\User', 'u')
            ->innerJoin('u.checkwords', 'c')
            ->where('c.checkword = :code AND u.isActive = :isActive')
            ->setParameters([
                'code' => $model->getCode(),
                'isActive' => true
            ])
            ->getQuery()
            ->getOneOrNullResult();

        if (!$this->user) {
            return false;
        }

        $password = (new \App\Entity\User\Password())
            ->setUser($this->user)
            ->setPassword($model->getPassword());

        $this->manager->persist($password);
        $this->manager->flush();

        return $password->getUuid() !== null;
    }

    /**
     * @return null|User
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}