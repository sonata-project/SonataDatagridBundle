<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\DatagridBundle\Tests\Datagrid;

use Sonata\DatagridBundle\Datagrid\DatagridBuilder;


/**
 * Class DatagridBuilderTest
 *
 * @package Sonata\DatagridBundle\Tests\Datagrid
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class DatagridBuilderTest extends \PHPUnit_Framework_TestCase
{
    public function testAddFilter()
    {
        $datagrid = $this->getMock('Sonata\DatagridBundle\Datagrid\DatagridInterface');
        $datagrid->expects($this->once())->method('addFilter');

        $filterFactory = $this->getMock('Sonata\DatagridBundle\Filter\FilterFactoryInterface');
        $filterFactory->expects($this->once())->method('create')->will($this->returnValue($this->getMock('Sonata\DatagridBundle\Filter\FilterInterface')));

        $DatagridBuilder = new DatagridBuilder($datagrid, $filterFactory);

        $DatagridBuilder->addFilter('test', 'test');
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage The filter type must be defined (filter type guesser not supported at the moment)
     */
    public function testAddFilterNoType()
    {
        $datagrid = $this->getMock('Sonata\DatagridBundle\Datagrid\DatagridInterface');

        $filterFactory = $this->getMock('Sonata\DatagridBundle\Filter\FilterFactoryInterface');

        $DatagridBuilder = new DatagridBuilder($datagrid, $filterFactory);

        $DatagridBuilder->addFilter('test');
    }
}
