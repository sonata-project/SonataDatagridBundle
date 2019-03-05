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

namespace Sonata\DatagridBundle\Tests\ProxyQuery;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Sonata\DatagridBundle\ProxyQuery\BaseProxyQuery;

/**
 * @author Romain Mouillard <romain.mouillard@gmail.com>
 */
class BaseProxyQueryTest extends TestCase
{
    /**
     * Test calling undefined method on proxy query object will also try it on its query builder.
     */
    public function testFallbackOnQuerybuilder(): void
    {
        if (!class_exists(Query::class)) {
            $this->markTestSkipped("Doctrine ORM doesn't seem to be installed");
        }

        $queryBuilder = $this->createMock(QueryBuilder::class);

        $queryBuilder->expects($this->any())
            ->method('getType')
            ->will($this->returnValue('foobar'));

        $proxyQuery = $this->getMockBuilder(BaseProxyQuery::class)
            ->setConstructorArgs([$queryBuilder])
            ->getMockForAbstractClass();

        $this->assertSame('foobar', $proxyQuery->getType());
    }
}
