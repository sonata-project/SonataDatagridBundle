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

namespace Sonata\DatagridBundle\Tests\Fixtures\Field;

use Sonata\DatagridBundle\Field\BaseFieldDescription;

class FieldDescription extends BaseFieldDescription
{
    public function isIdentifier(): bool
    {
        throw new \BadFunctionCallException('Not implemented');
    }

    public function getValue(?object $object)
    {
        throw new \BadFunctionCallException('Not implemented');
    }
}
