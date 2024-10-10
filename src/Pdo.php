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
     * @throws PseudoException|Throwable
     */
    public function prepare($query, $options = null): PdoStatement
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
     * @return false|string
     * @throws PseudoException
     */
    public function lastInsertId($name = null): false|string
    {
        $result = $this->getLastResult();

        if (!$result) {
            return false;
        }

        return $result->getInsertId();
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
     * @param $filePath
     */
    public function load($filePath): void
    {
        $this->mockedQueries = unserialize(file_get_contents($filePath));
    }

    /**
     * @param string $sql
     * @param array|null $params
     * @param mixed $expectedResults
     * @param bool $executionResult
     */
    public function mock(
        string $sql,
        ?array $params = null,
        mixed $expectedResults = null,
        bool $executionResult = true
    ): void {
        $this->mockedQueries->addQuery($sql, $params, $expectedResults, $executionResult);
    }

    /**
     * @return ResultCollection
     */
    public function getMockedQueries(): ResultCollection
    {
        return $this->mockedQueries;
    }
}
