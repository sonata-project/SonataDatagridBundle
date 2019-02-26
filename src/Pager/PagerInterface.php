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

interface PagerInterface extends \Iterator, \Countable, \Serializable
{
    /**
     * Initialize the Pager.
     */
    public function init(): void;

    public function getMaxPerPage(): int;

    public function setMaxPerPage(int $max): void;

    public function setPage(int $page): void;

    public function setQuery(ProxyQueryInterface $query): void;

    public function getResults(): ?array;

    /**
     * Returns the first page number.
     */
    public function getFirstPage(): int;

    /**
     * Returns the last page number.
     */
    public function getLastPage(): int;

    public function getPage(): int;

    public function getNextPage(): int;

    public function getPreviousPage(): int;

    public function getCurrentMaxLink(): int;

    public function getMaxRecordLimit(): int;

    public function setMaxRecordLimit(int $limit): void;

    /**
     * Returns an array of page numbers to use in pagination links.
     */
    public function getLinks(?int $nbLinks = null): array;

    /**
     * Returns true if the current query requires pagination.
     */
    public function haveToPaginate(): bool;

    public function getCursor(): int;

    public function setCursor(int $pos): void;

    public function getObjectByCursor(int $pos): ?object;

    /**
     * Returns the current object.
     */
    public function getCurrent(): ?object;

    /**
     * Returns the next object.
     */
    public function getNext(): ?object;

    /**
     * Returns the previous object.
     */
    public function getPrevious(): ?object;

    /**
     * Returns the first index on the current page.
     */
    public function getFirstIndice(): int;

    /**
     * Returns the last index on the current page.
     */
    public function getLastIndice(): int;

    public function getNbResults(): int;

    /**
     * Returns the maximum number of page numbers.
     */
    public function getMaxPageLinks(): int;

    /**
     * Sets the maximum number of page numbers.
     */
    public function setMaxPageLinks(int $maxPageLinks): void;

    public function isFirstPage(): bool;

    public function isLastPage(): bool;

    /**
     * Returns the current pager's parameter holder.
     */
    public function getParameters(): array;

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function getParameter(string $name, $default = null);

    public function hasParameter(string $name): bool;

    public function setParameter(string $name, $value): void;

    public function getCountColumn(): array;

    public function setCountColumn(array $countColumn): array;

    public function getQuery(): ?ProxyQueryInterface;
}
