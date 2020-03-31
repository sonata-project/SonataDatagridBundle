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
 * @template-covariant T of object
 */
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

    /**
     * @return array<T>
     */
    public function getResults(): ?array;

    public function getFirstPage(): int;

    public function getLastPage(): int;

    public function getPage(): int;

    public function getNextPage(): int;

    public function getPreviousPage(): int;

    public function getCurrentMaxLink(): int;

    public function getMaxRecordLimit(): int;

    public function setMaxRecordLimit(int $limit): void;

    /**
     * Returns an array of page numbers to use in pagination links.
     *
     * @return int[]
     */
    public function getLinks(?int $nbLinks = null): array;

    public function haveToPaginate(): bool;

    public function getCursor(): int;

    public function setCursor(int $pos): void;

    /**
     * @return T|null
     */
    public function getObjectByCursor(int $pos): ?object;

    /**
     * @return T|null
     */
    public function getCurrent(): ?object;

    /**
     * @return T|null
     */
    public function getNext(): ?object;

    /**
     * @return T|null
     */
    public function getPrevious(): ?object;

    public function getFirstIndice(): int;

    public function getLastIndice(): int;

    public function getNbResults(): int;

    public function getMaxPageLinks(): int;

    public function setMaxPageLinks(int $maxPageLinks): void;

    public function isFirstPage(): bool;

    public function isLastPage(): bool;

    /**
     * @return array<string, mixed>
     */
    public function getParameters(): array;

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function getParameter(string $name, $default = null);

    public function hasParameter(string $name): bool;

    /**
     * @param mixed $value
     */
    public function setParameter(string $name, $value): void;

    /**
     * @return string[]
     */
    public function getCountColumn(): array;

    /**
     * @param string[] $countColumn
     *
     * @return string[]
     */
    public function setCountColumn(array $countColumn): array;

    public function getQuery(): ?ProxyQueryInterface;
}
