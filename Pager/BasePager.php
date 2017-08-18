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

/**
 * Pager class.
 *
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
abstract class BasePager implements \Serializable, PagerInterface
{
    /**
     * @var int
     */
    protected $page = 1;

    /**
     * @var int
     */
    protected $maxPerPage = 0;

    /**
     * @var int
     */
    protected $lastPage = 1;

    /**
     * @var int
     */
    protected $nbResults = 0;

    /**
     * @var int
     */
    protected $cursor = 1;

    /**
     * @var array
     */
    protected $parameters = array();

    /**
     * @var int
     */
    protected $currentMaxLink = 1;

    /**
     * @var bool
     */
    protected $maxRecordLimit = false;

    /**
     * @var int
     */
    protected $maxPageLinks = 0;

    // used by iterator interface

    /**
     * @var array
     */
    protected $results = null;

    /**
     * @var int
     */
    protected $resultsCounter = 0;

    /**
     * @var ProxyQueryInterface
     */
    protected $query = null;

    /**
     * @var string[]
     */
    protected $countColumn = array('id');

    /**
     * @param int $maxPerPage Number of records to display per page
     */
    public function __construct($maxPerPage = 10)
    {
        $this->setMaxPerPage($maxPerPage);
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentMaxLink(): int
    {
        return $this->currentMaxLink;
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxRecordLimit(): int
    {
        return $this->maxRecordLimit;
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxRecordLimit(int $limit): void
    {
        $this->maxRecordLimit = $limit;
    }

    /**
     * {@inheritdoc}
     */
    public function getLinks(?int $nbLinks = null): array
    {
        if ($nbLinks == null) {
            $nbLinks = $this->getMaxPageLinks();
        }
        $links = array();
        $tmp = $this->page - floor($nbLinks / 2);
        $check = $this->lastPage - $nbLinks + 1;
        $limit = $check > 0 ? $check : 1;
        $begin = $tmp > 0 ? ($tmp > $limit ? $limit : $tmp) : 1;

        $i = $begin;
        while ($i < $begin + $nbLinks && $i <= $this->lastPage) {
            $links[] = $i++;
        }

        $this->currentMaxLink = count($links) ? $links[count($links) - 1] : 1;

        return $links;
    }

    /**
     * {@inheritdoc}
     */
    public function haveToPaginate(): bool
    {
        return $this->getMaxPerPage() && $this->getNbResults() > $this->getMaxPerPage();
    }

    /**
     * {@inheritdoc}
     */
    public function getCursor(): int
    {
        return $this->cursor;
    }

    /**
     * {@inheritdoc}
     */
    public function setCursor(int $pos): void
    {
        if ($pos < 1) {
            $this->cursor = 1;
        } else {
            if ($pos > $this->nbResults) {
                $this->cursor = $this->nbResults;
            } else {
                $this->cursor = $pos;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getObjectByCursor(int $pos)
    {
        $this->setCursor($pos);

        return $this->getCurrent();
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrent()
    {
        return $this->retrieveObject($this->cursor);
    }

    /**
     * {@inheritdoc}
     */
    public function getNext()
    {
        if ($this->cursor + 1 > $this->nbResults) {
            return null;
        }

        return $this->retrieveObject($this->cursor + 1);
    }

    /**
     * {@inheritdoc}
     */
    public function getPrevious()
    {
        if ($this->cursor - 1 < 1) {
            return null;
        }

        return $this->retrieveObject($this->cursor - 1);
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstIndice(): int
    {
        if ($this->page == 0) {
            return 1;
        }

        return ($this->page - 1) * $this->maxPerPage + 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastIndice(): int
    {
        if ($this->page == 0) {
            return $this->nbResults;
        }
        if ($this->page * $this->maxPerPage >= $this->nbResults) {
            return $this->nbResults;
        }

        return $this->page * $this->maxPerPage;
    }

    /**
     * {@inheritdoc}
     */
    public function getNbResults(): int
    {
        return $this->nbResults;
    }

    /**
     * {@inheritdoc}
     */
    public function getFirstPage(): int
    {
        return 1;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    /**
     * {@inheritdoc}
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * {@inheritdoc}
     */
    public function getNextPage(): int
    {
        return min($this->getPage() + 1, $this->getLastPage());
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousPage(): int
    {
        return max($this->getPage() - 1, $this->getFirstPage());
    }

    /**
     * {@inheritdoc}
     */
    public function setPage(int $page): void
    {
        $this->page = intval($page);

        if ($this->page <= 0) {
            // set first page, which depends on a maximum set
            $this->page = $this->getMaxPerPage() ? 1 : 0;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxPerPage(): int
    {
        return $this->maxPerPage;
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxPerPage(int $max): void
    {
        if ($max > 0) {
            $this->maxPerPage = $max;
            if ($this->page == 0) {
                $this->page = 1;
            }
        } else {
            if ($max == 0) {
                $this->maxPerPage = 0;
                $this->page = 0;
            } else {
                $this->maxPerPage = 1;
                if ($this->page == 0) {
                    $this->page = 1;
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMaxPageLinks(): int
    {
        return $this->maxPageLinks;
    }

    /**
     * {@inheritdoc}
     */
    public function setMaxPageLinks(int $maxPageLinks): void
    {
        $this->maxPageLinks = $maxPageLinks;
    }

    /**
     * {@inheritdoc}
     */
    public function isFirstPage(): bool
    {
        return 1 == $this->page;
    }

    /**
     * {@inheritdoc}
     */
    public function isLastPage(): bool
    {
        return $this->page == $this->lastPage;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getParameter(string $name, $default = null)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParameter(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    /**
     * {@inheritdoc}
     */
    public function setParameter(string $name, $value): void
    {
        $this->parameters[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function current()
    {
        if (!$this->isIteratorInitialized()) {
            $this->initializeIterator();
        }

        return current($this->results);
    }

    /**
     * {@inheritdoc}
     */
    public function key()
    {
        if (!$this->isIteratorInitialized()) {
            $this->initializeIterator();
        }

        return key($this->results);
    }

    /**
     * {@inheritdoc}
     */
    public function next()
    {
        if (!$this->isIteratorInitialized()) {
            $this->initializeIterator();
        }

        --$this->resultsCounter;

        return next($this->results);
    }

    /**
     * {@inheritdoc}
     */
    public function rewind()
    {
        if (!$this->isIteratorInitialized()) {
            $this->initializeIterator();
        }

        $this->resultsCounter = count($this->results);

        return reset($this->results);
    }

    /**
     * {@inheritdoc}
     */
    public function valid()
    {
        if (!$this->isIteratorInitialized()) {
            $this->initializeIterator();
        }

        return $this->resultsCounter > 0;
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->getNbResults();
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        $vars = get_object_vars($this);
        unset($vars['query']);

        return serialize($vars);
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $array = unserialize($serialized);

        foreach ($array as $name => $values) {
            $this->$name = $values;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getCountColumn(): array
    {
        return $this->countColumn;
    }

    /**
     * {@inheritdoc}
     */
    public function setCountColumn(array $countColumn): array
    {
        return $this->countColumn = $countColumn;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuery(ProxyQueryInterface $query): void
    {
        $this->query = $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuery(): ?ProxyQueryInterface
    {
        return $this->query;
    }

    /**
     * Sets the number of results.
     *
     * @param int $nb
     */
    protected function setNbResults(int $nb): void
    {
        $this->nbResults = $nb;
    }

    /**
     * Sets the last page number.
     *
     * @param int $page
     */
    protected function setLastPage(int $page): void
    {
        $this->lastPage = $page;

        if ($this->getPage() > $page) {
            $this->setPage($page);
        }
    }

    /**
     * Returns true if the properties used for iteration have been initialized.
     *
     * @return bool
     */
    protected function isIteratorInitialized(): bool
    {
        return null !== $this->results;
    }

    /**
     * Loads data into properties used for iteration.
     */
    protected function initializeIterator(): void
    {
        $this->results = $this->getResults();
        $this->resultsCounter = count($this->results);
    }

    /**
     * Empties properties used for iteration.
     */
    protected function resetIterator(): void
    {
        $this->results = null;
        $this->resultsCounter = 0;
    }

    /**
     * Retrieve the object for a certain offset.
     *
     * @param int $offset
     *
     * @return object
     */
    protected function retrieveObject(int $offset)
    {
        $queryForRetrieve = clone $this->getQuery();
        $queryForRetrieve
            ->setFirstResult($offset - 1)
            ->setMaxResults(1);

        $results = $queryForRetrieve->execute();

        return $results[0];
    }
}
