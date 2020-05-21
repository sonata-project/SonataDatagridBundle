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

namespace Sonata\DatagridBundle\Tests\Fixtures\Mapping;

use Sonata\DatagridBundle\Mapping\FieldMappingInterface;

class FieldMapping implements FieldMappingInterface
{
    /**
     * @var string|null
     */
    protected $fieldName;

    public function setFieldName(?string $fieldName): void
    {
        $this->fieldName = $fieldName;
    }

    public function getFieldName(): ?string
    {
        return $this->fieldName;
    }
}
