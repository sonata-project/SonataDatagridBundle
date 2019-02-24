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

namespace Sonata\DatagridBundle\Filter;

use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;

interface FilterInterface
{
    public const CONDITION_OR = 'OR';

    public const CONDITION_AND = 'AND';

    /**
     * Apply the filter to the QueryBuilder instance.
     *
     * @param string $alias
     * @param string $field
     * @param string $value
     */
    public function filter(ProxyQueryInterface $queryBuilder, string  $alias, string $field, string $value): void;

    /**
     * @param mixed $query
     * @param mixed $value
     */
    public function apply($query, $value): void;

    /**
     * Returns the filter name.
     *
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Returns the filter form name.
     *
     * @return string
     */
    public function getFormName(): string;

    /**
     * Returns the label name.
     *
     * @return string|null
     */
    public function getLabel(): ?string;

    /**
     * @param string $label
     */
    public function setLabel(string $label): void;

    /**
     * @return array
     */
    public function getDefaultOptions(): array;

    /**
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getOption(string $name, $default = null);

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setOption(string $name, $value): void;

    /**
     * @param string $name
     */
    public function initialize(string $name, array $options = []): void;

    /**
     * @return string
     */
    public function getFieldName(): string;

    /**
     * @return array
     */
    public function getFieldOptions(): array;

    /**
     * @return string
     */
    public function getFieldType(): string;

    /**
     * Returns the main widget used to render the filter.
     *
     * @return array
     */
    public function getRenderSettings(): array;

    /**
     * Returns true if filter is active.
     *
     * @return bool
     */
    public function isActive(): bool;

    /**
     * Set the condition to use with the left side of the query : OR or AND.
     *
     * @param string $condition
     */
    public function setCondition(string $condition): void;

    /**
     * @return string
     */
    public function getCondition(): string;

    /**
     * @return string
     */
    public function getTranslationDomain(): string;
}
