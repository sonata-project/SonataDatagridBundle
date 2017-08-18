<?php

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
     * @var string
     */
    protected $name = null;

    /**
     * @var mixed
     */
    protected $value = null;

    /**
     * @var array
     */
    protected $options = array();

    /**
     * @var string
     */
    protected $condition;

    /**
     * {@inheritdoc}
     */
    public function initialize(string $name, array $options = array()): void
    {
        $this->name = $name;
        $this->setOptions($options);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormName(): string
    {
        /* Symfony default form class sadly can't handle
           form element with dots in its name (when data
           get bound, the default dataMapper is a PropertyPathMapper).
           So use this trick to avoid any issue.
        */

        return str_replace('.', '__', $this->name);
    }

    /**
     * {@inheritdoc}
     */
    public function getOption(string $name, $default = null)
    {
        if (array_key_exists($name, $this->options)) {
            return $this->options[$name];
        }

        return $default;
    }

    /**
     * {@inheritdoc}
     */
    public function setOption(string $name, $value): void
    {
        $this->options[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldType(): string
    {
        return $this->getOption('field_type', 'text');
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldOptions(): array
    {
        return $this->getOption('field_options', array('required' => false));
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): ?string
    {
        return $this->getOption('label');
    }

    /**
     * {@inheritdoc}
     */
    public function setLabel(string $label): void
    {
        $this->setOption('label', $label);
    }

    /**
     * {@inheritdoc}
     */
    public function getFieldName(): string
    {
        $fieldName = $this->getOption('field_name');

        if (!$fieldName) {
            throw new \RuntimeException(sprintf('The option `field_name` must be set for field : `%s`', $this->getName()));
        }

        return $fieldName;
    }

    /**
     * @param array $options
     */
    public function setOptions(array $options): void
    {
        $this->options = array_merge($this->getDefaultOptions(), $options);
    }

    /**
     * @return array
     */
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

    /**
     * {@inheritdoc}
     */
    public function isActive(): bool
    {
        $values = $this->getValue();

        return isset($values['value'])
        && false !== $values['value']
        && '' !== $values['value'];
    }

    /**
     * @param string $condition
     */
    public function setCondition(string $condition): void
    {
        $this->condition = $condition;
    }

    /**
     * {@inheritdoc}
     */
    public function getCondition(): string
    {
        return $this->condition;
    }

    /**
     * {@inheritdoc}
     */
    public function getTranslationDomain(): string
    {
        return $this->getOption('translation_domain');
    }
}
