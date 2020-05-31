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

use Sonata\DatagridBundle\Field\FieldDescriptionInterface;

abstract class BaseProxyQuery implements ProxyQueryInterface
{
    /**
     * @var array
     */
    protected $results = [];

    /**
     * @var FieldDescriptionInterface|null
     */
    private $sortBy;

    /**
     * @var string|null
     */
    private $sortOrder;

    /**
     * @var int|null
     */
    private $firstResult;

    /**
     * @var int|null
     */
    private $maxResults;

    public function setSortBy(?FieldDescriptionInterface $sortBy): ProxyQueryInterface
    {
        $this->sortBy = $sortBy;

        return $this;
    }

    public function getSortBy(): ?FieldDescriptionInterface
    {
        return $this->sortBy;
    }

    public function setSortOrder(?string $sortOrder): ProxyQueryInterface
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    public function getSortOrder(): ?string
    {
        return $this->sortOrder;
    }

    public function setFirstResult(?int $firstResult): ProxyQueryInterface
    {
        $this->firstResult = $firstResult;

        return $this;
    }

    public function getFirstResult(): ?int
    {
        return $this->firstResult;
    }

    public function setMaxResults(?int $maxResults): ProxyQueryInterface
    {
        $this->maxResults = $maxResults;

        return $this;
    }

    public function getMaxResults(): ?int
    {
        return $this->maxResults;
    }

    public function getResults(): array
    {
        return $this->results;
    }
}
