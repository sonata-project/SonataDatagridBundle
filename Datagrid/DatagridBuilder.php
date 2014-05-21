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

use Sonata\DatagridBundle\Facet\FacetFactoryInterface;
use Sonata\DatagridBundle\Filter\FilterFactoryInterface;

/**
 * Class DatagridBuilder
 *
 * @package Sonata\DatagridBundle\Datagrid
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class DatagridBuilder implements DatagridBuilderInterface
{
    /**
     * @var DatagridInterface
     */
    private $datagrid;

    /**
     * @var FilterFactoryInterface
     */
    private $filterFactory;

    /**
     * @var FacetFactoryInterface
     */
    private $facetFactory;

    /**
     * @param DatagridInterface      $datagrid
     * @param FilterFactoryInterface $filterFactory
     * @param FacetFactoryInterface  $facetFactory
     */
    public function __construct(DatagridInterface $datagrid, FilterFactoryInterface $filterFactory, FacetFactoryInterface $facetFactory)
    {
        $this->datagrid      = $datagrid;
        $this->filterFactory = $filterFactory;
        $this->facetFactory  = $facetFactory;
    }

    /**
     * @return DatagridInterface
     */
    public function getDatagrid()
    {
        return $this->datagrid;
    }

    /**
     * {@inheritdoc}
     */
    public function addFilter($name, $type = null, array $options = array())
    {
        if (!$type) {
            throw new \RunTimeException('The filter type must be defined (filter type guesser not supported at the moment)');
        }

        $this->datagrid->addFilter($this->filterFactory->create($name, $type, $options));

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFormField($name, $type = null, array $options = array())
    {
        $this->datagrid->getForm()->add($name, $type, $options);
    }

    /**
     * {@inheritdoc}
     */
    public function addFacet($name, $type = null, array $options = array())
    {
        if (!$type) {
            throw new \RunTimeException('The facet type must be defined (facet type guesser not supported at the moment)');
        }

        $this->datagrid->addFacet($this->facetFactory->create($name, $type, $options));
    }
}