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

use Sonata\DatagridBundle\ProxyQuery\BaseProxyQuery;
use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;

/**
 * This class try to unify the query usage with Doctrine
 */
class ProxyQuery extends BaseProxyQuery implements ProxyQueryInterface
{
    /**
     * @param QueryBuilder $queryBuilder
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function getResults()
    {
        if (null === $this->results) {
            $this->execute();
        }

        return parent::getResults();
    }

    public function getSortBy()
    {
        return parent::getSortBy() ? : array();
    }
    /**
     * {@inheritdoc}
     */
    public function execute(array $params = array(), $hydrationMode = null)
    {
        // Sort
        $this->getQueryBuilder()->getQuery()->setSort($this->getSortBy());

        // Limit & offset
        $this->results = $this->getQueryBuilder()->getFinder()->find($this->getQueryBuilder()->getQuery(), $this->getMaxResults(), array('limit' => $this->getMaxResults()));
//        $this->results = $this->getQueryBuilder()->getRepository()->createPaginatorAdapter(
//            $this->getQueryBuilder()->getQuery()
//        )->getResults(
//            $this->getFirstResult(),
//            $this->getMaxResults()
//        );

        return $this->getResults();
    }
}
