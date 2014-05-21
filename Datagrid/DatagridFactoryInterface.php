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


/**
 * Class DatagridFactoryInterface
 *
 * @package Sonata\DatagridBundle\Datagrid
 *
 * @author Hugo Briand <briand@ekino.com>
 */
interface DatagridFactoryInterface
{
    /**
     * Builds a datagrid depending on the request parameters $params
     *
     * @param string $type   The type of the builder (elastica or doctrine)
     * @param array  $params The filter/sort request parameters
     *
     * @return DatagridInterface
     */
    public function getDatagrid($type, array $params = array());

    /**
     * Returns the datagrid builder instance matching $type used to build the datagrid
     * This allows to then call addFilter/addFacet to the builder
     *
     * @param string $type    The type of the builder (elastica or doctrine)
     * @param array  $options An array of options
     *
     * @return DatagridBuilderInterface
     */
    public function getDatagridBuilder($type, array $options = array());
}