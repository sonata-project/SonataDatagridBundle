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

namespace Sonata\DatagridBundle\Tests\Filter;

use PHPUnit\Framework\TestCase;
use Sonata\DatagridBundle\Filter\BaseFilter;
use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;

class FilterTest_Filter extends BaseFilter
{
    public function filter(ProxyQueryInterface $queryBuilder, string $alias, string $field, string $value): void
    {
    }

    public function apply($query, $value): void
    {
    }

    public function getDefaultOptions(): array
    {
        return [
            'foo' => 'bar',
        ];
    }

    public function getRenderSettings(): array
    {
        return [];
    }
}

class FilterTest extends TestCase
{
    public function testFilter(): void
    {
        $filter = new FilterTest_Filter();

        $this->assertSame('text', $filter->getFieldType());
        $this->assertSame(['required' => false], $filter->getFieldOptions());
        $this->assertNull($filter->getLabel());

        $options = [
            'label' => 'foo',
            'field_type' => 'integer',
            'field_options' => ['required' => true],
            'field_name' => 'name',
        ];

        $filter->setOptions($options);

        $this->assertSame('foo', $filter->getOption('label'));
        $this->assertSame('foo', $filter->getLabel());

        $expected = $options;
        $expected = ['foo' => 'bar'] + $expected;

        $this->assertSame($expected, $filter->getOptions());
        $this->assertSame('name', $filter->getFieldName());

        $this->assertSame('default', $filter->getOption('fake', 'default'));

        $filter->setValue(42);
        $this->assertSame(42, $filter->getValue());

        $filter->setCondition('>');
        $this->assertSame('>', $filter->getCondition());
    }

    public function testInitialize(): void
    {
        $filter = new FilterTest_Filter();
        $filter->initialize('name', [
            'field_name' => 'bar',
        ]);

        $this->assertSame('name', $filter->getName());
        $this->assertSame('bar', $filter->getOption('field_name'));
        $this->assertSame('bar', $filter->getFieldName());
    }

    public function testLabel(): void
    {
        $filter = new FilterTest_Filter();
        $filter->setLabel('foo');

        $this->assertSame('foo', $filter->getLabel());
    }

    public function testExceptionOnNonDefinedFieldName(): void
    {
        $this->expectException(\RuntimeException::class);

        $filter = new FilterTest_Filter();

        $filter->getFieldName();
    }

    /**
     * @dataProvider isActiveData
     *
     * @param $expected
     * @param $value
     */
    public function testIsActive($expected, $value): void
    {
        $filter = new FilterTest_Filter();
        $filter->setValue($value);

        $this->assertSame($expected, $filter->isActive());
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
