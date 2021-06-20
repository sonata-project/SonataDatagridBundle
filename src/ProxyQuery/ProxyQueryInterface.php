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

namespace Sonata\DatagridBundle\ProxyQuery;

/**
 * Interface used by the Datagrid to build the query.
 */
interface ProxyQueryInterface
{
    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @return mixed
     */
    public function __call(string $name, array $args);

    /**
     * @return array<object>|\Traversable<object>
     */
    public function execute();

    /**
     * NEXT_MAJOR: Make this method compatible with Sonata\AdminBundle\Datagrid\ProxyQueryInterface::setSortBy().
     *
     * @param mixed $sortBy
     */
    public function setSortBy($sortBy): self;

    /**
     * @return string|null
     */
    public function getSortBy();

    /**
     * @param string $sortOrder
     *
     * @return static
     */
    public function setSortOrder($sortOrder): self;

    /**
     * @return string|null
     */
    public function getSortOrder();

    /**
     * @return static
     */
    public function setFirstResult(?int $firstResult): self;

    public function getFirstResult(): ?int;

    /**
     * @return static
     */
    public function setMaxResults(?int $maxResults): self;

    public function getMaxResults(): ?int;

    /**
     * NEXT_MAJOR: Remove this method.
     */
    public function getResults(): array;
}
