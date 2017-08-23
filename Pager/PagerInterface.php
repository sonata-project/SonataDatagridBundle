<?php

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sonata\DatagridBundle\Pager;

use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;

interface PagerInterface extends \Iterator, \Countable, \Serializable
{
    /**
     * Initialize the Pager.
     */
    public function init();

    /**
     * Returns the maximum number of results per page.
     *
     * @return int
     */
    public function getMaxPerPage();

    /**
     * Sets the maximum number of results per page.
     *
     * @param int $max
     */
    public function setMaxPerPage($max);

    /**
     * Sets the current page.
     *
     * @param int $page
     */
    public function setPage($page);

    /**
     * Set query.
     *
     * @param mixed $query
     */
    public function setQuery($query);

    /**
     * Returns an array of results on the given page.
     *
     * @return array
     */
    public function getResults();

    /**
     * Returns the first page number.
     *
     * @return int
     */
    public function getFirstPage();

    /**
     * Returns the last page number.
     *
     * @return int
     */
    public function getLastPage();

    /**
     * Returns the current page.
     *
     * @return int
     */
    public function getPage();

    /**
     * Returns the next page.
     *
     * @return int
     */
    public function getNextPage();

    /**
     * Returns the previous page.
     *
     * @return int
     */
    public function getPreviousPage();

    /**
     * Returns the current pager's max link.
     *
     * @return int
     */
    public function getCurrentMaxLink();

    /**
     * Returns the current pager's max record limit.
     *
     * @return int
     */
    public function getMaxRecordLimit();

    /**
     * Sets the current pager's max record limit.
     *
     * @param int $limit
     */
    public function setMaxRecordLimit($limit);

    /**
     * Returns an array of page numbers to use in pagination links.
     *
     * @param int $nbLinks The maximum number of page numbers to return
     *
     * @return array
     */
    public function getLinks($nbLinks = null);

    /**
     * Returns true if the current query requires pagination.
     *
     * @return bool
     */
    public function haveToPaginate();

    /**
     * Returns the current cursor.
     *
     * @return int
     */
    public function getCursor();

    /**
     * Sets the current cursor.
     *
     * @param int $pos
     */
    public function setCursor($pos);

    /**
     * Returns an object by cursor position.
     *
     * @param int $pos
     *
     * @return mixed
     */
    public function getObjectByCursor($pos);

    /**
     * Returns the current object.
     *
     * @return mixed
     */
    public function getCurrent();

    /**
     * Returns the next object.
     *
     * @return mixed|null
     */
    public function getNext();

    /**
     * Returns the previous object.
     *
     * @return mixed|null
     */
    public function getPrevious();

    /**
     * Returns the first index on the current page.
     *
     * @return int
     */
    public function getFirstIndice();

    /**
     * Returns the last index on the current page.
     *
     * @return int
     */
    public function getLastIndice();

    /**
     * Returns the number of results.
     *
     * @return int
     */
    public function getNbResults();

    /**
     * Returns the maximum number of page numbers.
     *
     * @return int
     */
    public function getMaxPageLinks();

    /**
     * Sets the maximum number of page numbers.
     *
     * @param int $maxPageLinks
     */
    public function setMaxPageLinks($maxPageLinks);

    /**
     * Returns true if on the first page.
     *
     * @return bool
     */
    public function isFirstPage();

    /**
     * Returns true if on the last page.
     *
     * @return bool
     */
    public function isLastPage();

    /**
     * Returns the current pager's parameter holder.
     *
     * @return array
     */
    public function getParameters();

    /**
     * Returns a parameter.
     *
     * @param string $name
     * @param mixed  $default
     *
     * @return mixed
     */
    public function getParameter($name, $default = null);

    /**
     * Checks whether a parameter has been set.
     *
     * @param string $name
     *
     * @return bool
     */
    public function hasParameter($name);

    /**
     * Sets a parameter.
     *
     * @param string $name
     * @param mixed  $value
     */
    public function setParameter($name, $value);

    /**
     * @return array
     */
    public function getCountColumn();

    /**
     * @param array $countColumn
     *
     * @return array
     */
    public function setCountColumn(array $countColumn);

    /**
     * @return ProxyQueryInterface
     */
    public function getQuery();
}
