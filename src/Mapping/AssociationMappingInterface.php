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

namespace Sonata\DatagridBundle\Mapping;

interface AssociationMappingInterface extends FieldMappingInterface
{
    public const ONE_TO_ONE = 'association_mapping_type_one_to_one';
    public const MANY_TO_ONE = 'association_mapping_type_many_to_one';
    public const ONE_TO_MANY = 'association_mapping_type_one_to_many';
    public const MANY_TO_MANY = 'association_mapping_type_many_to_many';

    public function getMappingType(): ?string;

    public function getTargetModel(): ?string;
}
