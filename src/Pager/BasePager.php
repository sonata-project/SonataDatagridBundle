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
abstract class BasePager implements PagerInterface
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
     * NEXT_MAJOR: Remove this property.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @var int
     */
    private $nbResults = 0;

    /**
     * NEXT_MAJOR: Remove this property.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @var int
     */
    private $cursor = 1;

    /**
     * NEXT_MAJOR: Remove this property.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @var array<string, mixed>
     */
    private $parameters = [];

    /**
     * NEXT_MAJOR: Remove this property.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @var int
     */
    private $currentMaxLink = 1;

    /**
     * NEXT_MAJOR: Remove this property.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @var int
     */
    private $maxRecordLimit = 0;

    /**
     * @var int
     */
    private $maxPageLinks = 0;

    /**
     * NEXT_MAJOR: Remove this property.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @var array
     */
    private $results = [];

    /**
     * NEXT_MAJOR: Remove this property.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @var int
     */
    private $resultsCounter = 0;

    /**
     * @var ProxyQueryInterface|null
     */
    private $query;

    /**
     * NEXT_MAJOR: Remove this property.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @var string[]
     */
    private $countColumn = ['id'];

    public function __construct(int $maxPerPage = 10)
    {
        $this->setMaxPerPage($maxPerPage);
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getCurrentMaxLink(): int
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return $this->currentMaxLink;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getMaxRecordLimit(): int
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return $this->maxRecordLimit;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function setMaxRecordLimit(int $limit): void
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        $this->maxRecordLimit = $limit;
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function getLinks(?int $nbLinks = null): array
    {
        if (null === $nbLinks) {
            $nbLinks = $this->getMaxPageLinks();
        }

        $links = [];
        $tmp = $this->page - (int) floor($nbLinks / 2);
        $check = $this->lastPage - $nbLinks + 1;
        $limit = $check > 0 ? $check : 1;
        $begin = $tmp > 0 ? (($tmp > $limit) ? $limit : $tmp) : 1;

        $i = $begin;

        while ($i < $begin + $nbLinks && $i <= $this->lastPage) {
            $links[] = $i++;
        }

        // NEXT_MAJOR: Remove this line.
        $this->currentMaxLink = \count($links) ? $links[\count($links) - 1] : 1;

        return $links;
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function haveToPaginate(): bool
    {
        $countResults = $this->countResults();

        return $this->getMaxPerPage() && $countResults > $this->getMaxPerPage();
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getCursor(): int
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return $this->cursor;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function setCursor(int $pos): void
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

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

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getObjectByCursor(int $pos): ?object
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        $this->setCursor($pos);

        return $this->getCurrent();
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getCurrent(): ?object
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return $this->retrieveObject($this->cursor);
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getNext(): ?object
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        if ($this->cursor + 1 > $this->nbResults) {
            return null;
        }

        return $this->retrieveObject($this->cursor + 1);
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getPrevious(): ?object
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        if ($this->cursor - 1 < 1) {
            return null;
        }

        return $this->retrieveObject($this->cursor - 1);
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getFirstIndice(): int
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        if (0 === $this->page) {
            return 1;
        }

        return ($this->page - 1) * $this->maxPerPage + 1;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getLastIndice(): int
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        if (0 === $this->page) {
            return $this->nbResults;
        }

        if ($this->page * $this->maxPerPage >= $this->nbResults) {
            return $this->nbResults;
        }

        return $this->page * $this->maxPerPage;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getNbResults(): int
    {
        return $this->nbResults;
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function getFirstPage(): int
    {
        return 1;
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function getPage(): int
    {
        return $this->page;
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function getNextPage(): int
    {
        return min($this->getPage() + 1, $this->getLastPage());
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function getPreviousPage(): int
    {
        return max($this->getPage() - 1, $this->getFirstPage());
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function setPage(int $page): void
    {
        $this->page = $page;

        if ($this->page <= 0) {
            // set first page, which depends on a maximum set
            $this->page = $this->getMaxPerPage() ? 1 : 0;
        }
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function getMaxPerPage(): int
    {
        return $this->maxPerPage;
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
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

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function getMaxPageLinks(): int
    {
        return $this->maxPageLinks;
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function setMaxPageLinks(int $maxPageLinks): void
    {
        $this->maxPageLinks = $maxPageLinks;
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function isFirstPage(): bool
    {
        return $this->getFirstPage() === $this->page;
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function isLastPage(): bool
    {
        return $this->page === $this->lastPage;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getParameters(): array
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return $this->parameters;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @param mixed $default
     *
     * @return mixed
     */
    public function getParameter(string $name, $default = null)
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return $this->parameters[$name] ?? $default;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function hasParameter(string $name): bool
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return isset($this->parameters[$name]);
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @param mixed $value
     */
    public function setParameter(string $name, $value): void
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        $this->parameters[$name] = $value;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @return mixed
     */
    public function current()
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        if (!$this->isIteratorInitialized()) {
            $this->initializeIterator();
        }

        return current($this->results);
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @return mixed
     */
    public function key()
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        if (!$this->isIteratorInitialized()) {
            $this->initializeIterator();
        }

        return key($this->results);
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @return mixed
     */
    public function next()
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        if (!$this->isIteratorInitialized()) {
            $this->initializeIterator();
        }

        --$this->resultsCounter;

        return next($this->results);
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     *
     * @return mixed
     */
    public function rewind()
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        if (!$this->isIteratorInitialized()) {
            $this->initializeIterator();
        }

        $this->resultsCounter = \count($this->results);

        return reset($this->results);
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function valid(): bool
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        if (!$this->isIteratorInitialized()) {
            $this->initializeIterator();
        }

        return $this->resultsCounter > 0;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function count(): int
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return $this->getNbResults();
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function serialize(): string
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        $vars = get_object_vars($this);
        unset($vars['query']);

        return serialize($vars);
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function unserialize($serialized): void
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        $array = unserialize($serialized);

        foreach ($array as $name => $values) {
            $this->$name = $values;
        }
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function getCountColumn(): array
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return $this->countColumn;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    public function setCountColumn(array $countColumn): array
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        return $this->countColumn = $countColumn;
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function setQuery(ProxyQueryInterface $query): void
    {
        $this->query = $query;
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    public function getQuery(): ?ProxyQueryInterface
    {
        return $this->query;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    final protected function setNbResults(int $nb): void
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        $this->nbResults = $nb;
    }

    /**
     * @final since sonata-project/datagrid-bundle 3.x.
     */
    final protected function setLastPage(int $page): void
    {
        $this->lastPage = $page;

        // NEXT_MAJOR: Remove this code.
        if ($this->getPage() > $page) {
            $this->setPage($page);
        }
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    final protected function resetIterator(): void
    {
        @trigger_error(sprintf(
            'The method "%s()" is deprecated since sonata-project/datagrid-bundle 3.x and will be removed in 4.0.',
            __METHOD__
        ), E_USER_DEPRECATED);

        $this->results = [];
        $this->resultsCounter = 0;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    private function isIteratorInitialized(): bool
    {
        return \count($this->results) > 0;
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    private function initializeIterator(): void
    {
        $this->results = $this->getResults();
        $this->resultsCounter = \count($this->results);
    }

    /**
     * NEXT_MAJOR: Remove this method.
     *
     * @deprecated since sonata-project/datagrid-bundle 3.x
     */
    private function retrieveObject(int $offset): ?object
    {
        $queryForRetrieve = clone $this->getQuery();

        \assert($queryForRetrieve instanceof ProxyQueryInterface);

        $queryForRetrieve
            ->setFirstResult($offset - 1)
            ->setMaxResults(1);

        $results = $queryForRetrieve->execute();

        return $results[0] ?? null;
    }
}
