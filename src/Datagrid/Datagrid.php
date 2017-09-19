<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DatagridBundle\Datagrid;

use Sonata\DatagridBundle\Filter\FilterInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;

final class Datagrid implements DatagridInterface
{
    /**
     * The filter instances.
     *
     * @var array
     */
    private $filters = array();

    /**
     * Values / Datagrid options.
     *
     * @var array
     */
    private $values;

    /**
     * @var PagerInterface
     */
    private $pager;

    /**
     * @var bool
     */
    private $bound = false;

    /**
     * @var ProxyQueryInterface
     */
    private $query;

    /**
     * @var FormBuilder
     */
    private $formBuilder;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var array|null
     */
    private $results;

    /**
     * @param ProxyQueryInterface $query
     * @param PagerInterface      $pager
     * @param FormBuilder         $formBuilder
     * @param array               $values
     */
    public function __construct(ProxyQueryInterface $query, PagerInterface $pager, FormBuilder $formBuilder, array $values = array())
    {
        $this->pager = $pager;
        $this->query = $query;
        $this->values = $values;
        $this->formBuilder = $formBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getPager(): PagerInterface
    {
        return $this->pager;
    }

    /**
     * {@inheritdoc}
     */
    public function getResults(): ?array
    {
        $this->buildPager();

        if (!$this->results) {
            $this->results = $this->pager->getResults();
        }

        return $this->results;
    }

    /**
     * {@inheritdoc}
     */
    public function buildPager(): void
    {
        if ($this->bound) {
            return;
        }

        foreach ($this->getFilters() as $name => $filter) {
            list($type, $options) = $filter->getRenderSettings();

            $this->formBuilder->add($filter->getFormName(), $type, $options);
        }

        $this->formBuilder->add('_sort_by', 'hidden');
        $this->formBuilder->add('_sort_order', 'hidden');
        $this->formBuilder->add('_page', 'hidden');
        $this->formBuilder->add('_per_page', 'hidden');

        $this->form = $this->formBuilder->getForm();
        $this->form->submit($this->values);

        $data = $this->form->getData();

        foreach ($this->getFilters() as $name => $filter) {
            $this->values[$name] = isset($this->values[$name]) ? $this->values[$name] : null;
            $filter->apply($this->query, $data[$filter->getFormName()]);
        }

        if (isset($this->values['_sort_by'])) {
            $this->query->setSortBy($this->values['_sort_by']);
            $this->query->setSortOrder(isset($this->values['_sort_order']) ? $this->values['_sort_order'] : null);
        }

        $this->pager->setMaxPerPage(isset($this->values['_per_page']) ? $this->values['_per_page'] : 25);
        $this->pager->setPage(isset($this->values['_page']) ? $this->values['_page'] : 1);
        $this->pager->setQuery($this->query);
        $this->pager->init();

        $this->bound = true;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter(FilterInterface $filter): void
    {
        $this->filters[$filter->getName()] = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFilter(string $name): bool
    {
        return isset($this->filters[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeFilter(string $name): void
    {
        unset($this->filters[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter(string $name): ?FilterInterface
    {
        return $this->hasFilter($name) ? $this->filters[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function reorderFilters(array $keys): void
    {
        $this->filters = array_merge(array_flip($keys), $this->filters);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue(string $name, string $operator, $value): void
    {
        $this->values[$name] = array(
            'type' => $operator,
            'value' => $value,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function hasActiveFilters(): bool
    {
        foreach ($this->filters as $name => $filter) {
            if ($filter->isActive()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery(): ProxyQueryInterface
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm(): FormInterface
    {
        $this->buildPager();

        return $this->form;
    }
}
