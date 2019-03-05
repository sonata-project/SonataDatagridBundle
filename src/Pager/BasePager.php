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

namespace Sonata\DatagridBundle\Pager;

use Sonata\DatagridBundle\ProxyQuery\ProxyQueryInterface;

/**
 * @author Fabien Potencier <fabien.potencier@symfony-project.com>
 * @author Thomas Rabaix <thomas.rabaix@sonata-project.org>
 */
abstract class BasePager implements \Serializable, PagerInterface
{
    /**
     * @var int
     */
    private $page = 1;

    /**
     * @var int
     */
    private $maxPerPage = 0;

    /**
     * @var int
     */
    private $lastPage = 1;

    /**
     * @var int
     */
    private $nbResults = 0;

    /**
     * @var int
     */
    private $cursor = 1;

    /**
     * @var array
     */
    private $parameters = [];

    /**
     * @var int
     */
    private $currentMaxLink = 1;

    /**
     * @var int
     */
    private $maxRecordLimit = 0;

    /**
     * @var int
     */
    private $maxPageLinks = 0;

    /**
     * @var array
     */
    private $results = [];

    /**
     * @var int
     */
    private $resultsCounter = 0;

    /**
     * @var ProxyQueryInterface|null
     */
    private $query;

    /**
     * @var string[]
     */
    private $countColumn = ['id'];

    public function __construct(int $maxPerPage = 10)
    {
        $this->setMaxPerPage($maxPerPage);
    }

    public function getCurrentMaxLink(): int
    {
        return $this->currentMaxLink;
    }

    public function getMaxRecordLimit(): int
    {
        return $this->maxRecordLimit;
    }

    public function setMaxRecordLimit(int $limit): void
    {
        $this->maxRecordLimit = $limit;
    }

    public function getLinks(?int $nbLinks = null): array
    {
        if (null === $nbLinks) {
            $nbLinks = $this->getMaxPageLinks();
        }

        $links = [];
        $tmp = $this->page - (int) floor($nbLinks / 2);
        $check = $this->lastPage - $nbLinks + 1;
        $limit = $check > 0 ? $check : 1;
        $begin = $tmp > 0 ? ($tmp > $limit ? $limit : $tmp) : 1;

        $i = $begin;

        while ($i < $begin + $nbLinks && $i <= $this->lastPage) {
            $links[] = $i++;
        }

        $this->currentMaxLink = \count($links) ? $links[\count($links) - 1] : 1;

        return $links;
    }

    public function haveToPaginate(): bool
    {
        return $this->getMaxPerPage() && $this->getNbResults() > $this->getMaxPerPage();
    }

    public function getCursor(): int
    {
        return $this->cursor;
    }

    public function setCursor(int $pos): void
    {
        if ($pos < 1) {
            $this->cursor = 1;

            return;
        }

        if ($pos > $this->nbResults) {
            $this->cursor = $this->nbResults;

            return;
        }

        $this->cursor = $pos;
    }

    public function getObjectByCursor(int $pos): ?object
    {
        $this->setCursor($pos);

        return $this->getCurrent();
    }

    public function getCurrent(): ?object
    {
        return $this->retrieveObject($this->cursor);
    }

    public function getNext(): ?object
    {
        if ($this->cursor + 1 > $this->nbResults) {
            return null;
        }

        return $this->retrieveObject($this->cursor + 1);
    }

    public function getPrevious(): ?object
    {
        if ($this->cursor - 1 < 1) {
            return null;
        }

        return $this->retrieveObject($this->cursor - 1);
    }

    public function getFirstIndice(): int
    {
        if (0 === $this->page) {
            return 1;
        }

        return ($this->page - 1) * $this->maxPerPage + 1;
    }

    public function getLastIndice(): int
    {
        if (0 === $this->page) {
            return $this->nbResults;
        }

        if ($this->page * $this->maxPerPage >= $this->nbResults) {
            return $this->nbResults;
        }

        return $this->page * $this->maxPerPage;
    }

    public function getNbResults(): int
    {
        return $this->nbResults;
    }

    public function getFirstPage(): int
    {
        return 1;
    }

    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getNextPage(): int
    {
        return min($this->getPage() + 1, $this->getLastPage());
    }

    public function getPreviousPage(): int
    {
        return max($this->getPage() - 1, $this->getFirstPage());
    }

    public function setPage(int $page): void
    {
        if ($page <= 0) {
            // set first page, which depends on a maximum set
            $this->page = $this->getMaxPerPage() ? 1 : 0;

            return;
        }

        $this->page = $page;
    }

    public function getMaxPerPage(): int
    {
        return $this->maxPerPage;
    }

    public function setMaxPerPage(int $max): void
    {
        if ($max > 0) {
            $this->maxPerPage = $max;

            if (0 === $this->page) {
                $this->page = 1;
            }

            return;
        }

        if (0 === $max) {
            $this->maxPerPage = 0;
            $this->page = 0;

            return;
        }

        $this->maxPerPage = 1;

        if (0 === $this->page) {
            $this->page = 1;
        }
    }

    public function getMaxPageLinks(): int
    {
        return $this->maxPageLinks;
    }

    public function setMaxPageLinks(int $maxPageLinks): void
    {
        $this->maxPageLinks = $maxPageLinks;
    }

    public function isFirstPage(): bool
    {
        return $this->getFirstPage() === $this->page;
    }

    public function isLastPage(): bool
    {
        return $this->page === $this->lastPage;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * @param mixed $default
     *
     * @return mixed
     */
    public function getParameter(string $name, $default = null)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : $default;
    }

    public function hasParameter(string $name): bool
    {
        return isset($this->parameters[$name]);
    }

    /**
     * @param mixed $value
     */
    public function setParameter(string $name, $value): void
    {
        $this->parameters[$name] = $value;
    }

    /**
     * @return mixed
     */
    public function current()
    {
        if (!$this->isIteratorInitialized()) {
            $this->initializeIterator();
        }

        return current($this->results);
    }

    /**
     * @return mixed
     */
    public function key()
    {
        if (!$this->isIteratorInitialized()) {
            $this->initializeIterator();
        }

        return key($this->results);
    }

    /**
     * @return mixed
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
     * @return mixed
     */
    public function rewind()
    {
        if (!$this->isIteratorInitialized()) {
            $this->initializeIterator();
        }

        $this->resultsCounter = \count($this->results);

        return reset($this->results);
    }

    public function valid(): bool
    {
        if (!$this->isIteratorInitialized()) {
            $this->initializeIterator();
        }

        return $this->resultsCounter > 0;
    }

    public function count(): int
    {
        return $this->getNbResults();
    }

    public function serialize(): string
    {
        $vars = get_object_vars($this);
        unset($vars['query']);

        return serialize($vars);
    }

    public function unserialize($serialized): void
    {
        $array = unserialize($serialized);

        foreach ($array as $name => $values) {
            $this->$name = $values;
        }
    }

    public function getCountColumn(): array
    {
        return $this->countColumn;
    }

    public function setCountColumn(array $countColumn): array
    {
        return $this->countColumn = $countColumn;
    }

    public function setQuery(ProxyQueryInterface $query): void
    {
        $this->query = $query;
    }

    public function getQuery(): ?ProxyQueryInterface
    {
        return $this->query;
    }

    final protected function setNbResults(int $nb): void
    {
        $this->nbResults = $nb;
    }

    final protected function setLastPage(int $page): void
    {
        $this->lastPage = $page;

        if ($this->getPage() > $page) {
            $this->setPage($page);
        }
    }

    final protected function resetIterator(): void
    {
        $this->results = [];
        $this->resultsCounter = 0;
    }

    private function isIteratorInitialized(): bool
    {
        return \count($this->results) > 0;
    }

    private function initializeIterator(): void
    {
        $this->results = $this->getResults();
        $this->resultsCounter = \count($this->results);
    }

    private function retrieveObject(int $offset): ?object
    {
        $queryForRetrieve = clone $this->getQuery();

        \assert($queryForRetrieve instanceof ProxyQueryInterface);

        $queryForRetrieve
            ->setFirstResult($offset - 1)
            ->setMaxResults(1);

        $results = $queryForRetrieve->execute();

        return $results[0];
    }
}
