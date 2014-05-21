<?php
/*
 * This file is part of the Sonata package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */


namespace Sonata\DatagridBundle\Datagrid;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManager;
use Elastica\Query;
use FOS\ElasticaBundle\Finder\FinderInterface;
use FOS\ElasticaBundle\Manager\RepositoryManager;
use FOS\ElasticaBundle\Repository;
use Sonata\DatagridBundle\Facet\FacetFactoryInterface;
use Sonata\DatagridBundle\Filter\FilterFactoryInterface;
use Sonata\DatagridBundle\Pager\Elastica\Pager as ElasticaPager;
use Sonata\DatagridBundle\ProxyQuery\Elastica\ProxyQuery as ElasticaProxyQuery;
use Sonata\DatagridBundle\ProxyQuery\Elastica\QueryBuilder as ElasticaQueryBuilder;
use Sonata\DatagridBundle\Pager\Doctrine\Pager as DoctrinePager;
use Sonata\DatagridBundle\ProxyQuery\Doctrine\ProxyQuery as DoctrineProxyQuery;
use Doctrine\ORM\QueryBuilder as DoctrineQueryBuilder;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryInterface;


/**
 * Class DatagridFactory
 *
 * @package Sonata\DatagridBundle\Datagrid
 *
 * @author Hugo Briand <briand@ekino.com>
 */
class DatagridFactory implements DatagridFactoryInterface
{
    /**
     * @var FilterFactoryInterface
     */
    protected $filterFactory;

    /**
     * @var FacetFactoryInterface
     */
    protected $facetFactory;

    /**
    * @var FormFactoryInterface
     */
    protected $formFactory;

    /**
     * @var FinderInterface
     */
    protected $finder;

    /**
    * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param Registry               $registry
     * @param FinderInterface        $finder
     * @param string                 $class
     * @param FilterFactoryInterface $filterFactory
     * @param FacetFactoryInterface  $facetFactory
     * @param FormFactoryInterface   $formFactory
     */
    public function __construct(Registry $registry, FinderInterface $finder, $class, FilterFactoryInterface $filterFactory, FacetFactoryInterface $facetFactory, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $registry->getManagerForClass($class);
        $this->finder        = $finder;
        $this->filterFactory = $filterFactory;
        $this->facetFactory  = $facetFactory;
        $this->formFactory   = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatagrid($type, array $params = array())
    {
        switch ($type) {
            case 'elastica':
                $proxyQuery = new ElasticaProxyQuery(new ElasticaQueryBuilder(new Query(), $this->finder));
                $pager      = new ElasticaPager();
                break;
            case 'doctrine':
                $proxyQuery = new DoctrineProxyQuery(new DoctrineQueryBuilder($this->entityManager));
                $pager      = new DoctrinePager();
                break;
            default:
                throw new \RuntimeException(sprintf("Unsupported datagrid builder type '%s'; supported types are: 'doctrine', 'elastica'", $type));
                break;
        }

        $datagrid = new Datagrid($proxyQuery, $pager, $this->formFactory->createBuilder(), $params);

        return $datagrid;
    }

    /**
     * {@inheritdoc}
     */
    public function getDatagridBuilder($type, array $options = array())
    {
        return new DatagridBuilder($this->getDatagrid($type, $options), $this->filterFactory, $this->facetFactory);
    }
}