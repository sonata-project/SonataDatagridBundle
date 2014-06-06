<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\DatagridBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;


/**
 * Class FacetCompilerPass
 *
 * @package Sonata\DatagridBundle\DependencyInjection\CompilerPass
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class FacetCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $facets       = $container->findTaggedServiceIds('sonata.datagrid.facet.type');
        $facetsByType = array();

        foreach ($facets as $facetId => $attributes) {
            $facetsByType[$attributes[0]['engine']][$attributes[0]['alias']] = new Reference($facetId);
        }

        $container->getDefinition('sonata.datagrid.facet.factory')->replaceArgument(0, $facetsByType);
    }
}