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

use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class FacetFactory
 *
 * @package Sonata\DatagridBundle\Facet
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class FacetFactory implements FacetFactoryInterface
{
    /**
     * @var array
     */
    private $types = array();

    /**
     * @var string The factory engine (elastica or doctrine)
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
            throw new \RunTimeException('The Facet type must be defined');
        }

        if (!$this->getEngine()) {
            throw new \RuntimeException('The engine of facets must be defined, call setEngine method on filter factory with elastica or doctrine.');
        }

        $facet = isset($this->types[$this->getEngine()][$type]) ? $this->types[$this->getEngine()][$type] : false;

        if (!$facet) {
            throw new \RunTimeException(sprintf('No attached service to Facet type named `%s` in engine `%s`', $type, $this->getEngine()));
        }

        if (!$facet instanceof FacetInterface) {
            throw new \RunTimeException(sprintf('The service `%s` must implement `FacetInterface`', $type));
        }

        $facet->initialize($name, $options);

        return $facet;
    }
}