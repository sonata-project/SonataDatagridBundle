<?php

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

    /**
     * Returns the maximum number of results per page.
     *
     * @return int
     */
    public function getMaxPerPage(): int;

    /**
     * Sets the maximum number of results per page.
     *
     * @param int $max
     */
    public function setMaxPerPage(int $max): void;

    /**
     * Sets the current page.
     *
     * @param int $page
     */
    public function setPage(int $page): void;

    /**
     * Set query.
     *
     * @param ProxyQueryInterface $query
     */
    public function setQuery(ProxyQueryInterface $query): void;

    /**
     * Returns an array of results on the given page.
     *
     * @return array|null
     */
    public function getResults(): ?array;

    /**
     * Returns the first page number.
     *
     * @return int
     */
    public function getFirstPage(): int;

    /**
     * Returns the last page number.
     *
     * @return int
     */
    public function getLastPage(): int;

    /**
     * Returns the current page.
     *
     * @return int
     */
    public function getPage(): int;

    /**
     * Returns the next page.
     *
     * @return int
     */
    public function getNextPage(): int;

    /**
     * Returns the previous page.
     *
     * @return int
     */
    public function getPreviousPage(): int;

    /**
     * Returns the current pager's max link.
     *
     * @return int
     */
    public function getCurrentMaxLink(): int;

    /**
     * Returns the current pager's max record limit.
     *
     * @return int
     */
    public function getMaxRecordLimit(): int;

    /**
     * Sets the current pager's max record limit.
     *
     * @param int $limit
     */
    public function setMaxRecordLimit(int $limit): void;

    /**
     * Returns an array of page numbers to use in pagination links.
     *
     * @param int|null $nbLinks The maximum number of page numbers to return
     *
     * @return array
     */
    public function getLinks(?int $nbLinks = null): array;

    /**
     * Returns true if the current query requires pagination.
     *
     * @return bool
     */
    public function haveToPaginate(): bool;

    /**
     * Returns the current cursor.
     *
     * @return int
     */
    public function getCursor(): int;

    /**
     * Sets the current cursor.
     *
     * @param int $pos
     */
    public function setCursor(int $pos): void;

    /**
     * Returns an object by cursor position.
     *
     * @param int $pos
     *
     * @return mixed
     */
    public function getObjectByCursor(int $pos);

    /**
     * Returns the current object.
     *
     * @return mixed
     */
    public function getCurrent();

    /**
     * Returns the next object.
     *
     * @return mixed|null
     */
    public function getNext();

    /**
     * Returns the previous object.
     *
     * @return mixed|null
     */
    public function getPrevious();

    /**
     * Returns the first index on the current page.
     *
     * @return int
     */
    public function getFirstIndice(): int;

    /**
     * Returns the last index on the current page.
     *
     * @return int
     */
    public function getLastIndice(): int;

    /**
     * Returns the number of results.
     *
     * @return int
     */
    public function getNbResults(): int;

    /**
     * Returns the maximum number of page numbers.
     *
     * @return int
     */
    public function getMaxPageLinks(): int;

    /**
     * Sets the maximum number of page numbers.
     *
     * @param int $maxPageLinks
     */
    public function setMaxPageLinks(int $maxPageLinks): void;

    /**
     * Returns true if on the first page.
     *
     * @return bool
     */
    public function isFirstPage(): bool;

    /**
     * Returns true if on the last page.
     *
     * @return bool
     */
    public function isLastPage(): bool;

    /**
     * Returns the current pager's parameter holder.
     *
     * @return array
     */
    public function getParameters(): array;

    /**
     * Returns a parameter.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getParameter(string $name, $default = null);

    /**
     * Checks whether a parameter has been set.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter(string $name): bool;

    /**
     * Sets a parameter.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setParameter(string $name, $value): void;

    /**
     * @return array
     */
    public function getCountColumn(): array;

    /**
     * @param array $countColumn
     *
     * @return array
     */
    public function setCountColumn(array $countColumn): array;

    /**
     * @return ProxyQueryInterface|null
     */
    public function getQuery(): ?ProxyQueryInterface;
}
