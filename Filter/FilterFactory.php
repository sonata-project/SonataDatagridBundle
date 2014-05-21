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

use Symfony\Component\DependencyInjection\ContainerInterface;

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
            throw new \RunTimeException('The filter type must be defined');
        }

        $id = isset($this->types[$type]) ? $this->types[$type] : false;

        if (!$id) {
            throw new \RunTimeException(sprintf('No attached service to filter type named `%s`', $type));
        }

        $filter = $this->container->get($id);

        if (!$filter instanceof FilterInterface) {
            throw new \RunTimeException(sprintf('The service `%s` must implement `FilterInterface`', $id));
        }

        $filter->initialize($name, $options);

        return $filter;
    }
}