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
     * @param string $name
     * @param array  $args
     *
     * @return mixed
     */
    public function __call($name, $args);

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

    public function setFirstResult(int $firstResult): self;

    /**
     * @return mixed
     */
    public function getFirstResult();

    public function setMaxResults(int $maxResults): self;

    /**
     * @return mixed
     */
    public function getMaxResults();

    public function getResults(): array;
}
