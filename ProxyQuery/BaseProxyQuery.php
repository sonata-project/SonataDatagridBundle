<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DatagridBundle\ProxyQuery;

use Doctrine\ORM\QueryBuilder;
use Sonata\ProductBundle\Search\Provider\SearchProviderInterface;

/**
 * This class try to unify the query usage between different models
 */
class BaseProxyQuery
{
    /**
     * @var QueryBuilder|SearchProviderInterface
     */
    protected $queryBuilder;

    /**
     * @var array
     */
    protected $sortBy;

    /**
     * @var array
     */
    protected $sortOrder;

    /**
     * @var integer
     */
    protected $firstResult;

    /**
     * @var integer
     */
    protected $maxResults;

    /**
     * @var array
     */
    protected $results;

    /**
     * @param $sortBy
     *
     * @return BaseProxyQuery
     */
    public function setSortBy($sortBy)
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    /**
     * @return array
     */
    public function getSortBy()
    {
        return $this->sortBy;
    }

    /**
     * @param $sortOrder
     *
     * @return BaseProxyQuery
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * @return array
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param $firstResult
     *
     * @return BaseProxyQuery
     */
    public function setFirstResult($firstResult)
    {
        $this->firstResult = $firstResult;

        return $this;
    }

    /**
     * @return int
     */
    public function getFirstResult()
    {
        return $this->firstResult;
    }

    /**
     * @param $maxResults
     *
     * @return BaseProxyQuery
     */
    public function setMaxResults($maxResults)
    {
        $this->maxResults = $maxResults;

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxResults()
    {
        return $this->maxResults;
    }

    /**
     * @return mixed
     */
    public function getQueryBuilder()
    {
        return $this->queryBuilder;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    public function __clone()
    {
        $this->queryBuilder = clone $this->queryBuilder;
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->queryBuilder, $name), $args);
    }
}
