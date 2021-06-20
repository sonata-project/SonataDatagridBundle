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

namespace Sonata\DatagridBundle\Pager;

/**
 * @phpstan-template T of \Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface
 */
interface PageableInterface
{
    /**
     * @param array<string, mixed>  $criteria
     * @param array<string, string> $sort
     *
     * @phpstan-return PagerInterface<T>
     */
    public function getPager(
        array $criteria,
        int $page,
        int $limit = 10,
        array $sort = []
    ): PagerInterface;
}
