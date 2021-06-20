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

use Symfony\Component\Form\Extension\Core\Type\TextType;

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
     * @var array<string, mixed>
     */
    private $options = [];

    /**
     * @var string|null
     */
    private $condition;

    /**
     * @var bool
     */
    private $active = false;

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function initialize(string $name, array $options = []): void
    {
        $this->name = $name;
        $this->setOptions($options);
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function getName(): ?string
    {
        if (null === $this->name) {
            // NEXT_MAJOR: Uncomment the exception.
//            throw new \LogicException(sprintf(
//                'Seems like you didn\'t call `initialize()` on the filter `%s`. Did you create it through `%s::create()`?',
//                static::class,
//                FilterFactoryInterface::class
//            ));
        }

        return $this->name;
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
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
     * @final since sonata-project/datagrid-bundle 3.x.
     *
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
     * @final since sonata-project/datagrid-bundle 3.x.
     *
     * @param mixed $value
     */
    public function setOption(string $name, $value): void
    {
        $this->options[$name] = $value;
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function getFieldType(): string
    {
        return $this->getOption('field_type', TextType::class);
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function getFieldOptions(): array
    {
        return $this->getOption('field_options', ['required' => false]);
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function getLabel()
    {
        return $this->getOption('label');
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function setLabel($label): void
    {
        $this->setOption('label', $label);
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function getFieldName(): string
    {
        $fieldName = $this->getOption('field_name');

        if (null === $fieldName) {
            throw new \RuntimeException(sprintf(
                'The option `field_name` must be set for field: `%s`',
                $this->getName()
            ));
        }

        return $fieldName;
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     *
     * @param array<string, mixed> $options
     */
    public function setOptions(array $options): void
    {
        $this->options = array_merge($this->getDefaultOptions(), $options);
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     *
     * @return array<string, mixed>
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

    public function isActive(): bool
    {
        $values = $this->getValue();
        $deprecatedIsActive = isset($values['value'])
            && false !== $values['value']
            && '' !== $values['value'];

        if ($deprecatedIsActive && !$this->active) {
            @trigger_error(
                'Not calling the `setActive` method is deprecated since sonata-project/datagrid-bundle 3.x and will not work in 4.0.',
                \E_USER_DEPRECATED
            );

            return true;
        }

        return $this->active;
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

    final protected function setActive(bool $active): void
    {
        $this->active = $active;
    }
}
