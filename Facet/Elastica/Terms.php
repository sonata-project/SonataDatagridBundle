<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\DatagridBundle\Facet\Elastica;

use Sonata\DatagridBundle\Facet\BaseFacet;
use Sonata\DatagridBundle\ProxyQuery\Elastica\ProxyQuery;


/**
 * Class Terms
 *
 * @package Sonata\DatagridBundle\Facet\Elastica
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class Terms extends BaseFacet
{
    /**
     * @param ProxyQuery $query
     */
    public function apply($query)
    {
        $termFacet = new \Elastica\Facet\Terms($this->getName());
        $termFacet->setField($this->getOption('field'));

        $query->addFacet($termFacet);
    }
}