<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\DatagridBundle\Filter\Elastica;

use Sonata\DatagridBundle\Filter\BaseFilter;
use Sonata\DatagridBundle\ProxyQuery\Elastica\ProxyQuery;
use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;


/**
 * Class Max
 *
 * @package Sonata\DatagridBundle\Filter\Elastica
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class Max extends BaseFilter
{
    /**
     * Apply the filter to the QueryBuilder instance
     *
     * @param ProxyQueryInterface $queryBuilder
     * @param string              $alias
     * @param string              $field
     * @param string              $value
     *
     * @return void
     */
    public function filter(ProxyQueryInterface $queryBuilder, $alias, $field, $value)
    {
        // TODO: Implement filter() method.
    }

    /**
     * @param ProxyQuery $query
     * @param mixed $value
     */
    public function apply($query, $value)
    {
        $query->addFilter(new \Elastica\Filter\Range($this->getName(), array('to' => $value)));
    }

    /**
     * Returns the main widget used to render the filter
     *
     * @return array
     */
    public function getRenderSettings()
    {
        return array(
            'choice',
            array(
                'field_type'    => $this->getFieldType(),
                'field_options' => $this->getFieldOptions(),
                'label'         => $this->getLabel()
            )
        );
    }

}