<?php

namespace Pseudo;

use InvalidArgumentException;
use Pseudo\Exceptions\PseudoException;
use Throwable;

class Pdo extends \PDO
{
    private ResultCollection $mockedQueries;
    private bool $inTransaction = false;
    private QueryLog $queryLog;

    /**
     * @param ResultCollection|null $collection
     */
    public function __construct(
        ResultCollection $collection = null
    ) {
        $this->mockedQueries = $collection ?? new ResultCollection();
        $this->queryLog = new QueryLog();
    }

    /**
     * @param ParsedQuery|string $query
     * @param array<int|string,mixed> $options
     * @return PdoStatement
     * @throws PseudoException
     * @throws Throwable
     */
    public function prepare(ParsedQuery|string $query, array $options = []): PdoStatement
    {
        $result = $this->mockedQueries->getResult($query);

        return new PdoStatement($result, $this->queryLog, $query);
    }

    public function beginTransaction(): bool
    {
        if (!$this->inTransaction) {
            $this->inTransaction = true;

            return true;
        }

        return false;
    }

    public function commit(): bool
    {
        if ($this->inTransaction()) {
            $this->inTransaction = false;

            return true;
        }

        return false;
    }

    public function rollBack(): bool
    {
        if ($this->inTransaction()) {
            $this->inTransaction = false;

            return true;
        }

        return false;
    }

    public function inTransaction(): bool
    {
        return $this->inTransaction;
    }

    public function exec($statement): false|int
    {
        $result = $this->query($statement);

        return $result->rowCount();
    }

    /**
     * @param string $query
     * @param int|null $fetchMode
     * @param mixed ...$fetchModeArgs
     *
     * @return PdoStatement
     * @throws PseudoException|Throwable
     */
    public function query(string $query, ?int $fetchMode = null, mixed ...$fetchModeArgs): PdoStatement
    {
        if ($this->mockedQueries->exists($query)) {
            $result = $this->mockedQueries->getResult($query);

            $this->queryLog->addQuery($query);
            $statement = new PdoStatement();
            $statement->setResult($result);

            return $statement;
        }

        throw new PseudoException('Unable to convert query to PdoStatement');
    }

    /**
     * @param null $name
     *
     * @return string|false
     * @throws PseudoException|Throwable
     */
    public function lastInsertId($name = null): string|false
    {
        return $this->getLastResult() !== false ? $this->getLastResult()->getInsertId() : false;
    }

    /**
     * @return Result|false
     * @throws PseudoException|Throwable
     */
    private function getLastResult(): Result|false
    {
        try {
            $lastQuery = $this->queryLog[count($this->queryLog) - 1];
        } catch (InvalidArgumentException) {
            return false;
        }

        return $this->mockedQueries->getResult($lastQuery);
    }

    /**
     * @param string $filePath
     */
    public function save(string $filePath): void
    {
        file_put_contents($filePath, serialize($this->mockedQueries));
    }

    /**
     * @param string $filePath
     */
    public function load(string $filePath): void
    {
        $this->mockedQueries = unserialize(file_get_contents($filePath));
    }

    /**
     * @param string $sql
     * @param array<int|string,mixed>|null $params
     * @param mixed $expectedResults
     */
    public function mock(string $sql, ?array $params = null, mixed $expectedResults = null): void
    {
        $this->mockedQueries->addQuery($sql, $params, $expectedResults);
    }

    /**
     * @return ResultCollection
     */
    public function getMockedQueries(): ResultCollection
    {
        return $this->mockedQueries;
    }
}
