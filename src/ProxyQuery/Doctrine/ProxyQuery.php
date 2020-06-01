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

namespace Sonata\DatagridBundle\ProxyQuery\Doctrine;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Sonata\DatagridBundle\Mapping\AssociationMappingInterface;
use Sonata\DatagridBundle\ProxyQuery\BaseProxyQuery;

/**
 * TODO:
 * This bundle should not rely on Doctrine\ORM. You should use the DoctrineORMAdminBundle ProxyQuery
 * instead because multiple bugfix/new feature was not added to the DatagridBundle ProxyQuery but
 * the DoctrineORMAdminBundle require SonataAdminBundle which lead to a lot of requirements for nothing.
 * We should split DoctrineORMAdminBundle to provide a DoctrineORMDatagridBundle.
 *
 * @deprecated prefer using the DoctrineORMAdminBundle ProxyQuery if possible
 */
final class ProxyQuery extends BaseProxyQuery
{
    /**
     * @var QueryBuilder
     */
    private $queryBuilder;

    /**
     * @var array
     */
    private $entityJoinAliases = [];

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

    /**
     * @param AssociationMappingInterface[] $associationMappings
     */
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

                    if (sprintf('%s.%s', $alias, $associationMapping->getFieldName()) === $joinExpr->getJoin()) {
                        $this->entityJoinAliases[] = $newAliasTmp;
                        $alias = $newAliasTmp;

                        continue 3;
                    }
                }
            }

            $newAlias .= '_'.$associationMapping->getFieldName();
            if (!\in_array($newAlias, $this->entityJoinAliases, true)) {
                $this->entityJoinAliases[] = $newAlias;
                $this->queryBuilder->leftJoin(sprintf('%s.%s', $alias, $associationMapping->getFieldName()), $newAlias);
            }

            $alias = $newAlias;
        }

        return $alias;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function execute(array $params = [], ?int $hydrationMode = null)
    {
        $this->queryBuilder->setMaxResults($this->getMaxResults());
        $this->queryBuilder->setFirstResult($this->getFirstResult());

        $sortBy = $this->getSortBy();
        $sortOrder = $this->getSortOrder();

        if ($sortBy && $sortOrder) {
            $alias = $this->entityJoin($sortBy->getParentAssociationMappings());
            $sortBy = $alias.'.'.$sortBy->getFieldMapping()->getFieldName();

            $rootAliases = $this->queryBuilder->getRootAliases();
            $rootAlias = $rootAliases[0];
            $sortBy = sprintf('%s.%s', $rootAlias, $sortBy);

            $this->queryBuilder->orderBy($sortBy, $sortOrder);
        }

        return $this->getFixedQueryBuilder($this->queryBuilder)->getQuery()->execute($params, $hydrationMode);
    }

    /**
     * Generates new QueryBuilder for Postgresql or Oracle if necessary.
     */
    private function preserveSqlOrdering(QueryBuilder $queryBuilder): QueryBuilder
    {
        $rootAliases = $queryBuilder->getRootAliases();
        $rootAlias = $rootAliases[0];

        // for SELECT DISTINCT, ORDER BY expressions must appear in select list
        // Consider SELECT DISTINCT x FROM tab ORDER BY y;
        // For any particular x-value in the table there might be many different y
        // values. Which one will you use to sort that x-value in the output?

        // todo : check how doctrine behave, potential SQL injection here ...
        if ($this->getSortBy()) {
            $alias = $this->entityJoin($this->getSortBy()->getParentAssociationMappings());
            $sortBy = $alias.'.'.$this->getSortBy()->getFieldMapping()->getFieldName();

            if (false === strpos($sortBy, '.')) {
                // add the current alias
                $sortBy = $rootAlias.'.'.$sortBy;
            }
            $sortBy .= ' AS __order_by';
            $queryBuilder->addSelect($sortBy);
        }

        // For any orderBy clause defined directly in the dqlParts
        $dqlParts = $queryBuilder->getDQLParts();
        if ($dqlParts['orderBy'] && \count($dqlParts['orderBy'])) {
            $sqlOrderColumns = [];
            foreach ($dqlParts['orderBy'] as $part) {
                foreach ($part->getParts() as $orderBy) {
                    $sqlOrderColumns[] = preg_replace("/\s+(ASC|DESC)$/i", '', $orderBy);
                }
            }
            $queryBuilder->addSelect(implode(', ', $sqlOrderColumns));
        }

        return $queryBuilder;
    }

    /**
     * This method alters the query to return a clean set of object with a working
     * set of Object.
     */
    private function getFixedQueryBuilder(QueryBuilder $queryBuilder): QueryBuilder
    {
        $queryBuilderId = clone $queryBuilder;

        // step 1 : retrieve the targeted class
        $from = $queryBuilderId->getDQLPart('from');
        $class = $from[0]->getFrom();

        // step 2 : retrieve the column id
        $idName = current($queryBuilderId->getEntityManager()->getMetadataFactory()->getMetadataFor($class)->getIdentifierFieldNames());

        // step 3 : retrieve the different subjects id
        $rootAliases = $queryBuilderId->getRootAliases();
        $rootAlias = $rootAliases[0];
        $select = sprintf('%s.%s', $rootAlias, $idName);
        $queryBuilderId->resetDQLPart('select');
        $queryBuilderId->add('select', 'DISTINCT '.$select);
        $queryBuilderId = $this->preserveSqlOrdering($queryBuilderId);

        $results = $queryBuilderId->getQuery()->execute([], Query::HYDRATE_ARRAY);

        $idx = [];
        $connection = $queryBuilder->getEntityManager()->getConnection();
        foreach ($results as $id) {
            $idx[] = $connection->quote($id[$idName]);
        }

        // step 4 : alter the query to match the targeted ids
        if (\count($idx) > 0) {
            $queryBuilder->andWhere(sprintf('%s IN (%s)', $select, implode(',', $idx)));
            $queryBuilder->setMaxResults(null);
            $queryBuilder->setFirstResult(null);
        }

        return $queryBuilder;
    }
}
