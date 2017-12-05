<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DatagridBundle\Tests\Datagrid;

use PHPUnit\Framework\TestCase;
use Sonata\DatagridBundle\Datagrid\Datagrid;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;
use Symfony\Component\Form\FormBuilder;

class TestEntity
{
}

/**
 * @author Andrej Hudec <pulzarraider@gmail.com>
 */
class DatagridTest extends TestCase
{
    /**
     * @var Datagrid
     */
    private $datagrid;

    /**
     * @var PagerInterface
     */
    private $pager;

    /**
     * @var ProxyQueryInterface
     */
    private $query;

    /**
     * @var FormBuilder
     */
    private $formBuilder;

    /**
     * @var array
     */
    private $formTypes;

    public function setUp()
    {
        $this->query = $this->createMock('Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface');
        $this->pager = $this->createMock('Sonata\DatagridBundle\Pager\PagerInterface');

        $this->formTypes = [];

        // php 5.3 BC
        $formTypes = &$this->formTypes;

        $this->formBuilder = $this->getMockBuilder('Symfony\Component\Form\FormBuilder')
            ->disableOriginalConstructor()
            ->getMock();
        $this->formBuilder->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($name) use (&$formTypes) {
                if (isset($formTypes[$name])) {
                    return $formTypes[$name];
                }
            }));

        // php 5.3 BC
        $eventDispatcher = $this->createMock('Symfony\Component\EventDispatcher\EventDispatcherInterface');
        $formFactory = $this->createMock('Symfony\Component\Form\FormFactoryInterface');

        $this->formBuilder->expects($this->any())
            ->method('add')
            ->will($this->returnCallback(function ($name, $type, $options) use (&$formTypes, $eventDispatcher, $formFactory) {
                $formTypes[$name] = new FormBuilder($name, 'Sonata\DatagridBundle\Tests\Datagrid\TestEntity', $eventDispatcher, $formFactory, $options);
            }));

        // php 5.3 BC
        $form = $this->getMockBuilder('Symfony\Component\Form\Form')
            ->disableOriginalConstructor()
            ->getMock();

        $this->formBuilder->expects($this->any())
            ->method('getForm')
            ->will($this->returnCallback(function () use ($form) {
                return $form;
            }));

        $values = [];

        $this->datagrid = new Datagrid($this->query, $this->pager, $this->formBuilder, $values);
    }

    public function testGetPager()
    {
        $this->assertEquals($this->pager, $this->datagrid->getPager());
    }

    public function testFilter()
    {
        $this->assertFalse($this->datagrid->hasFilter('foo'));
        $this->assertNull($this->datagrid->getFilter('foo'));

        $filter = $this->createMock('Sonata\DatagridBundle\Filter\FilterInterface');
        $filter->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $this->datagrid->addFilter($filter);

        $this->assertTrue($this->datagrid->hasFilter('foo'));
        $this->assertFalse($this->datagrid->hasFilter('nonexistent'));
        $this->assertEquals($filter, $this->datagrid->getFilter('foo'));

        $this->datagrid->removeFilter('foo');

        $this->assertFalse($this->datagrid->hasFilter('foo'));
    }

    public function testGetFilters()
    {
        $this->assertEquals([], $this->datagrid->getFilters());

        $filter1 = $this->createMock('Sonata\DatagridBundle\Filter\FilterInterface');
        $filter1->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $filter2 = $this->createMock('Sonata\DatagridBundle\Filter\FilterInterface');
        $filter2->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $filter3 = $this->createMock('Sonata\DatagridBundle\Filter\FilterInterface');
        $filter3->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('baz'));

        $this->datagrid->addFilter($filter1);
        $this->datagrid->addFilter($filter2);
        $this->datagrid->addFilter($filter3);

        $this->assertEquals(['foo' => $filter1, 'bar' => $filter2, 'baz' => $filter3], $this->datagrid->getFilters());

        $this->datagrid->removeFilter('bar');

        $this->assertEquals(['foo' => $filter1, 'baz' => $filter3], $this->datagrid->getFilters());
    }

    public function testReorderFilters()
    {
        $this->assertEquals([], $this->datagrid->getFilters());

        $filter1 = $this->createMock('Sonata\DatagridBundle\Filter\FilterInterface');
        $filter1->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $filter2 = $this->createMock('Sonata\DatagridBundle\Filter\FilterInterface');
        $filter2->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $filter3 = $this->createMock('Sonata\DatagridBundle\Filter\FilterInterface');
        $filter3->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('baz'));

        $this->datagrid->addFilter($filter1);
        $this->datagrid->addFilter($filter2);
        $this->datagrid->addFilter($filter3);

        $this->assertEquals(['foo' => $filter1, 'bar' => $filter2, 'baz' => $filter3], $this->datagrid->getFilters());
        $this->assertEquals(['foo', 'bar', 'baz'], array_keys($this->datagrid->getFilters()));

        $this->datagrid->reorderFilters(['bar', 'baz', 'foo']);

        $this->assertEquals(['bar' => $filter2, 'baz' => $filter3, 'foo' => $filter1], $this->datagrid->getFilters());
        $this->assertEquals(['bar', 'baz', 'foo'], array_keys($this->datagrid->getFilters()));
    }

    public function testGetValues()
    {
        $this->assertEquals([], $this->datagrid->getValues());

        $this->datagrid->setValue('foo', 'bar', 'baz');

        $this->assertEquals(['foo' => ['type' => 'bar', 'value' => 'baz']], $this->datagrid->getValues());
    }

    public function testGetQuery()
    {
        $this->assertEquals($this->query, $this->datagrid->getQuery());
    }

    public function testHasActiveFilters()
    {
        $this->assertFalse($this->datagrid->hasActiveFilters());

        $filter1 = $this->createMock('Sonata\DatagridBundle\Filter\FilterInterface');
        $filter1->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $filter1->expects($this->any())
            ->method('isActive')
            ->will($this->returnValue(false));

        $this->datagrid->addFilter($filter1);

        $this->assertFalse($this->datagrid->hasActiveFilters());

        $filter2 = $this->createMock('Sonata\DatagridBundle\Filter\FilterInterface');
        $filter2->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('bar'));
        $filter2->expects($this->any())
            ->method('isActive')
            ->will($this->returnValue(true));

        $this->datagrid->addFilter($filter2);

        $this->assertTrue($this->datagrid->hasActiveFilters());
    }

    public function testGetForm()
    {
        $this->assertInstanceOf('Symfony\Component\Form\Form', $this->datagrid->getForm());
    }

    public function testGetResults()
    {
        $this->assertNull($this->datagrid->getResults());

        $this->pager->expects($this->once())
            ->method('getResults')
            ->will($this->returnValue(['foo', 'bar']));

        $this->assertEquals(['foo', 'bar'], $this->datagrid->getResults());
    }

    public function testBuildPager()
    {
        $filter1 = $this->createMock('Sonata\DatagridBundle\Filter\FilterInterface');
        $filter1->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $filter1->expects($this->any())
            ->method('getFormName')
            ->will($this->returnValue('fooFormName'));
        $filter1->expects($this->any())
            ->method('isActive')
            ->will($this->returnValue(false));
        $filter1->expects($this->any())
            ->method('getRenderSettings')
            ->will($this->returnValue(['foo1', ['bar1' => 'baz1']]));

        $this->datagrid->addFilter($filter1);

        $filter2 = $this->createMock('Sonata\DatagridBundle\Filter\FilterInterface');
        $filter2->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('bar'));
        $filter2->expects($this->any())
            ->method('getFormName')
            ->will($this->returnValue('barFormName'));
        $filter2->expects($this->any())
            ->method('isActive')
            ->will($this->returnValue(true));
        $filter2->expects($this->any())
            ->method('getRenderSettings')
            ->will($this->returnValue(['foo2', ['bar2' => 'baz2']]));

        $this->datagrid->addFilter($filter2);

        $this->datagrid->buildPager();

        $this->assertEquals(['foo' => null, 'bar' => null], $this->datagrid->getValues());
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilder', $this->formBuilder->get('fooFormName'));
        $this->assertEquals(['bar1' => 'baz1'], $this->formBuilder->get('fooFormName')->getOptions());
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilder', $this->formBuilder->get('barFormName'));
        $this->assertEquals(['bar2' => 'baz2'], $this->formBuilder->get('barFormName')->getOptions());
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilder', $this->formBuilder->get('_sort_by'));
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilder', $this->formBuilder->get('_sort_order'));
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilder', $this->formBuilder->get('_page'));
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilder', $this->formBuilder->get('_per_page'));
    }

    public function testBuildPagerWithSortBy()
    {
        $this->datagrid = new Datagrid($this->query, $this->pager, $this->formBuilder, [
            '_sort_by' => 'name',
        ]);

        $filter = $this->createMock('Sonata\DatagridBundle\Filter\FilterInterface');
        $filter->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $filter->expects($this->any())
            ->method('getFormName')
            ->will($this->returnValue('fooFormName'));
        $filter->expects($this->any())
            ->method('isActive')
            ->will($this->returnValue(false));
        $filter->expects($this->any())
            ->method('getRenderSettings')
            ->will($this->returnValue(['foo', ['bar' => 'baz']]));

        $this->datagrid->addFilter($filter);

        $this->datagrid->buildPager();

        $this->assertEquals(['_sort_by' => 'name', 'foo' => null], $this->datagrid->getValues());
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilder', $this->formBuilder->get('fooFormName'));
        $this->assertEquals(['bar' => 'baz'], $this->formBuilder->get('fooFormName')->getOptions());
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilder', $this->formBuilder->get('_sort_by'));
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilder', $this->formBuilder->get('_sort_order'));
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilder', $this->formBuilder->get('_page'));
        $this->assertInstanceOf('Symfony\Component\Form\FormBuilder', $this->formBuilder->get('_per_page'));
    }
}
