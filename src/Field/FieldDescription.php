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

use Doctrine\Inflector\InflectorFactory;
use Sonata\DatagridBundle\Exception\NoValueException;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
class FieldDescription implements FieldDescriptionInterface
{
    /**
     * @var string|null the field name
     */
    protected $name;

    /**
     * @var string|null the field name (of the form)
     */
    protected $fieldName;

    /**
     * @var string|int|null the type
     */
    protected $type;

    /**
     * @var string|int|null the original mapping type
     */
    protected $mappingType;

    /**
     * @var array the ORM association mapping
     */
    protected $associationMapping = [];

    /**
     * @var array the ORM field information
     */
    protected $fieldMapping = [];

    /**
     * @var array the ORM parent mapping association
     */
    protected $parentAssociationMappings = [];

    /**
     * @var string the template name
     */
    protected $template;

    /**
     * @var array the option collection
     */
    protected $options = [];

    /**
     * @var array[] cached object field getters
     */
    private static $fieldGetters = [];

    public function setFieldName(string $fieldName): void
    {
        $this->fieldName = $fieldName;
    }

    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }

    public function setName(string $name): void
    {
        $this->name = $name;

        if (!$this->getFieldName()) {
            $this->setFieldName(substr(strrchr('.'.$name, '.'), 1));
        }
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    public function getTemplate(): ?string
    {
        return $this->template;
    }

    /**
     * @param int|string $type
     */
    public function setType($type): void
    {
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOption(string $name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    /**
     * @param mixed $value
     */
    public function setOption(string $name, $value): void
    {
        $this->options[$name] = $value;
    }

    public function setOptions(array $options): void
    {
        // set the type if provided
        if (isset($options['type'])) {
            $this->setType($options['type']);
            unset($options['type']);
        }

        // set the template if provided
        if (isset($options['template'])) {
            $this->setTemplate($options['template']);
            unset($options['template']);
        }

        $this->options = $options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function mergeOption(string $name, array $options = []): void
    {
        if (!isset($this->options[$name])) {
            $this->options[$name] = [];
        }

        if (!\is_array($this->options[$name])) {
            throw new \RuntimeException(sprintf('The key `%s` does not point to an array value', $name));
        }

        $this->options[$name] = array_merge($this->options[$name], $options);
    }

    public function mergeOptions(array $options = []): void
    {
        $this->setOptions(array_merge_recursive($this->options, $options));
    }

    public function getTargetEntity(): ?string
    {
        return $this->associationMapping ? $this->associationMapping['targetEntity'] : null;
    }

    public function setAssociationMapping(array $associationMapping): void
    {
        $this->associationMapping = $associationMapping;

        $this->type = $this->type ?: $associationMapping['type'];
        $this->mappingType = $this->mappingType ?: $associationMapping['type'];
        $this->fieldName = $associationMapping['fieldName'];
    }

    public function getAssociationMapping(): array
    {
        return $this->associationMapping;
    }

    public function setFieldMapping(array $fieldMapping): void
    {
        $this->fieldMapping = $fieldMapping;

        $this->type = $this->type ?: $fieldMapping['type'];
        $this->mappingType = $this->mappingType ?: $fieldMapping['type'];
        $this->fieldName = $this->fieldName ?: $fieldMapping['fieldName'];
    }

    public function getFieldMapping(): array
    {
        return $this->fieldMapping;
    }

    /**
     * @throws \RuntimeException
     */
    public function setParentAssociationMappings(array $parentAssociationMappings): void
    {
        foreach ($parentAssociationMappings as $parentAssociationMapping) {
            if (!\is_array($parentAssociationMapping)) {
                throw new \RuntimeException('An association mapping must be an array');
            }
        }

        $this->parentAssociationMappings = $parentAssociationMappings;
    }

    public function getParentAssociationMappings(): array
    {
        return $this->parentAssociationMappings;
    }

    /**
     * @param int|string $mappingType
     */
    public function setMappingType($mappingType): void
    {
        $this->mappingType = $mappingType;
    }

    public function getMappingType()
    {
        return $this->mappingType;
    }

    public function isIdentifier(): bool
    {
        return isset($this->fieldMapping['id']) ? $this->fieldMapping['id'] : false;
    }

    public function isVirtual(): bool
    {
        return false !== $this->getOption('virtual_field', false);
    }

    /**
     * @throws NoValueException
     *
     * @return mixed
     */
    public function getFieldValue(?object $object, ?string $fieldName)
    {
        if ($this->isVirtual() || null === $object) {
            return null;
        }

        $getters = [];
        $parameters = [];

        // prefer method name given in the code option
        if ($this->getOption('code')) {
            $getters[] = $this->getOption('code');
        }
        // parameters for the method given in the code option
        if ($this->getOption('parameters')) {
            $parameters = $this->getOption('parameters');
        }

        if (null !== $fieldName && '' !== $fieldName) {
            if ($this->hasCachedFieldGetter($object, $fieldName)) {
                return $this->callCachedGetter($object, $fieldName, $parameters);
            }

            $camelizedFieldName = InflectorFactory::create()->build()->classify($fieldName);

            $getters[] = 'get'.$camelizedFieldName;
            $getters[] = 'is'.$camelizedFieldName;
            $getters[] = 'has'.$camelizedFieldName;
        }

        foreach ($getters as $getter) {
            if (method_exists($object, $getter) && \is_callable([$object, $getter])) {
                $this->cacheFieldGetter($object, $fieldName, 'getter', $getter);

                return $object->{$getter}(...$parameters);
            }
        }

        if (null !== $fieldName) {
            if (method_exists($object, '__call')) {
                $this->cacheFieldGetter($object, $fieldName, 'call');

                return $object->{$fieldName}(...$parameters);
            }

            if ('' !== $fieldName && isset($object->{$fieldName})) {
                $this->cacheFieldGetter($object, $fieldName, 'var');

                return $object->{$fieldName};
            }
        }

        throw new NoValueException(sprintf(
            'Neither the property "%s" nor one of the methods "%s()" exist and have public access in class "%s".',
            $this->getName(),
            implode('()", "', $getters),
            \get_class($object)
        ));
    }

    /**
     * @throws NoValueException
     *
     * @return mixed
     */
    public function getValue(?object $object)
    {
        foreach ($this->parentAssociationMappings as $parentAssociationMapping) {
            $object = $this->getFieldValue($object, $parentAssociationMapping['fieldName']);
        }

        $fieldMapping = $this->getFieldMapping();
        // Support embedded object for mapping
        // Ref: https://www.doctrine-project.org/projects/doctrine-orm/en/latest/tutorials/embeddables.html
        if (isset($fieldMapping['declaredField'])) {
            $parentFields = explode('.', $fieldMapping['declaredField']);
            foreach ($parentFields as $parentField) {
                $object = $this->getFieldValue($object, $parentField);
            }
        }

        return $this->getFieldValue($object, $this->fieldName);
    }

    private function getFieldGetterKey(object $object, ?string $fieldName): ?string
    {
        if (null === $fieldName) {
            return null;
        }

        $components = [\get_class($object), $fieldName];

        $code = $this->getOption('code');
        if (\is_string($code) && '' !== $code) {
            $components[] = $code;
        }

        return implode('-', $components);
    }

    private function hasCachedFieldGetter(object $object, string $fieldName): bool
    {
        return isset(
            self::$fieldGetters[$this->getFieldGetterKey($object, $fieldName)]
        );
    }

    private function callCachedGetter(object $object, string $fieldName, array $parameters = [])
    {
        $getterKey = $this->getFieldGetterKey($object, $fieldName);

        if ('getter' === self::$fieldGetters[$getterKey]['method']) {
            return $object->{self::$fieldGetters[$getterKey]['getter']}(...$parameters);
        }

        if ('call' === self::$fieldGetters[$getterKey]['method']) {
            return $object->__call($fieldName, $parameters);
        }

        return $object->{$fieldName};
    }

    private function cacheFieldGetter(object $object, ?string $fieldName, string $method, ?string $getter = null): void
    {
        $getterKey = $this->getFieldGetterKey($object, $fieldName);

        if (null !== $getterKey) {
            self::$fieldGetters[$getterKey] = ['method' => $method];
            if (null !== $getter) {
                self::$fieldGetters[$getterKey]['getter'] = $getter;
            }
        }
    }
}
