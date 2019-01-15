<?php

namespace App\Repository\Catalog;

use App\Entity\Catalog\Catalog;
use App\Entity\Catalog\Element;
use App\Entity\Field\Field;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * Class ElementRepository
 *
 * @package App\Repository\Catalog
 */
class ElementRepository extends ServiceEntityRepository
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
        parent::__construct($registry, Element::class);

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
        if (!array_key_exists('catalog', $conditions)) {
            throw new \InvalidArgumentException('Missing required parameter `catalog`.');
        }

        $catalog = $this->getEntityManager()
            ->getRepository('App:Catalog\Catalog')
            ->findOneBy(['code' => $conditions['catalog']]);

        if (!$catalog) {
            throw new \InvalidArgumentException('Invalid catalog identifier.');
        }

        $conditions['catalog'] = $catalog;

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

        // Apply other filtering rules
        $this->filter($conditions);

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

    /**
     * @param array $conditions
     */
    protected function filter(array $conditions)
    {
        $skipAttributes = ['order'];

        foreach ($conditions as $name => $value) {
            if (in_array($name, $skipAttributes) || $value === '') {
                continue;
            }

            if (strpos($name, 'property_') === 0) {
                // Filtering by properties values
                $this->filterByProperty(substr($name, strlen('property_')), $value, $conditions['catalog']);
            }
            else if ($name == 'section') {
                // Filtering elements by parent section
                $this->filterBySection($value);
            }
            else if ($name == 'period') {
                // Filtering elements by `activeFrom` dates
                $this->filterByDates($value);
            }
            else {
                // Other filtering rules
                $this->filterInternal($name, $value);
            }
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    protected function filterInternal(string $name, $value)
    {
        $action = is_array($value) ? 'in' : 'eq';
        $actions = ['~' => 'like', '>' => 'gt', '<' => 'lt', '>=' => 'gte', '<=' => 'lte'];
        foreach ($actions as $key => $val) {
            if (strpos($name, $key) === 0) {
                $name = substr($name, 1);
                $action = $val;
                break;
            }
        }

        if ($value && property_exists($this->getEntityName(), $name)) {
            $param = sprintf("t_%s_value", $name);
            $this->builder->andWhere($this->builder->expr()->andX(
                $this->builder->expr()->$action("t.$name", ":$param")
            ));

            $this->builder->setParameter($param, $action === 'like' ? "%$value%" : $value);
        }
    }

    /**
     * @param Catalog $catalog
     * @param string $name
     *
     * @return Field|null
     */
    private function getProperty(Catalog $catalog, string $name): ?Field
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('f')
            ->from('App:Field\Field', 'f')
            ->where($this->builder->expr()->andX(
                $this->builder->expr()->eq('f.code', ':field'),
                $this->builder->expr()->eq('f.hash', ':hash')
            ))
            ->setParameter('field', $name)
            ->setParameter('hash', strtoupper(md5('App\Entity\Catalog\Catalog' . $catalog->getUuid())))
            ->getQuery()
            ->getSingleResult();
    }

    /**
     * @param string $name
     * @param mixed $value
     * @param Catalog $catalog
     */
    protected function filterByProperty(string $name, $value, Catalog $catalog)
    {
        if (!$property = $this->getProperty($catalog, $name)) {
            return;
        }

        $params = ['field' => $property];

        if (is_array($value) && $property->isMultiple()) {
            $predicates = $this->builder->expr()->orX();
            foreach ($value as $index => $item) {
                $predicates->add($this->builder->expr()->like('v.value', ":value_$index"));
                $params["value_$index"] = "%$item%";
            }
        }
        else {
            $predicates = $this->builder->expr()->eq('v.value', ':value');
            $params["value"] = $value;
        }

        $query = $this->getEntityManager()->createQueryBuilder()
            ->select('v')
            ->from('App:Catalog\ElementValue', 'v')
            ->innerJoin('v.element', 'e')
            ->innerJoin('v.field', 'f')
            ->where($this->builder->expr()->andX(
                $predicates,
                $this->builder->expr()->eq('v.field', ':field')
            ))
            ->setParameters($params)
            ->getQuery();

        $elements = array_map([$this, 'getElementsUuid'], $query->getArrayResult());
        $previous = $this->builder->getParameter('elements');

        if ($previous) {
            // If previous element uuid exist just update it
            $elements = array_intersect($previous->getValue(), $elements);
        }
        else {
            // Set builder condition
            $this->builder->andWhere($this->builder->expr()->andX(
                $this->builder->expr()->in('t.uuid', ':elements')
            ));
        }

        $this->builder->setParameter('elements', $elements);
    }

    /**
     * @param array $item
     *
     * @return string
     */
    protected function getElementsUuid(array $item): string
    {
        return $item['element_uuid'];
    }

    /**
     * @param string|array $dates
     */
    protected function filterByDates($dates)
    {
        if (is_array($dates)) {
            $predicates = $this->builder->expr()->andX();

            if (isset($dates['from'])) {
                $predicates->add('t.activeFrom > :from');
                $this->builder->setParameter('from', new \DateTime($dates['from']));
            }

            if (isset($dates['to'])) {
                $predicates->add('t.activeFrom < :to');
                $this->builder->setParameter('to', new \DateTime($dates['to']));
            }

            $this->builder->andWhere($predicates);
        }
    }

    /**
     * @param string|string[] $code
     */
    protected function filterBySection($code)
    {
        $sections = $this->getEntityManager()
            ->getRepository('App:Catalog\Element')
            ->findBy(['code' => $code, 'type' => Element::TYPE_SECTION]);

        if (!$sections) {
            throw new \InvalidArgumentException('Invalid section identifier.');
        }

        $nodes = $this->getEntityManager()
            ->getRepository('App:Catalog\Tree')
            ->findBy(['element' => $sections]);

        $predicates = $this->builder->expr()->orX();
        foreach ($nodes as $index => $node) {
            $predicates->add("n.leftMargin > :leftMargin_$index AND n.rightMargin < :rightMargin_$index");

            $params = [
                "leftMargin_$index" => $node->getLeftMargin(),
                "rightMargin_$index" => $node->getRightMargin()
            ];

            foreach ($params as $name => $value) {
                $this->builder->setParameter($name, $value);
            }
        }

        $this->builder
            ->leftJoin('t.nodes', 'n')
            ->andWhere($predicates);
    }
}
