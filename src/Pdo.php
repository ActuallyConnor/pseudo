<?php

namespace Pseudo;

use InvalidArgumentException;

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
     * @throws Exception
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

    public function setAttribute($attribute, $value): bool
    {
        throw new \RuntimeException('Not yet implemented');
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
     * @return PdoStatement
     * @throws Exception
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

        throw new Exception('Unable to convert query to PdoStatement');
    }

    /**
     * @param null $name
     * @return false|string
     * @throws Exception
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
     * @throws Exception
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

    public function errorCode(): ?string
    {
        throw new \RuntimeException('Not yet implemented');
    }

    public function errorInfo(): array
    {
        throw new \RuntimeException('Not yet implemented');
    }

    public function getAttribute(int $attribute): mixed
    {
        throw new \RuntimeException('Not yet implemented');
    }

    public function quote($string, $type = PDO::PARAM_STR): false|string
    {
        throw new \RuntimeException('Not yet implemented');
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
     * @param null $expectedResults
     * @param null $params
     */
    public function mock(string $sql, $expectedResults = null, $params = null): void
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

    public function getIterator()
    {

    }
}
