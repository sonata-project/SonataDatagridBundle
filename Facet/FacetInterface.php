<?php

/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DatagridBundle\Facet;

/**
 * Interface FacetInterface
 */
interface FacetInterface
{
    /**
     * Applies the facet to the query
     *
     * @param mixed $query
     */
    public function apply($query);

    /**
     * Returns the Facet name
     *
     * @return string
     */
    public function getName();

    /**
     * @return array
     */
    public function getDefaultOptions();

    /**
     * @param string $name
     * @param null   $default
     *
     * @return mixed
     */
    public function getOption($name, $default = null);

    /**
     * @param string $name
     * @param mixed  $value
     */
    public function setOption($name, $value);

    /**
     * @param string $name
     * @param array  $options
     *
     * @return void
     */
    public function initialize($name, array $options = array());
}
