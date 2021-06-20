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

/**
 * Interface FilterInterface
 *
 * @method array getOptions()
 * @method mixed getFieldOption(string $name, $default = null)
 * @method void  setFieldOption(string $name, $value)
 */
interface FilterInterface
{
    public const CONDITION_OR = 'OR';

    public const CONDITION_AND = 'AND';

    /**
     * NEXT_MAJOR: Change $value typehint to FilterData.
     *
     * @param ProxyQueryInterface $query
     * @param array               $value
     */
    public function apply($query, $value): void;

    /**
     * NEXT_MAJOR: Remove this method from the interface.
     */
    public function filter(ProxyQueryInterface $queryBuilder, string $alias, string $field, string $value): void;

    /**
     * NEXT_MAJOR: Remove null from the return type.
     */
    public function getName(): ?string;

    public function getFormName(): string;

    /**
     * @return string|false|null
     */
    public function getLabel();

    /**
     * @param string|false|null $label
     */
    public function setLabel($label): void;

    /**
     * @return array<string, mixed>
     */
    public function getDefaultOptions(): array;

    /**
     * NEXT_MAJOR: Uncomment this.
     *
     * @return array<string, mixed>
     */
    //public function getOptions(): array;

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOption(string $name, $default = null);

    /**
     * @param mixed $value
     */
    public function setOption(string $name, $value): void;

    /**
     * @param array<string, mixed> $options
     */
    public function initialize(string $name, array $options = []): void;

    public function getFieldName(): string;

    /**
     * @return array<string, mixed>
     */
    public function getFieldOptions(): array;

    /**
     * NEXT_MAJOR: Uncomment this.
     *
     * @param mixed $default
     *
     * @return mixed
     */
    //public function getFieldOption(string $name, $default = null);

    /**
     * NEXT_MAJOR: Uncomment this.
     *
     * @param mixed $value
     */
    //public function setFieldOption(string $name, $value): void;

    public function getFieldType(): string;

    /**
     * Returns the main widget used to render the filter.
     *
     * @return array{0: string, 1: array<string, mixed>}
     */
    public function getRenderSettings(): array;

    public function isActive(): bool;

    /**
     * Set the condition to use with the left side of the query : OR or AND.
     */
    public function setCondition(string $condition): void;

    public function getCondition(): string;

    public function getTranslationDomain(): string;
}
