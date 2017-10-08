<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DatagridBundle\Tests\Filter;

use Sonata\DatagridBundle\Filter\BaseFilter;
use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;

class FilterTest_Filter extends BaseFilter
{
    public function filter(ProxyQueryInterface $queryBuilder, $alias, $field, $value)
    {
    }

    public function apply($query, $value)
    {
    }

    public function getDefaultOptions()
    {
        return [
            'foo' => 'bar',
        ];
    }

    public function getRenderSettings()
    {
    }
}

class FilterTest extends \PHPUnit_Framework_TestCase
{
    public function testFilter()
    {
        $filter = new FilterTest_Filter();

        $this->assertEquals('text', $filter->getFieldType());
        $this->assertEquals(['required' => false], $filter->getFieldOptions());
        $this->assertNull($filter->getLabel());

        $options = [
            'label' => 'foo',
            'field_type' => 'integer',
            'field_options' => ['required' => true],
            'field_name' => 'name',
        ];

        $filter->setOptions($options);

        $this->assertEquals('foo', $filter->getOption('label'));
        $this->assertEquals('foo', $filter->getLabel());

        $expected = $options;
        $expected['foo'] = 'bar';

        $this->assertEquals($expected, $filter->getOptions());
        $this->assertEquals('name', $filter->getFieldName());

        $this->assertEquals('default', $filter->getOption('fake', 'default'));

        $filter->setValue(42);
        $this->assertEquals(42, $filter->getValue());

        $filter->setCondition('>');
        $this->assertEquals('>', $filter->getCondition());
    }

    public function testInitialize()
    {
        $filter = new FilterTest_Filter();
        $filter->initialize('name', [
            'field_name' => 'bar',
        ]);

        $this->assertEquals('name', $filter->getName());
        $this->assertEquals('bar', $filter->getOption('field_name'));
        $this->assertEquals('bar', $filter->getFieldName());
    }

    public function testLabel()
    {
        $filter = new FilterTest_Filter();
        $filter->setLabel('foo');

        $this->assertEquals('foo', $filter->getLabel());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testExceptionOnNonDefinedFieldName()
    {
        $filter = new FilterTest_Filter();

        $filter->getFieldName();
    }

    /**
     * @dataProvider isActiveData
     *
     * @param $expected
     * @param $value
     */
    public function testIsActive($expected, $value)
    {
        $filter = new FilterTest_Filter();
        $filter->setValue($value);

        $this->assertEquals($expected, $filter->isActive());
    }

    public function isActiveData()
    {
        return [
            [false, []],
            [false, ['value' => null]],
            [false, ['value' => '']],
            [false, ['value' => false]],
            [true, ['value' => 'active']],
        ];
    }
}
