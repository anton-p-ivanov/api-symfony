<?php

namespace App\Repository\User;

use App\Entity\User\User;
use App\Entity\User\Password;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class PasswordRepository
 * @package App\Repository\User
 */
class PasswordRepository extends ServiceEntityRepository
{
    /**
     * UserPasswordRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Password::class);
    }

    /**
     * @param User $user
     *
     * @return mixed
     */
    public function expireUserPasswords(User $user)
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder();

        return $queryBuilder->update('App:User\Password', 'p')
            ->set('p.isExpired', true)
            ->where('p.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
