<?php

declare(strict_types=1);

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
use Sonata\DatagridBundle\Filter\FilterInterface;
use Sonata\DatagridBundle\Pager\PagerInterface;
use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;

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

    public function setUp(): void
    {
        $this->query = $this->createMock(ProxyQueryInterface::class);
        $this->pager = $this->createMock(PagerInterface::class);

        $this->formTypes = [];

        $this->formBuilder = $this->createMock(FormBuilder::class);
        $this->formBuilder
            ->expects($this->any())
            ->method('get')
            ->will($this->returnCallback(function ($name) {
                if (isset($this->formTypes[$name])) {
                    return $this->formTypes[$name];
                }
            }));

        $eventDispatcher = $this->createMock(EventDispatcherInterface::class);
        $formFactory = $this->createMock(FormFactoryInterface::class);

        $this->formBuilder->expects($this->any())
            ->method('add')
            ->will($this->returnCallback(function ($name, $type, $options) use ($eventDispatcher, $formFactory): void {
                $this->formTypes[$name] = new FormBuilder($name, TestEntity::class, $eventDispatcher, $formFactory, $options);
            }));

        $form = $this->createMock(Form::class);

        $this->formBuilder->expects($this->any())
            ->method('getForm')
            ->will($this->returnCallback(function () use ($form) {
                return $form;
            }));

        $values = [];

        $this->datagrid = new Datagrid($this->query, $this->pager, $this->formBuilder, $values);
    }

    public function testGetPager(): void
    {
        $this->assertSame($this->pager, $this->datagrid->getPager());
    }

    public function testFilter(): void
    {
        $this->assertFalse($this->datagrid->hasFilter('foo'));
        $this->assertNull($this->datagrid->getFilter('foo'));

        $filter = $this->createMock(FilterInterface::class);
        $filter->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $this->datagrid->addFilter($filter);

        $this->assertTrue($this->datagrid->hasFilter('foo'));
        $this->assertFalse($this->datagrid->hasFilter('nonexistent'));
        $this->assertSame($filter, $this->datagrid->getFilter('foo'));

        $this->datagrid->removeFilter('foo');

        $this->assertFalse($this->datagrid->hasFilter('foo'));
    }

    public function testGetFilters(): void
    {
        $this->assertSame([], $this->datagrid->getFilters());

        $filter1 = $this->createMock(FilterInterface::class);
        $filter1->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $filter2 = $this->createMock(FilterInterface::class);
        $filter2->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $filter3 = $this->createMock(FilterInterface::class);
        $filter3->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('baz'));

        $this->datagrid->addFilter($filter1);
        $this->datagrid->addFilter($filter2);
        $this->datagrid->addFilter($filter3);

        $this->assertSame(['foo' => $filter1, 'bar' => $filter2, 'baz' => $filter3], $this->datagrid->getFilters());

        $this->datagrid->removeFilter('bar');

        $this->assertSame(['foo' => $filter1, 'baz' => $filter3], $this->datagrid->getFilters());
    }

    public function testReorderFilters(): void
    {
        $this->assertSame([], $this->datagrid->getFilters());

        $filter1 = $this->createMock(FilterInterface::class);
        $filter1->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));

        $filter2 = $this->createMock(FilterInterface::class);
        $filter2->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('bar'));

        $filter3 = $this->createMock(FilterInterface::class);
        $filter3->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('baz'));

        $this->datagrid->addFilter($filter1);
        $this->datagrid->addFilter($filter2);
        $this->datagrid->addFilter($filter3);

        $this->assertSame(['foo' => $filter1, 'bar' => $filter2, 'baz' => $filter3], $this->datagrid->getFilters());
        $this->assertSame(['foo', 'bar', 'baz'], array_keys($this->datagrid->getFilters()));

        $this->datagrid->reorderFilters(['bar', 'baz', 'foo']);

        $this->assertSame(['bar' => $filter2, 'baz' => $filter3, 'foo' => $filter1], $this->datagrid->getFilters());
        $this->assertSame(['bar', 'baz', 'foo'], array_keys($this->datagrid->getFilters()));
    }

    public function testGetValues(): void
    {
        $this->assertSame([], $this->datagrid->getValues());

        $this->datagrid->setValue('foo', 'bar', 'baz');

        $this->assertSame(['foo' => ['type' => 'bar', 'value' => 'baz']], $this->datagrid->getValues());
    }

    public function testGetQuery(): void
    {
        $this->assertSame($this->query, $this->datagrid->getQuery());
    }

    public function testHasActiveFilters(): void
    {
        $this->assertFalse($this->datagrid->hasActiveFilters());

        $filter1 = $this->createMock(FilterInterface::class);
        $filter1->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('foo'));
        $filter1->expects($this->any())
            ->method('isActive')
            ->will($this->returnValue(false));

        $this->datagrid->addFilter($filter1);

        $this->assertFalse($this->datagrid->hasActiveFilters());

        $filter2 = $this->createMock(FilterInterface::class);
        $filter2->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('bar'));
        $filter2->expects($this->any())
            ->method('isActive')
            ->will($this->returnValue(true));

        $this->datagrid->addFilter($filter2);

        $this->assertTrue($this->datagrid->hasActiveFilters());
    }

    public function testGetForm(): void
    {
        $this->assertInstanceOf(Form::class, $this->datagrid->getForm());
    }

    public function testGetResults(): void
    {
        $this->assertNull($this->datagrid->getResults());

        $this->pager->expects($this->once())
            ->method('getResults')
            ->will($this->returnValue(['foo', 'bar']));

        $this->assertSame(['foo', 'bar'], $this->datagrid->getResults());
    }

    public function testBuildPager(): void
    {
        $filter1 = $this->createMock(FilterInterface::class);
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

        $filter2 = $this->createMock(FilterInterface::class);
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

        $this->assertSame(['foo' => null, 'bar' => null], $this->datagrid->getValues());
        $this->assertInstanceOf(FormBuilder::class, $this->formBuilder->get('fooFormName'));
        $this->assertSame(['bar1' => 'baz1'], $this->formBuilder->get('fooFormName')->getOptions());
        $this->assertInstanceOf(FormBuilder::class, $this->formBuilder->get('barFormName'));
        $this->assertSame(['bar2' => 'baz2'], $this->formBuilder->get('barFormName')->getOptions());
        $this->assertInstanceOf(FormBuilder::class, $this->formBuilder->get('_sort_by'));
        $this->assertInstanceOf(FormBuilder::class, $this->formBuilder->get('_sort_order'));
        $this->assertInstanceOf(FormBuilder::class, $this->formBuilder->get('_page'));
        $this->assertInstanceOf(FormBuilder::class, $this->formBuilder->get('_per_page'));
    }

    public function testBuildPagerWithSortBy(): void
    {
        $this->datagrid = new Datagrid($this->query, $this->pager, $this->formBuilder, [
            '_sort_by' => 'name',
        ]);

        $filter = $this->createMock(FilterInterface::class);
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

        $this->assertSame(['_sort_by' => 'name', 'foo' => null], $this->datagrid->getValues());
        $this->assertInstanceOf(FormBuilder::class, $this->formBuilder->get('fooFormName'));
        $this->assertSame(['bar' => 'baz'], $this->formBuilder->get('fooFormName')->getOptions());
        $this->assertInstanceOf(FormBuilder::class, $this->formBuilder->get('_sort_by'));
        $this->assertInstanceOf(FormBuilder::class, $this->formBuilder->get('_sort_order'));
        $this->assertInstanceOf(FormBuilder::class, $this->formBuilder->get('_page'));
        $this->assertInstanceOf(FormBuilder::class, $this->formBuilder->get('_per_page'));
    }
}
