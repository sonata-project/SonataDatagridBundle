.. index::
    single: Installation
    single: Configuration

Installation
============

To begin, add the dependent bundles to the vendor/bundles directory. Add the following lines to the file deps:

.. code-block:: bash

    composer require sonata-project/datagrid-bundle

Now, add the new Bundle to ``bundles.php`` file::

    // config/bundles.php

    return [
        // ...
        Sonata\DatagridBundle\SonataDatagridBundle::class => ['all' => true],
    ];
