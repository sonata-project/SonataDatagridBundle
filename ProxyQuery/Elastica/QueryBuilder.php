<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\DatagridBundle\ProxyQuery\Elastica;

use Elastica\Query;
use FOS\ElasticaBundle\Finder\FinderInterface;
use FOS\ElasticaBundle\Repository;


/**
 * Class QueryBuilder
 *
 * @package Sonata\DatagridBundle\ProxyQuery\Elastica
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class QueryBuilder
{
    /**
     * @var Query
     */
    private $query;

    /**
     * @var FinderInterface
     */
    private $finder;

    /**
     * @param Query           $query
     * @param FinderInterface $finder
     */
    public function __construct(Query $query, FinderInterface $finder)
    {
        $this->query  = $query;
        $this->finder = $finder;
    }

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * @return FinderInterface
     */
    public function getFinder()
    {
        return $this->finder;
    }
}