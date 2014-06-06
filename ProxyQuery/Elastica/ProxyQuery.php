<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DatagridBundle\ProxyQuery\Elastica;

use Elastica\Facet\AbstractFacet;
use Elastica\Filter\AbstractFilter;
use Sonata\DatagridBundle\ProxyQuery\BaseProxyQuery;
use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;

/**
 * This class try to unify the query usage with Doctrine
 */
class ProxyQuery extends BaseProxyQuery implements ProxyQueryInterface
{
    /**
     * @var array
     */
    private $filters = array();

    /**
     * @var array
     */
    private $facets = array();

    /**
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * @param array $filters
     */
    public function setFilters($filters)
    {
        $this->filters = $filters;
    }

    /**
     * @return array
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * @param AbstractFilter $filter
     */
    public function addFilter(AbstractFilter $filter)
    {
        $this->filters[] = $filter;
    }

    /**
     * @param array $facets
     */
    public function setFacets($facets)
    {
        $this->facets = $facets;
    }

    /**
     * @return array
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * @param AbstractFacet $facet
     */
    public function addFacet(AbstractFacet $facet)
    {
        $this->facets[] = $facet;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        if (null === $this->results) {
            $this->execute();
        }

        return parent::getResults();
    }

    /**
     * {@inheritdoc}
     */
    public function getSortBy()
    {
        return parent::getSortBy() ? : array();
    }

    /**
     * @return QueryBuilder
     */
    public function getQueryBuilder()
    {
        return parent::getQueryBuilder();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $params = array(), $hydrationMode = null)
    {
        $this->prepareQuery();

        $this->results = $this->getQueryBuilder()->getFinder()->find($this->getQueryBuilder()->getQuery());

        return $this->getResults();
    }

    /**
     * @return int
     */
    public function count()
    {
        $this->prepareQuery();

        return $this->getQueryBuilder()
            ->getFinder()
            ->createPaginatorAdapter($this->getQueryBuilder()->getQuery())
            ->getTotalHits();
    }

    /**
     * Applies sort, filters, facets & pagination to the query
     */
    protected function prepareQuery()
    {
        // Sort
        $this->getQueryBuilder()->getQuery()->setSort($this->getSortBy());

        // Filter
        $compoundFilter = new \Elastica\Filter\Bool();
        foreach ($this->getFilters() as $filter) {
            $compoundFilter->addMust($filter);
        }
        $this->getQueryBuilder()->getQuery()->setFilter($compoundFilter);

        // Facets
        foreach ($this->getFacets() as $facet) {
            $facet->setFilter($compoundFilter);
            $this->getQueryBuilder()->getQuery()->addFacet($facet);
        }

        // Limit & offset
        if (0 < $this->getMaxResults()) {
            $this->getQueryBuilder()->getQuery()->setSize($this->getMaxResults());
        }
        $this->getQueryBuilder()->getQuery()->setFrom($this->getFirstResult());
    }
}
