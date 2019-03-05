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
     * @return mixed
     */
    public function __call(string $name, array $args);

    /**
     * @return mixed
     */
    public function execute(array $params = [], ?int $hydrationMode = null);

    /**
     * @param mixed $sortBy
     */
    public function setSortBy($sortBy): self;

    /**
     * @return mixed
     */
    public function getSortBy();

    /**
     * @param mixed $sortOrder
     */
    public function setSortOrder($sortOrder): self;

    /**
     * @return mixed
     */
    public function getSortOrder();

    public function setFirstResult(?int $firstResult): self;

    public function getFirstResult(): ?int;

    public function setMaxResults(?int $maxResults): self;

    public function getMaxResults(): ?int;

    public function getResults(): array;
}
