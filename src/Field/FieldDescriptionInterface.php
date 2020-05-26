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

namespace Sonata\DatagridBundle\Field;

use Sonata\DatagridBundle\Exception\NoValueException;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
interface FieldDescriptionInterface
{
    public function setFieldName(string $fieldName): void;

    public function getFieldName(): ?string;

    /**
     * Set the name, used as a form label or table header.
     */
    public function setName(string $name): void;

    public function getName(): ?string;

    /**
     * Set the template, used to render the field.
     */
    public function setTemplate(string $template): void;

    public function getTemplate(): ?string;

    /**
     * Set the type, this is a mandatory field as it used to select the correct template
     * or the logic associated to the current FieldDescription object.
     *
     * This can be a string (text, array, datetime, boolean) or an int (ClassMetadata::ONE_TO_MANY)
     *
     * @param int|string $type
     */
    public function setType($type): void;

    /**
     * @return int|string|null
     */
    public function getType();

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
     * Define the options value, if the options array contains the reserved keywords
     *   - type
     *   - template.
     *
     * Then the value are copied across to the related property value
     */
    public function setOptions(array $options): void;

    public function getOptions(): array;

    /**
     * @throws \RuntimeException
     */
    public function mergeOption(string $name, array $options = []): void;

    public function mergeOptions(array $options = []): void;

    public function setAssociationMapping(array $associationMapping): void;

    public function getAssociationMapping(): array;

    public function getTargetModel(): ?string;

    public function setFieldMapping(array $fieldMapping): void;

    public function getFieldMapping(): array;

    public function setParentAssociationMappings(array $parentAssociationMappings): void;

    public function getParentAssociationMappings(): array;

    /**
     * Set the original mapping type (only used if the field is linked to an entity).
     *
     * This can be a string (text, array, datetime, boolean) or an int (ClassMetadata::ONE_TO_MANY)
     *
     * @param int|string $mappingType
     */
    public function setMappingType($mappingType): void;

    /**
     * @return int|string|null
     */
    public function getMappingType();

    public function isIdentifier(): bool;

    /**
     * @throws NoValueException
     *
     * @return mixed
     */
    public function getValue(?object $object);

    /**
     * @throws NoValueException
     *
     * @return mixed
     */
    public function getFieldValue(?object $object, string $fieldName);
}
