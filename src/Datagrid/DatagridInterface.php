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
    public function getPager(): PagerInterface;

    public function getQuery(): ProxyQueryInterface;

    public function getResults(): ?array;

    public function buildPager(): void;

    public function addFilter(FilterInterface $filter): void;

    public function getFilters(): array;

    public function reorderFilters(array $keys): void;

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
