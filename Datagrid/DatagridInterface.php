<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */

namespace Sonata\DatagridBundle\Datagrid;

use Sonata\DatagridBundle\Facet\FacetInterface;
use Sonata\DatagridBundle\Filter\FilterInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;

interface DatagridInterface
{
    /**
     * @return PagerInterface
     */
    public function getPager();

    /**
     * @return ProxyQueryInterface
     */
    public function getQuery();

    /**
     * @return array
     */
    public function getResults();

    /**
     * @return void
     */
    public function buildPager();

    /**
     * @param FilterInterface $filter
     *
     * @return FilterInterface
     */
    public function addFilter(FilterInterface $filter);

    /**
     * @return FilterInterface[]
     */
    public function getFilters();

    /**
     * Reorder filters
     */
    public function reorderFilters(array $keys);

    /**
     * Adds a facet to the datagrid
     *
     * @param FacetInterface $facet
     *
     * @return mixed
     */
    public function addFacet(FacetInterface $facet);

    /**
     * Checks if facet $name exists in datagrid
     *
     * @param $name
     *
     * @return bool
     */
    public function hasFacet($name);

    /**
     * Removes facet $name from the datagrid
     *
     * @param $name
     */
    public function removeFacet($name);

    /**
     * Retrieves facet $name
     *
     * @param $name
     *
     * @return FacetInterface
     */
    public function getFacet($name);

    /**
     * Retrieves the list of facets
     *
     * @return FacetInterface[]
     */
    public function getFacets();

    /**
     * Sorts the facets
     *
     * @param array $keys
     */
    public function reorderFacets(array $keys);


    /**
     * @return array
     */
    public function getValues();

    /**
     * @param string $name
     * @param string $operator
     * @param mixed  $value
     */
    public function setValue($name, $operator, $value);

    /**
     * Sorts the results by $sortField in $sortDirection
     *
     * @param $sortField     The field to sort the results on
     * @param $sortDirection The direction (ASC or DESC) to sort on
     */
    public function setSort($sortField, $sortDirection);

    /**
     * Sets the page for the paginator
     *
     * @param $page
     */
    public function setPage($page);

    /**
     * Sets the number of items in the page
     *
     * @param $maxPerPage
     */
    public function setMaxPerPage($maxPerPage);

    /**
     * @return \Symfony\Component\Form\Form
     */
    public function getForm();

    /**
     * @param string $name
     *
     * @return FilterInterface
     */
    public function getFilter($name);

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasFilter($name);

    /**
     * @param string $name
     */
    public function removeFilter($name);

    /**
     * @return boolean
     */
    public function hasActiveFilters();
}
