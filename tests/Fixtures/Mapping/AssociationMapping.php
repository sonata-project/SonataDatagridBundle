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

use Sonata\DatagridBundle\Mapping\AssociationMappingInterface;

class AssociationMapping extends FieldMapping implements AssociationMappingInterface
{
    /**
     * @var string|null
     */
    protected $mappingType;

    /**
     * @var string|null
     */
    protected $targetModel;

    public function setMappingType(?string $mappingType): void
    {
        $this->mappingType = $mappingType;
    }

    public function getMappingType(): ?string
    {
        return $this->mappingType;
    }

    public function setTargetModel(?string $targetModel): void
    {
        $this->targetModel = $targetModel;
    }

    public function getTargetModel(): ?string
    {
        return $this->targetModel;
    }
}
