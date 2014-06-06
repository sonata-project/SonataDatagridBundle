<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\DatagridBundle\Filter;

/**
 * Class FilterFactory
 *
 * @package Sonata\DatagridBundle\Filter
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class FilterFactory implements FilterFactoryInterface
{
    /**
     * @var array
     */
    private $types = array();

    /**
     * @var string The engine of the filters to build (elastica or doctrine)
     */
    private $engine;

    /**
     * @param array $types
     */
    public function __construct(array $types = array())
    {
        $this->types = $types;
    }

    /**
     * {@inheritdoc}
     */
    public function setEngine($engine)
    {
        $this->engine = $engine;
    }

    /**
     * @return string
     */
    public function getEngine()
    {
        return $this->engine;
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, $type, array $options = array())
    {
        if (!$type) {
            throw new \RunTimeException('The filter type must be defined');
        }

        if (!$this->getEngine()) {
            throw new \RuntimeException('The engine of filters must be defined, call setEngine method on filter factory with elastica or doctrine.');
        }

        $filter = isset($this->types[$this->getEngine()][$type]) ? $this->types[$this->getEngine()][$type] : false;

        if (!$filter) {
            throw new \RunTimeException(sprintf('No attached service to filter type named `%s` in', $type));
        }

        if (!$filter instanceof FilterInterface) {
            throw new \RunTimeException(sprintf('The service `%s` of engine `%s` must implement `FilterInterface`', $name, $this->getEngine()));
        }

        $filter->initialize($name, $options);

        return $filter;
    }
}