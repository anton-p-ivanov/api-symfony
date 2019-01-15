<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * Class UserRepository
 * @package App\Repository
 */
class UserRepository extends EntityRepository
{
    /**
     * @return Query
     */
    public function search(): Query
    {
        return $this->createQueryBuilder('t')
            ->select(['t', 'w'])
            ->leftJoin('t.workflow', 'w')
            ->where('w.uuid IS NULL OR w.isDeleted = :isDeleted')
            ->setParameters(['isDeleted' => false])
            ->addOrderBy('t.fullName', 'ASC')
            ->setMaxResults(10)
            ->getQuery();
    }
}
