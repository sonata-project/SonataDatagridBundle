<?php

/*
 * This file is part of the symfony package.
 * (c) Fabien Potencier <fabien.potencier@symfony-project.com>
 * (c) Jonathan H. Wage <jonwage@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DatagridBundle\ProxyQuery\Doctrine;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Sonata\DatagridBundle\ProxyQuery\BaseProxyQuery;
use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;

/**
 * Class ProxyQuery
 *
 * This is the Doctrine proxy query class
 */
class ProxyQuery extends BaseProxyQuery implements ProxyQueryInterface
{
    /**
     * Constructor
     *
     * @param QueryBuilder $queryBuilder A query builder object
     */
    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(array $params = array(), $hydrationMode = null)
    {
        // Sort
        $this->getQueryBuilder()->orderBy($this->getSortBy());

        // Limit & offset
        $this->getQueryBuilder()->setFirstResult($this->getFirstResult());
        $this->getQueryBuilder()->setMaxResults($this->getMaxResults());

        return $this->getQueryBuilder()->getQuery()->execute();
    }
}
