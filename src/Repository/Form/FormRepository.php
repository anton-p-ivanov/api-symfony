<?php

namespace App\Repository\Form;

use App\Entity\Form\Form;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class FormRepository
 *
 * @package App\Repository\Form
 */
class FormRepository extends ServiceEntityRepository
{
    /**
     * @var QueryBuilder
     */
    private $builder;

    /**
     * ElementRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Form::class);

        // Init query builder
        $this->builder = $this->createQueryBuilder('t');
    }

    /**
     * @param array $conditions
     *
     * @return \Doctrine\ORM\Query
     */
    public function search($conditions = [])
    {
        $this->builder = $this->prepareSearchQuery($conditions);

        return $this->builder
            ->select(['t'])
            ->setMaxResults($conditions['limit'] ?? 10)
            ->getQuery();
    }

    /**
     * @param array $conditions
     *
     * @return QueryBuilder
     */
    protected function prepareSearchQuery(array $conditions): QueryBuilder
    {
        $params = ['isDeleted' => false, 'published' => 'PUBLISHED'];
        $predicates = $this->builder->expr()->andX();
        $predicates->add('w.uuid IS NULL OR w.isDeleted = :isDeleted');
        $predicates->add('s.code = :published');

        foreach ($params as $name => $value) {
            $this->builder->setParameter($name, $value);
        }

        // Apply ordering rules
        $this->order($conditions['order'] ?? null);

        return $this->builder
            ->leftJoin('t.workflow', 'w')
            ->leftJoin('w.status', 's')
            ->addSelect(['w', 's'])
            ->andWhere($predicates);
    }

    /**
     * @param string|null $sort
     */
    protected function order($sort)
    {
        if ($sort) {
            foreach (preg_split('/\s*,\s*/', $sort) as $sort) {
                $order = 'ASC';
                if (strpos($sort, '-') === 0) {
                    $order = 'DESC';
                    $sort = substr($sort, 1);
                }

                $this->builder->addOrderBy("t.$sort", $order);
            }
        }
        else {
            // Default sorting
            $this->builder->addOrderBy('t.sort', 'ASC');
            $this->builder->addOrderBy('t.title', 'ASC');
        }
    }
}
