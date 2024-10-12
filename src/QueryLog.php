<?php

namespace Pseudo;

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use Traversable;

/**
 * @implements IteratorAggregate<int|string,ParsedQuery>
 * @implements ArrayAccess<int|string,ParsedQuery>
 */
class QueryLog implements IteratorAggregate, ArrayAccess, Countable
{
    /**
     * @var array<int|string,ParsedQuery>
     */
    private array $queries = [];

    public function count(): int
    {
        return count($this->queries);
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->queries);
    }

    public function offsetExists($offset): bool
    {
        return isset($this->queries[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        if (!$this->offsetExists($offset)) {
            throw new InvalidArgumentException("Offset $offset does not exist");
        }

        return $this->queries[$offset];
    }

    public function offsetSet($offset, $value): void
    {
        $this->queries[$offset] = $value;
    }

    public function offsetUnset($offset): void
    {
        unset($this->queries[$offset]);
    }

    public function addQuery(string $sql): void
    {
        $this->queries[] = new ParsedQuery($sql);
    }

    /**
     * @return array<int|string,ParsedQuery>
     */
    public function getQueries(): array
    {
        return $this->queries;
    }
}
