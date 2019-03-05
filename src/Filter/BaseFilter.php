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

abstract class BaseFilter implements FilterInterface
{
    /**
     * @var string|null
     */
    private $name;

    /**
     * @var mixed
     */
    private $value;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var string
     */
    private $condition;

    public function initialize(string $name, array $options = []): void
    {
        $this->name = $name;
        $this->setOptions($options);
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getFormName(): string
    {
        /* Symfony default form class sadly can't handle
           form element with dots in its name (when data
           get bound, the default dataMapper is a PropertyPathMapper).
           So use this trick to avoid any issue.
        */

        return \str_replace('.', '__', $this->name);
    }

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function getOption(string $name, $default = null)
    {
        if (\array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        return $default;
    }

    /**
     * @param mixed $value
     */
    public function setOption(string $name, $value): void
    {
        $this->options[$name] = $value;
    }

    public function getFieldType(): string
    {
        return $this->getOption('field_type', 'text');
    }

    public function getFieldOptions(): array
    {
        return $this->getOption('field_options', ['required' => false]);
    }

    public function getLabel(): ?string
    {
        return $this->getOption('label');
    }

    public function setLabel(string $label): void
    {
        $this->setOption('label', $label);
    }

    public function getFieldName(): string
    {
        $fieldName = $this->getOption('field_name');

        if (!$fieldName) {
            throw new \RuntimeException(sprintf('The option `field_name` must be set for field : `%s`', $this->getName()));
        }

        return $fieldName;
    }

    public function setOptions(array $options): void
    {
        $this->options = \array_merge($this->getDefaultOptions(), $options);
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value): void
    {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    public function isActive(): bool
    {
        $values = $this->getValue();

        return isset($values['value'])
            && false !== $values['value']
            && '' !== $values['value'];
    }

    public function setCondition(string $condition): void
    {
        $this->condition = $condition;
    }

    public function getCondition(): string
    {
        return $this->condition;
    }

    public function getTranslationDomain(): string
    {
        return $this->getOption('translation_domain');
    }
}
