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
use Sonata\DatagridBundle\Mapping\AssociationMappingInterface;
use Sonata\DatagridBundle\Mapping\FieldMappingInterface;

/**
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
abstract class BaseFieldDescription implements FieldDescriptionInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string|null
     */
    private $type;

    /**
     * @var AssociationMappingInterface|null
     */
    private $associationMapping;

    /**
     * @var FieldMappingInterface|null
     */
    private $fieldMapping;

    /**
     * @var AssociationMappingInterface[]
     */
    private $parentAssociationMappings = [];

    /**
     * @var string
     */
    private $template;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var array[]
     */
    private static $fieldGetters = [];

    public function __construct(string $name)
    {
        $this->setName($name);
    }

    final public function setName(string $name): void
    {
        $this->name = $name;
    }

    final public function getName(): string
    {
        return $this->name;
    }

    final public function setTemplate(string $template): void
    {
        $this->template = $template;
    }

    final public function getTemplate(): ?string
    {
        return $this->template;
    }

    final public function setType(string $type): void
    {
        $this->type = $type;
    }

    final public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    final public function getOption(string $name, $default = null)
    {
        return isset($this->options[$name]) ? $this->options[$name] : $default;
    }

    /**
     * @param mixed $value
     */
    final public function setOption(string $name, $value): void
    {
        $this->options[$name] = $value;
    }

    final public function setOptions(array $options): void
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

    final public function getOptions(): array
    {
        return $this->options;
    }

    final public function mergeOption(string $name, array $options = []): void
    {
        if (!isset($this->options[$name])) {
            $this->options[$name] = [];
        }

        if (!\is_array($this->options[$name])) {
            throw new \RuntimeException(sprintf('The key `%s` does not point to an array value', $name));
        }

        $this->options[$name] = array_merge($this->options[$name], $options);
    }

    final public function mergeOptions(array $options = []): void
    {
        foreach ($options as $name => $option) {
            if (\is_array($option)) {
                $this->mergeOption($name, $option);
            } else {
                $this->setOption($name, $option);
            }
        }
    }

    final public function getAssociationMapping(): ?AssociationMappingInterface
    {
        return $this->associationMapping;
    }

    final public function setAssociationMapping(?AssociationMappingInterface $associationMapping): void
    {
        $this->associationMapping = $associationMapping;
    }

    final public function getFieldMapping(): ?FieldMappingInterface
    {
        return $this->fieldMapping;
    }

    final public function setFieldMapping(?FieldMappingInterface $fieldMapping): void
    {
        $this->fieldMapping = $fieldMapping;
    }

    final public function getParentAssociationMappings(): array
    {
        return $this->parentAssociationMappings;
    }

    /**
     * @param AssociationMappingInterface[] $parentAssociationMappings
     */
    final public function setParentAssociationMappings(array $parentAssociationMappings): void
    {
        $this->parentAssociationMappings = $parentAssociationMappings;
    }

    final public function isVirtual(): bool
    {
        return false !== $this->getOption('virtual_field', false);
    }

    /**
     * @throws NoValueException
     *
     * @return mixed
     */
    final public function getFieldValue(?object $object, ?string $fieldName)
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
