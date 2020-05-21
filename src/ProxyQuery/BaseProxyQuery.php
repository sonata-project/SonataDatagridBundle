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

use Doctrine\ORM\QueryBuilder;

abstract class BaseProxyQuery implements ProxyQueryInterface
{
    /**
     * @var QueryBuilder
     */
    protected $queryBuilder;

    /**
     * @var array
     */
    protected $results = [];

    /**
     * @var int
     */
    protected $uniqueParameterId = 0;

    /**
     * @var string[]
     */
    protected $entityJoinAliases = [];

    /**
     * @var string|null
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

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    public function __clone()
    {
        $this->queryBuilder = clone $this->queryBuilder;
    }

    public function __call(string $name, array $args)
    {
        return \call_user_func_array([$this->queryBuilder, $name], $args);
    }

    public function setSortBy(array $parentAssociationMappings, array $fieldMapping): ProxyQueryInterface
    {
        $alias = $this->entityJoin($parentAssociationMappings);
        $this->sortBy = $alias.'.'.$fieldMapping['fieldName'];

        return $this;
    }

    public function getSortBy(): ?string
    {
        return $this->sortBy;
    }

    public function setSortOrder(string $sortOrder): ProxyQueryInterface
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

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function getUniqueParameterId(): int
    {
        return $this->uniqueParameterId++;
    }

    public function entityJoin(array $associationMappings): string
    {
        $alias = current($this->queryBuilder->getRootAliases());

        $newAlias = 's';

        $joinedEntities = $this->queryBuilder->getDQLPart('join');

        foreach ($associationMappings as $associationMapping) {
            // Do not add left join to already joined entities with custom query
            foreach ($joinedEntities as $joinExprList) {
                foreach ($joinExprList as $joinExpr) {
                    $newAliasTmp = $joinExpr->getAlias();

                    if (sprintf('%s.%s', $alias, $associationMapping['fieldName']) === $joinExpr->getJoin()) {
                        $this->entityJoinAliases[] = $newAliasTmp;
                        $alias = $newAliasTmp;

                        continue 3;
                    }
                }
            }

            $newAlias .= '_'.$associationMapping['fieldName'];
            if (!\in_array($newAlias, $this->entityJoinAliases, true)) {
                $this->entityJoinAliases[] = $newAlias;
                $this->queryBuilder->leftJoin(sprintf('%s.%s', $alias, $associationMapping['fieldName']), $newAlias);
            }

            $alias = $newAlias;
        }

        return $alias;
    }
}
