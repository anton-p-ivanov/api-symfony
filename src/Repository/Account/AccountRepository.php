<?php

namespace App\Repository\Account;

use App\Entity\Account\Account;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class AccountRepository
 *
 * @package App\Repository\Account
 */
class AccountRepository extends ServiceEntityRepository
{
    /**
     * AccountRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Account::class);
    }

    /**
     * @param string $code
     *
     * @return Account|null
     */
    public function findOneByCode(string $code)
    {
        $queryBuilder = $this->createQueryBuilder('t');

        return $queryBuilder
            ->select(['t'])
            ->innerJoin('t.codes', 'c')
            ->andWhere('c.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
