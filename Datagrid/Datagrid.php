<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DatagridBundle\Datagrid;

use Sonata\DatagridBundle\Facet\FacetInterface;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormInterface;

use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\DatagridBundle\Filter\FilterInterface;

class Datagrid implements DatagridInterface
{
    /**
     * The filter instances
     *
     * @var array
     */
    protected $filters = array();

    /**
     * The facet instances
     *
     * @var array
     */
    protected $facets = array();

    /**
     * Values / Datagrid options
     *
     * @var array
     */
    protected $values;

    /**
     * @var PagerInterface
     */
    protected $pager;

    /**
     * @var bool
     */
    protected $bound = false;

    /**
     * @var ProxyQueryInterface
     */
    protected $query;

    /**
     * @var FormBuilder
     */
    protected $formBuilder;

    /**
     * @var FormInterface
     */
    protected $form;

    /**
     * @var array|null
     */
    protected $results;

    /**
     * Constructor
     *
     * @param ProxyQueryInterface $query
     * @param PagerInterface      $pager
     * @param FormBuilder         $formBuilder
     * @param array               $values
     */
    public function __construct(ProxyQueryInterface $query, PagerInterface $pager, FormBuilder $formBuilder, array $values = array())
    {
        $this->pager       = $pager;
        $this->query       = $query;
        $this->values      = $values;
        $this->formBuilder = $formBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getPager()
    {
        return $this->pager;
    }

    /**
     * {@inheritdoc}
     */
    public function getResults()
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
    public function buildPager()
    {
        if ($this->bound) {
            return;
        }

        foreach ($this->getFilters() as $filter) {
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

        foreach ($this->getFacets() as $facet) {
            $facet->apply($this->query);
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
    public function addFilter(FilterInterface $filter)
    {
        $this->filters[$filter->getName()] = $filter;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFilter($name)
    {
        return isset($this->filters[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeFilter($name)
    {
        unset($this->filters[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFilter($name)
    {
        return $this->hasFilter($name) ? $this->filters[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return $this->filters;
    }

    /**
     * {@inheritdoc}
     */
    public function reorderFilters(array $keys)
    {
        $this->filters = array_merge(array_flip($keys), $this->filters);
    }

    /**
     * {@inheritdoc}
     */
    public function addFacet(FacetInterface $facet)
    {
        $this->facets[$facet->getName()] = $facet;
    }

    /**
     * {@inheritdoc}
     */
    public function hasFacet($name)
    {
        return isset($this->facets[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function removeFacet($name)
    {
        unset($this->facets[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function getFacet($name)
    {
        return $this->hasFacet($name) ? $this->facets[$name] : null;
    }

    /**
     * {@inheritdoc}
     */
    public function getFacets()
    {
        return $this->facets;
    }

    /**
     * {@inheritdoc}
     */
    public function reorderFacets(array $keys)
    {
        $this->facets = array_merge(array_flip($keys), $this->facets);
    }

    /**
     * {@inheritdoc}
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * {@inheritdoc}
     */
    public function setValue($name, $operator, $value)
    {
        $this->values[$name] = array(
            'type'  => $operator,
            'value' => $value
        );
    }

    /**
     * {@inheritdoc}
     */
    public function setSort($sortField, $sortDirection)
    {
        $this->values['_sort_by']    = $sortField;
        $this->values['_sort_order'] = $sortDirection;
    }

    /**
     * {@inheritdoc}
     */
    public function setPage($page)
    {
        $this->values['_page'] = $page;
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxPerPage($maxPerPage)
    {
        $this->values['_per_page'] = $maxPerPage;
    }

    /**
     * {@inheritdoc}
     */
    public function hasActiveFilters()
    {
        foreach ($this->getFilters() as $filter) {
            if ($filter->isActive()) {
                return true;
            }
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery()
    {
        return $this->query;
    }

    /**
     * {@inheritdoc}
     */
    public function getForm()
    {
        $this->buildPager();

        return $this->form;
    }
}
