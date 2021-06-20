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

namespace Sonata\DatagridBundle\Datagrid;

use Sonata\DatagridBundle\Filter\FilterInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @phpstan-template T of ProxyQueryInterface
 */
interface DatagridInterface
{
    public const SORT_ORDER = '_sort_order';
    public const SORT_BY = '_sort_by';
    public const PAGE = '_page';
    public const PER_PAGE = '_per_page';

    /**
     * @phpstan-return PagerInterface<T>
     */
    public function getPager(): PagerInterface;

    /**
     * @phpstan-return T
     */
    public function getQuery(): ProxyQueryInterface;

    /**
     * NEXT_MAJOR: Change return type to  `iterable<object>`
     *
     * @return array<object>|null
     */
    public function getResults(): ?array;

    public function buildPager(): void;

    public function addFilter(FilterInterface $filter): void;

    /**
     * @return array<string, FilterInterface>
     */
    public function getFilters(): array;

    /**
     * @param string[] $keys
     */
    public function reorderFilters(array $keys): void;

    /**
     * @return array<string, mixed>
     */
    public function getValues(): array;

    /**
     * @param mixed $value
     */
    public function setValue(string $name, string $operator, $value): void;

    public function getForm(): FormInterface;

    public function getFilter(string $name): ?FilterInterface;

    public function hasFilter(string $name): bool;

    public function removeFilter(string $name): void;

    public function hasActiveFilters(): bool;
}
