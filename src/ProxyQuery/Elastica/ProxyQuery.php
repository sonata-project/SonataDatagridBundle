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

namespace Sonata\DatagridBundle\ProxyQuery\Elastica;

use Elastica\Search;
use Sonata\DatagridBundle\ProxyQuery\BaseProxyQuery;

final class ProxyQuery extends BaseProxyQuery
{
    public function execute(array $params = [], ?int $hydrationMode = null)
    {
        $query = $this->queryBuilder->getQuery();

        $sortBy = $this->getSortBy();
        $sortOrder = $this->getSortOrder();

        if ($sortBy && $sortOrder) {
            $query->setSort([$sortBy => ['order' => $sortOrder]]);
        }

        $this->results = $this->queryBuilder
            ->getRepository()
            ->createPaginatorAdapter(
                $query,
                [
                    Search::OPTION_SIZE => $this->getMaxResults(),
                    Search::OPTION_FROM => $this->getFirstResult(),
                ]
            );

        return $this->results->getResults($this->getFirstResult(), $this->getMaxResults())->toArray();
    }
}
