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
 * Class FilterCompilerPass
 *
 * @package Sonata\DatagridBundle\DependencyInjection\CompilerPass
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class FilterCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $filters       = $container->findTaggedServiceIds('sonata.datagrid.filter.type');
        $filtersByType = array();

        foreach ($filters as $filterId => $attributes) {
            $filtersByType[$attributes[0]['engine']][$attributes[0]['alias']] = new Reference($filterId);
        }

        $container->getDefinition('sonata.datagrid.filter.factory')->replaceArgument(0, $filtersByType);
    }
}