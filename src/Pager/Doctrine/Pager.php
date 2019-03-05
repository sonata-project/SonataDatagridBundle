<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DatagridBundle\Pager\Doctrine;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Sonata\DatagridBundle\Pager\BasePager;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery;
use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;

/**
 * @author Jonathan H. Wage <jonwage@gmail.com>
 */
final class Pager extends BasePager
{
    public function computeNbResult(): int
    {
        $countQuery = clone $this->getQuery();

        \assert($countQuery instanceof ProxyQueryInterface);

        if (\count($this->getParameters()) > 0) {
            $countQuery->setParameters($this->getParameters());
        }

        if ($countQuery->getQueryBuilder()->getDQLPart('orderBy')) {
            $countQuery->getQueryBuilder()->resetDQLPart('orderBy');
        }

        $countQuery->select(sprintf(
            'count(DISTINCT %s.%s) as cnt',
            current($countQuery->getRootAliases()),
            current($this->getCountColumn())
        ));

        return $countQuery->getQuery()->getSingleScalarResult();
    }

    public function getResults($hydrationMode = Query::HYDRATE_OBJECT): ?array
    {
        return $this->getQuery()->execute([], $hydrationMode);
    }

    public function init(): void
    {
        $this->resetIterator();

        $this->setNbResults($this->computeNbResult());

        $query = $this->getQuery();

        \assert($query instanceof ProxyQueryInterface);

        $query->setFirstResult(null);
        $query->setMaxResults(null);

        if (\count($this->getParameters()) > 0) {
            $query->setParameters($this->getParameters());
        }

        if (0 === $this->getPage() || 0 === $this->getMaxPerPage() || 0 === $this->getNbResults()) {
            $this->setLastPage(1);
        } else {
            $offset = ($this->getPage() - 1) * $this->getMaxPerPage();

            $this->setLastPage((int) ceil($this->getNbResults() / $this->getMaxPerPage()));

            $query->setFirstResult($offset);
            $query->setMaxResults($this->getMaxPerPage());
        }
    }

    /**
     * Builds a pager for a given query builder.
     */
    public static function create(QueryBuilder $builder, int $limit, int $page): PagerInterface
    {
        $pager = new self($limit);
        $pager->setQuery(new ProxyQuery($builder));
        $pager->setPage($page);
        $pager->init();

        return $pager;
    }
}
