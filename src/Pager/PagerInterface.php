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

use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;

/**
 * NEXT_MAJOR: Remove the extends \Iterator, \Countable, \Serializable
 *
 * @phpstan-template T of \Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface
 *
 * @method int      countResults
 * @method iterable getCurrentPageResults
 */
interface PagerInterface extends \Iterator, \Countable, \Serializable
{
    /**
     * Initialize the Pager.
     */
    public function init(): void;

    public function getMaxPerPage(): int;

    public function setMaxPerPage(int $max): void;

    public function getPage(): int;

    public function setPage(int $page): void;

    public function getNextPage(): int;

    public function getPreviousPage(): int;

    public function getFirstPage(): int;

    public function isFirstPage(): bool;

    public function getLastPage(): int;

    public function isLastPage(): bool;

    /**
     * @phpstan-return T|null
     */
    public function getQuery(): ?ProxyQueryInterface;

    /**
     * @phpstan-param T $query
     */
    public function setQuery(ProxyQueryInterface $query): void;

    public function haveToPaginate(): bool;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @return array<T>
     */
    public function getResults(): ?array;

    /**
     * NEXT_MAJOR: Uncomment this.
     *
     * Returns a collection of results on the given page.
     *
     * @return iterable<object>
     */
    //public function getCurrentPageResults(): iterable;

    // NEXT_MAJOR: Uncomment this.
    //public function countResults(): int;

    /**
     * Returns an array of page numbers to use in pagination links.
     *
     * @return int[]
     */
    public function getLinks(?int $nbLinks = null): array;

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function setMaxRecordLimit(int $limit): void;

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getMaxRecordLimit(): int;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getCurrentMaxLink(): int;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getCursor(): int;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function setCursor(int $pos): void;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getObjectByCursor(int $pos): ?object;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getCurrent(): ?object;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getNext(): ?object;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getPrevious(): ?object;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getFirstIndice(): int;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getLastIndice(): int;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getNbResults(): int;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getMaxPageLinks(): int;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function setMaxPageLinks(int $maxPageLinks): void;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @return array<string, mixed>
     */
    public function getParameters(): array;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function getParameter(string $name, $default = null);

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function hasParameter(string $name): bool;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @param mixed $value
     */
    public function setParameter(string $name, $value): void;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @return string[]
     */
    public function getCountColumn(): array;

    /**
     * NEXT_MAJOR: Remove this method
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @param string[] $countColumn
     *
     * @return string[]
     */
    public function setCountColumn(array $countColumn): array;
}
