<?php

namespace App\Repository\Catalog;

use App\Entity\Catalog\Catalog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class CatalogRepository
 *
 * @package App\Repository\Catalog
 */
class CatalogRepository extends ServiceEntityRepository
{
    /**
     * CatalogRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Catalog::class);
    }
}
