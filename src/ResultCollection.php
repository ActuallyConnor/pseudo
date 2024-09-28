<?php

namespace Pseudo;

use Countable;
use Pseudo\Exceptions\PseudoException;
use Throwable;

class ResultCollection implements Countable
{
    private array $queries = [];

    public function count(): int
    {
        return count($this->queries);
    }

    public function addQuery(string $sql, ?array $params = null, mixed $results = null): void
    {
        $query = new ParsedQuery($sql);

        if (is_array($results)) {
            $storedResults = new Result($results, $params);
        } elseif ($results instanceof Result) {
            $storedResults = $results;
        } elseif (is_bool($results)) {
            $storedResults = $results;
        } elseif ($results instanceof Throwable) {
            throw $results;
        } else {
            $storedResults = new Result;
        }

        $this->queries[$query->getHash()] = $storedResults;
    }

    public function exists($sql): bool
    {
        $query = new ParsedQuery($sql);
        return isset($this->queries[$query->getHash()]);
    }

    /**
     * @throws PseudoException
     */
    public function getResult(string|ParsedQuery $query): Result|bool
    {
        if (!($query instanceof ParsedQuery)) {
            $query = new ParsedQuery($query);
        }
        $result = (isset($this->queries[$query->getHash()])) ? $this->queries[$query->getHash()] : null;
        if ($result instanceof Result) {
            return $result;
        } elseif (is_bool($result)) {
            return $result;
        } else {
            $message = "Attempting an operation on an un-mocked query is not allowed, the raw query: "
                . $query->getRawQuery();
            throw new PseudoException($message);
        }
    }
}
