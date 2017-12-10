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

namespace Sonata\DatagridBundle\Tests\Pager\Doctrine;

use PHPUnit\Framework\TestCase;
use Sonata\DatagridBundle\Pager\Doctrine\Pager;

/**
 * @author Romain Mouillard <romain.mouillard@gmail.com>
 */
class PagerTest extends TestCase
{
    /**
     * @var Pager
     */
    private $pager;

    protected function setUp(): void
    {
        $this->pager = new Pager();

        if (!class_exists('Doctrine\ORM\Query')) {
            $this->markTestSkipped("Doctrine ORM doesn't seem to be installed");
        }
    }

    /**
     * Test get results method retuns query results.
     */
    public function testGetResults(): void
    {
        $query = $this->createMock('Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface');

        $object1 = new \stdClass();
        $object1->foo = 'bar1';

        $object2 = new \stdClass();
        $object2->foo = 'bar2';

        $object3 = new \stdClass();
        $object3->foo = 'bar3';

        $expectedObjects = [$object1, $object2, $object3];

        $query->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($expectedObjects));

        $this->pager->setQuery($query);

        $this->assertEquals($expectedObjects, $this->pager->getResults());
    }
}
