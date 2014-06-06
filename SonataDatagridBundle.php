<?php

/*
 * This file is part of the Sonata project.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DatagridBundle;

use Sonata\DatagridBundle\DependencyInjection\CompilerPass\FacetCompilerPass;
use Sonata\DatagridBundle\DependencyInjection\CompilerPass\FilterCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class SonataDatagridBundle
 */
class SonataDatagridBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new FilterCompilerPass());
        $container->addCompilerPass(new FacetCompilerPass());
    }
}
