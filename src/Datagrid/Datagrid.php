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

namespace Sonata\DatagridBundle\Datagrid;

use Sonata\DatagridBundle\Field\FieldDescriptionInterface;
use Sonata\DatagridBundle\Filter\FilterInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;

final class Datagrid implements DatagridInterface
{
    /**
     * @var FilterInterface[]
     */
    private $filters = [];

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
     * @var FormInterface|null
     */
    private $form;

    /**
     * @var array|null
     */
    private $results;

    public function __construct(
        ProxyQueryInterface $query,
        PagerInterface $pager,
        FormBuilder $formBuilder,
        array $values = []
    ) {
        $this->pager = $pager;
        $this->query = $query;
        $this->values = $values;
        $this->formBuilder = $formBuilder;
    }

    public function getPager(): PagerInterface
    {
        return $this->pager;
    }

    public function getResults(): ?array
    {
        $this->buildPager();

        if (!$this->results) {
            $this->results = $this->pager->getResults();
        }

        return $this->results;
    }

    public function buildPager(): void
    {
        if ($this->bound) {
            return;
        }

        foreach ($this->getFilters() as $name => $filter) {
            [$type, $options] = $filter->getRenderSettings();

            $this->formBuilder->add($filter->getFormName(), $type, $options);
        }

        $this->formBuilder->add('_sort_by', HiddenType::class);
        $this->formBuilder->add('_sort_order', HiddenType::class);
        $this->formBuilder->add('_page', HiddenType::class);
        $this->formBuilder->add('_per_page', HiddenType::class);

        $this->formBuilder->get('_sort_by')->addViewTransformer(new CallbackTransformer(
            static function ($value) {
                return $value;
            },
            static function ($value) {
                return $value instanceof FieldDescriptionInterface ? $value->getName() : $value;
            }
        ));

        $this->form = $this->formBuilder->getForm();
        $this->form->submit($this->values);

        $data = $this->form->getData();

        foreach ($this->getFilters() as $name => $filter) {
            $this->values[$name] = $this->values[$name] ?? null;

            $filterFormName = $filter->getFormName();
            if (isset($this->values[$filterFormName]['value']) && '' !== $this->values[$filterFormName]['value']) {
                $filter->apply($this->query, $data[$filterFormName]);
            }
        }

        if (isset($this->values['_sort_by'])) {
            $sortBy = $this->values['_sort_by'];
            if (!$sortBy instanceof FieldDescriptionInterface) {
                throw new UnexpectedTypeException($this->values['_sort_by'], FieldDescriptionInterface::class);
            }

            if (false !== $sortBy->getOption('sortable', false)) {
                $this->values['_sort_order'] = $this->values['_sort_order'] ?? 'ASC';

                $this->query->setSortBy($this->values['_sort_by']);
                $this->query->setSortOrder($this->values['_sort_order']);
            }
        }

        $this->pager->setMaxPerPage($this->values['_per_page'] ?? 25);
        $this->pager->setPage($this->values['_page'] ?? 1);
        $this->pager->setQuery($this->query);
        $this->pager->init();

        $this->bound = true;
    }

    public function addFilter(FilterInterface $filter): void
    {
        $this->filters[$filter->getName()] = $filter;
    }

    public function hasFilter(string $name): bool
    {
        return isset($this->filters[$name]);
    }

    public function removeFilter(string $name): void
    {
        unset($this->filters[$name]);
    }

    public function getFilter(string $name): ?FilterInterface
    {
        return $this->hasFilter($name) ? $this->filters[$name] : null;
    }

    /**
     * @return FilterInterface[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function reorderFilters(array $keys): void
    {
        $this->filters = array_merge(array_flip($keys), $this->filters);
    }

    public function getValues(): array
    {
        return $this->values;
    }

    public function setValue(string $name, string $operator, $value): void
    {
        $this->values[$name] = [
            'type' => $operator,
            'value' => $value,
        ];
    }

    public function hasActiveFilters(): bool
    {
        foreach ($this->filters as $name => $filter) {
            if ($filter->isActive()) {
                return true;
            }
        }

        return false;
    }

    public function hasDisplayableFilters(): bool
    {
        foreach ($this->filters as $name => $filter) {
            $showFilter = $filter->getOption('show_filter', null);
            if (($filter->isActive() && null === $showFilter) || (true === $showFilter)) {
                return true;
            }
        }

        return false;
    }

    public function getQuery(): ProxyQueryInterface
    {
        return $this->query;
    }

    public function getForm(): FormInterface
    {
        $this->buildPager();

        return $this->form;
    }

    public function getSortParameters(FieldDescriptionInterface $fieldDescription): array
    {
        $values = $this->getValues();

        if ($this->isFieldAlreadySorted($fieldDescription)) {
            if ('ASC' === $values['_sort_order']) {
                $values['_sort_order'] = 'DESC';
            } else {
                $values['_sort_order'] = 'ASC';
            }
        } else {
            $values['_sort_order'] = 'ASC';
        }

        $values['_sort_by'] = \is_string($fieldDescription->getOption('sortable'))
            ? $fieldDescription->getOption('sortable')
            : $fieldDescription->getName();

        return ['filter' => $values];
    }

    public function getPaginationParameters(int $page): array
    {
        $values = $this->getValues();

        if (isset($values['_sort_by']) && $values['_sort_by'] instanceof FieldDescriptionInterface) {
            $values['_sort_by'] = $values['_sort_by']->getName();
        }
        $values['_page'] = $page;

        return ['filter' => $values];
    }

    private function isFieldAlreadySorted(FieldDescriptionInterface $fieldDescription): bool
    {
        $values = $this->getValues();

        if (!isset($values['_sort_by']) || !$values['_sort_by'] instanceof FieldDescriptionInterface) {
            return false;
        }

        return $values['_sort_by']->getName() === $fieldDescription->getName()
            || $values['_sort_by']->getName() === $fieldDescription->getOption('sortable');
    }
}
