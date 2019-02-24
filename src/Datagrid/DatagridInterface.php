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

interface DatagridInterface
{
    /**
     * @return PagerInterface
     */
    public function getPager(): PagerInterface;

    /**
     * @return ProxyQueryInterface
     */
    public function getQuery(): ProxyQueryInterface;

    /**
     * @return array|null
     */
    public function getResults(): ?array;

    public function buildPager(): void;

    public function addFilter(FilterInterface $filter): void;

    /**
     * @return array
     */
    public function getFilters(): array;

    /**
     * Reorder filters.
     *
     * @param array $keys
     */
    public function reorderFilters(array $keys): void;

    /**
     * @return array
     */
    public function getValues(): array;

    /**
     * @param string $name
     * @param string $operator
     * @param mixed  $value
     */
    public function setValue(string $name, string $operator, $value): void;

    /**
     * @return FormInterface
     */
    public function getForm(): FormInterface;

    /**
     * @param string $name
     *
     * @return FilterInterface|null
     */
    public function getFilter(string $name): ?FilterInterface;

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasFilter(string $name): bool;

    /**
     * @param string $name
     */
    public function removeFilter(string $name): void;

    /**
     * @return bool
     */
    public function hasActiveFilters(): bool;
}
