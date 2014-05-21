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
 * Class DatagridBuilderInterface
 *
 * @package Sonata\DatagridBundle\Datagrid
 *
 * @author Hugo Briand <briand@ekino.com>
 */
interface DatagridBuilderInterface
{
    /**
     * @return DatagridInterface
     */
    public function getDatagrid();

    /**
     * Adds a filter of type $type on field $name
     *
     * @param string $name
     * @param string $type
     * @param array  $options
     *
     * @return DatagridBuilderInterface
     */
    public function addFilter($name, $type = null, array $options = array());

    /**
     * @param string $name
     * @param string $type
     * @param array  $options
     *
     * @return DatagridBuilderInterface
     */
    public function addFacet($name, $type = null, array $options = array());

    /**
     * @param       $name
     * @param null  $type
     * @param array $options
     *
     * @return mixed
     */
    public function addFormField($name, $type = null, array $options = array());
}