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

namespace Sonata\DatagridBundle\Filter;

/**
 * Interface FilterFactoryInterface.
 */
interface FilterFactoryInterface
{
    /**
     * @param string $name
     * @param string $type
     * @param array  $options
     *
     * @return mixed
     */
    public function create(string $name, string $type, array $options = []);
}
