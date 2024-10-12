<?php

namespace Pseudo;

use ArrayIterator;
use Iterator;
use Pseudo\Exceptions\LogicException;
use Pseudo\Exceptions\PseudoException;
use ReflectionClass;
use ReflectionException;
use stdClass;

class PdoStatement extends \PDOStatement
{
    private Result $result;
    private int $fetchMode = \PDO::FETCH_BOTH; //DEFAULT FETCHMODE
    /**
     * @var array<int|string,mixed>
     */
    private array $boundParams = [];
    /**
     * @var array<int|string,mixed>
     */
    private array $boundColumns = [];

    /**
     * @var QueryLog
     */
    private QueryLog $queryLog;

    private string $statement;

    /**
     * @param mixed $result
     * @param QueryLog|null $queryLog
     * @param string $statement
     */
    public function __construct(mixed $result = null, QueryLog $queryLog = null, string $statement = '')
    {
        if (!($result instanceof Result)) {
            $result = new Result();
        }
        $this->result = $result;
        if (!($queryLog instanceof QueryLog)) {
            $queryLog = new QueryLog();
        }
        $this->queryLog = $queryLog;
        $this->statement = $statement;
    }

    public function setResult(Result|bool $result): void
    {
        if (is_bool($result)) {
            return;
        }

        $this->result = $result;
    }

    /**
     * @param array<int|string,mixed>|null $params
     *
     * @return bool
     * @throws LogicException
     */
    public function execute(array $params = null): bool
    {
        $params = array_merge((array) $params, $this->boundParams);
        $this->result->setParams($params, !empty($this->boundParams));
        $this->queryLog->addQuery($this->statement);

        if ($this->result->hasExecutionResult()) {
            return $this->result->getExecutionResult();
        }

        // TODO: I'm not a fan of exception-based logic, but I want to avoid a backwards incompatibility change here.
        // Perhaps we can address in the next major version update
        try {
            $success = (bool) $this->result->getRows($params ?: []);
        } catch (PseudoException) {
            $success = false;
        }


        return $success;
    }

    public function fetch($mode = null, $cursorOrientation = \PDO::FETCH_ORI_NEXT, $cursorOffset = 0): mixed
    {
        // scrolling cursors not implemented
        $row = $this->result->nextRow();
        if ($row) {
            return $this->processFetchedRow($row, $mode);
        }

        return false;
    }

    public function bindParam(
        $param,
        &$var,
        $type = \PDO::PARAM_STR,
        $maxLength = null,
        $driverOptions = null
    ): bool {
        $this->boundParams[$param] =& $var;

        return true;
    }

    public function bindColumn($column, &$var, $type = null, $maxLength = null, $driverOptions = null): bool
    {
        $this->boundColumns[$column] =& $var;

        return true;
    }

    public function bindValue($param, $value, $type = \PDO::PARAM_STR): bool
    {
        $this->boundParams[$param] = $value;

        return true;
    }

    public function rowCount(): int
    {
        return $this->result->getAffectedRowCount();
    }

    public function fetchColumn($column = 0): mixed
    {
        $row = $this->result->nextRow();
        if ($row) {
            $row = $this->processFetchedRow($row, \PDO::FETCH_NUM);

            return $row[$column];
        }

        return false;
    }

    /**
     * @param int $mode
     * @param mixed ...$args
     * @return array<int|string,mixed>
     * @throws PseudoException
     */
    public function fetchAll(int $mode = \PDO::FETCH_DEFAULT, mixed ...$args): array
    {
        $rows = $this->result->getRows() ?? [];
        $returnArray = [];
        foreach ($rows as $row) {
            $returnArray[] = $this->processFetchedRow($row, $mode);
        }

        return $returnArray;
    }

    /**
     * @param array<int|string,mixed> $row
     * @param int|null $fetchMode
     * @return mixed
     */
    private function processFetchedRow(array $row, ?int $fetchMode): mixed
    {
        $i = 0;
        switch ($fetchMode ?: $this->fetchMode) {
            case \PDO::FETCH_BOTH:
                $returnRow = [];
                $keys = array_keys($row);
                $c = 0;
                foreach ($keys as $key) {
                    $returnRow[$key] = $row[$key];
                    $returnRow[$c++] = $row[$key];
                }

                return $returnRow;
            case \PDO::FETCH_ASSOC:
                return $row;
            case \PDO::FETCH_NUM:
                return array_values($row);
            case \PDO::FETCH_OBJ:
                return (object) $row;
            case \PDO::FETCH_BOUND:
                if ($this->result->isOrdinalArray($this->boundColumns)) {
                    foreach ($this->boundColumns as &$column) {
                        $column = array_values($row)[++$i];
                    }
                } else {
                    foreach ($this->boundColumns as $columnName => &$column) {
                        $column = $row[$columnName];
                    }
                }

                return true;
            case \PDO::FETCH_COLUMN:
                $returnRow = array_values($row);

                return $returnRow[0];
            default:
                return null;
        }
    }

    /**
     * @param class-string|null $class
     * @param array<int|string,mixed> $constructorArgs
     *
     * @return object|false
     * @throws ReflectionException
     */
    public function fetchObject(?string $class = "stdClass", array $constructorArgs = []): object|false
    {
        if (is_null($class)) {
            return false;
        }

        $row = $this->result->nextRow();
        if ($row) {
            $reflect = new ReflectionClass($class);
            $obj = $reflect->newInstanceArgs($constructorArgs);
            foreach ($row as $key => $val) {
                $obj->$key = $val;
            }

            return $obj;
        }

        return false;
    }

    /**
     * @return string|null
     */
    public function errorCode(): ?string
    {
        return $this->result->getErrorCode();
    }

    /**
     * @return array<string>
     */
    public function errorInfo(): array
    {
        return [$this->result->getErrorInfo()];
    }

    /**
     * @return int
     * @throws PseudoException
     */
    public function columnCount(): int
    {
        $rows = $this->result->getRows();
        if ($rows) {
            $row = array_shift($rows);

            return count(array_keys($row));
        }

        return 0;
    }

    /**
     * @param int $mode
     * @param null $className
     * @param mixed ...$params
     *
     * @return bool|int
     */
    public function setFetchMode($mode, $className = null, ...$params): bool|int
    {
        $r = new ReflectionClass(new Pdo());
        $constants = $r->getConstants();
        $constantNames = array_keys($constants);
        $allowedConstantNames = array_filter($constantNames, function ($val) {
            return str_starts_with($val, 'FETCH_');
        });
        $allowedConstantVals = [];
        foreach ($allowedConstantNames as $name) {
            $allowedConstantVals[] = $constants[$name];
        }

        if (in_array($mode, $allowedConstantVals)) {
            $this->fetchMode = $mode;

            return 1;
        }

        return false;
    }

    /**
     * @return array<int|string,mixed>
     */
    public function getBoundParams(): array
    {
        return $this->boundParams;
    }

    /**
     * @return Iterator
     * @throws PseudoException
     */
    public function getIterator(): Iterator
    {
        return new ArrayIterator($this->fetchAll());
    }
}
