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
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var array
     */
    private $types = array();

    /**
     * @param ContainerInterface $container
     * @param array              $types
     */
    public function __construct(ContainerInterface $container, array $types = array())
    {
        $this->container = $container;
        $this->types     = $types;
    }

    /**
     * {@inheritdoc}
     */
    public function create($name, $type, array $options = array())
    {
        if (!$type) {
            throw new \RunTimeException('The Facet type must be defined');
        }

        $id = isset($this->types[$type]) ? $this->types[$type] : false;

        if (!$id) {
            throw new \RunTimeException(sprintf('No attached service to Facet type named `%s`', $type));
        }

        $Facet = $this->container->get($id);

        if (!$Facet instanceof FacetInterface) {
            throw new \RunTimeException(sprintf('The service `%s` must implement `FacetInterface`', $id));
        }

        $Facet->initialize($name, $options);

        return $Facet;
    }
}