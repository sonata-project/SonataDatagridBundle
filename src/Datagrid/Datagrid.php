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

use Sonata\DatagridBundle\Filter\FilterInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\UnexpectedTypeException;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

/**
 * @phpstan-template T of ProxyQueryInterface
 * @phpstan-implements DatagridInterface<T>
 */
final class Datagrid implements DatagridInterface
{
    /**
     * @var array<string, FilterInterface>
     */
    private $filters = [];

    /**
     * Values / Datagrid options.
     *
     * @var array<string, mixed>
     */
    private $values;

    /**
     * @var PagerInterface
     * @phpstan-var PagerInterface<T>
     */
    private $pager;

    /**
     * @var bool
     */
    private $bound = false;

    /**
     * @var ProxyQueryInterface
     * @phpstan-var T
     */
    private $query;

    /**
     * @var FormBuilderInterface
     */
    private $formBuilder;

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * NEXT_MAJOR: Change to iterable<object>|null
     *
     * Results are null prior to its initialization in `getResults()`.
     *
     * @var array<object>|null
     */
    private $results;

    /**
     * @param array<string, mixed> $values
     *
     * @phpstan-param T                 $query
     * @phpstan-param PagerInterface<T> $pager
     */
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

    /**
     * NEXT_MAJOR: Change return type to `iterable`
     */
    public function getResults(): ?array
    {
        $this->buildPager();

        if (null === $this->results) {
            // NEXT_MAJOR: Keep the if part.
            if (method_exists($this->pager, 'getCurrentPageResults')) {
                $this->results = $this->pager->getCurrentPageResults();
            } else {
                $this->results = $this->pager->getResults();
            }
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

        $this->formBuilder->add(DatagridInterface::SORT_BY, HiddenType::class);
        $this->formBuilder->get(DatagridInterface::SORT_BY)->addViewTransformer(new CallbackTransformer(
            static function ($value) {
                return $value;
            },
            static function ($value) {
                return $value instanceof FieldDescriptionInterface ? $value->getName() : $value;
            }
        ));
        $this->formBuilder->add(DatagridInterface::SORT_ORDER, HiddenType::class);
        $this->formBuilder->add(DatagridInterface::PAGE, HiddenType::class);

        if (isset($this->values[DatagridInterface::PER_PAGE]) && \is_array($this->values[DatagridInterface::PER_PAGE])) {
            $this->formBuilder->add(DatagridInterface::PER_PAGE, CollectionType::class, [
                'entry_type' => HiddenType::class,
                'allow_add' => true,
            ]);
        } else {
            $this->formBuilder->add(DatagridInterface::PER_PAGE, HiddenType::class);
        }

        $this->form = $this->formBuilder->getForm();
        $this->form->submit($this->values);

        $data = $this->form->getData();

        $this->applyFilters($this->form->getData() ?? []);
        $this->applySorting();

        $this->pager->setMaxPerPage($this->getMaxPerPage(25));
        $this->pager->setPage($this->getPage(1));
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
        if (!$this->hasFilter($name)) {
            // NEXT_MAJOR: Uncomment the exception.
//            throw new \InvalidArgumentException(sprintf(
//                'Filter named "%s" doesn\'t exist.',
//                $name
//            ));

            return null;
        }

        return $this->filters[$name];
    }

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

    public function getQuery(): ProxyQueryInterface
    {
        return $this->query;
    }

    public function getForm(): FormInterface
    {
        $this->buildPager();

        return $this->form;
    }

    /**
     * @param array<string, mixed> $data
     */
    private function applyFilters(array $data): void
    {
        foreach ($this->getFilters() as $name => $filter) {
            $this->values[$name] = $this->values[$name] ?? null;
            $filterFormName = $filter->getFormName();

            $value = $this->values[$filterFormName]['value'] ?? '';
            $type = $this->values[$filterFormName]['type'] ?? '';

            if ('' !== $value || '' !== $type) {
                $filter->apply($this->query, $data[$filterFormName]);
            }
        }
    }

    private function applySorting(): void
    {
        if (!isset($this->values[DatagridInterface::SORT_BY])) {
            return;
        }

        if (!$this->values[DatagridInterface::SORT_BY] instanceof FieldDescriptionInterface) {
            throw new UnexpectedTypeException($this->values[DatagridInterface::SORT_BY], FieldDescriptionInterface::class);
        }

        if (!$this->values[DatagridInterface::SORT_BY]->isSortable()) {
            return;
        }

        $this->query->setSortBy(
            $this->values[DatagridInterface::SORT_BY]->getSortParentAssociationMapping(),
            $this->values[DatagridInterface::SORT_BY]->getSortFieldMapping()
        );

        $this->values[DatagridInterface::SORT_ORDER] = $this->values[DatagridInterface::SORT_ORDER] ?? 'ASC';
        $this->query->setSortOrder($this->values[DatagridInterface::SORT_ORDER]);
    }

    private function getMaxPerPage(int $default): int
    {
        if (!isset($this->values[DatagridInterface::PER_PAGE])) {
            return $default;
        }

        if (isset($this->values[DatagridInterface::PER_PAGE]['value'])) {
            return (int) $this->values[DatagridInterface::PER_PAGE]['value'];
        }

        return (int) $this->values[DatagridInterface::PER_PAGE];
    }

    private function getPage(int $default): int
    {
        if (!isset($this->values[DatagridInterface::PAGE])) {
            return $default;
        }

        if (isset($this->values[DatagridInterface::PAGE]['value'])) {
            return (int) $this->values[DatagridInterface::PAGE]['value'];
        }

        return (int) $this->values[DatagridInterface::PAGE];
    }
}
