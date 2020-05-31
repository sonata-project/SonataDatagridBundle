UPGRADE FROM 3.x to 4.0
=======================

## Added `Sonata\DatagridBundle\Field\FieldDescriptionInterface` and `Sonata\DatagridBundle\Field\BaseFieldDescription`

They are similar to `Sonata\AdminBundle\Admin\FieldDescriptionInterface`
and `Sonata\AdminBundle\Admin\BaseFieldDescription` except that

- `name` is now required to create a new FieldDescription.
- `getFieldMapping` and `setFieldMapping` now use an `FieldMappingInterface`.
- `getAssociationMapping` and `setAssociationMapping` now use an `AssociationMappingInterface`.
- `getParentAssociationMappings` and `setParentAssociationMappings` now use an `AssociationMappingInterface`.
- `FieldDescriptionInterface::getFieldName` were removed
in favor of `FieldMappingInterface::getFieldName`.
- `FieldDescriptionInterface::setFieldName` were removed
in favor of `FieldMappingInterface::setFieldName`.
- `FieldDescriptionInterface::getMappingType` were removed
in favor of `FieldMappingInterface::getMappingType`.
- `FieldDescriptionInterface::setMappingType` were removed
in favor of `FieldMappingInterface::setMappingType`.
- `FieldDescriptionInterface::getLabel` was removed
in favor of `FieldDescriptionInterface::getOption('label')`.
- `FieldDescriptionInterface::getTranslationDomain` was removed
in favor of `FieldDescriptionInterface::getOption('translation_domain')`.
- `FieldDescriptionInterface::isSortable` was removed
in favor of `FieldDescriptionInterface::getOption('sortable')`.
- `FieldDescriptionInterface::getSortFieldMapping` was removed.
- `FieldDescriptionInterface::getSortParentAssociationMapping` was removed.
- All admin-related method was not added.

`Sonata\AdminBundle\Admin\FieldDescriptionInterface` will extend this interface in the next major.

## Changed

- `Sonata\DatagridBundle\ProxyQuery\BaseProxyQuery`:
Moved every DoctrineORM-related methods to `Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery`.

- `Sonata\DatagridBundle\Datagrid\DatagridInterface`:
Must now implement `hasDisplayableFilters`, `getSortParameters` and `getPaginationParameters` methods.

## Removed

- `Sonata\DatagridBundle\ProxyQuery\Elastica\ProxyQuery`.
- `Sonata\DatagridBundle\Pager\Elastica\Pager`.

## Deprecated

- `Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery`.
- `Sonata\DatagridBundle\Pager\Doctrine\Pager`.
