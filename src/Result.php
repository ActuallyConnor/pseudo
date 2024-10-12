<?php

namespace Pseudo;

use Pseudo\Exceptions\LogicException;
use Pseudo\Exceptions\PseudoException;

class Result
{
    /**
     * @var array<int|string,array<int|string,mixed>>
     */
    private array $rows = [];
    private ?bool $executionResult;
    private bool $isParameterized = false;
    private string $errorCode;
    private string $errorInfo;
    private int $affectedRowCount = 0;
    private int $insertId = 0;
    private int $rowOffset = 0;
    /**
     * @var array<int|string,mixed>
     */
    private array $params = [];

    /**
     * @param  array<int|string,array<int|string,mixed>>|null  $rows
     * @param  array<int|string,mixed>|null  $params
     * @param  bool|null  $executionResult
     */
    public function __construct(?array $rows = null, ?array $params = null, ?bool $executionResult = null)
    {
        if (is_array($rows)) {
            if ($params) {
                $this->rows[$this->stringifyParameterSet($params)] = $rows;
                $this->isParameterized                             = true;
            } else {
                $this->rows = $rows;
            }
        }

        $this->executionResult = $executionResult;
    }

    /**
     * @param  array<int|string,mixed>  $row
     * @param  array<int|string,mixed>  $params
     *
     * @return void
     * @throws PseudoException
     */
    public function addRow(array $row, ?array $params = null): void
    {
        if (empty($row)) {
            return;
        }

        if ($params) {
            $parameterKey = $this->stringifyParameterSet($params);

            if ($this->isParameterized) {
                $this->rows[$parameterKey][] = $row;
            } else {
                $this->initializeParameterizedRows($parameterKey, $row);
            }
        } else {
            $this->addNonParameterizedRow($row);
        }
    }

    /**
     * @param  array<int|string,mixed>  $params
     * @param  bool  $parameterize
     *
     * @return void
     */
    public function setParams(array $params, bool $parameterize = false): void
    {
        $this->params = $params;
        if ($parameterize) {
            $this->isParameterized = true;
        }
    }

    /**
     * @param  array<int|string,mixed>  $params
     *
     * @return mixed
     * @throws PseudoException
     */
    public function getRows(array $params = []): mixed
    {
        if (!empty($this->params) && empty($params)) {
            $params = $this->params;
        }

        if ($params) {
            if ($this->isParameterized) {
                if (isset($this->rows[$this->stringifyParameterSet($params)])) {
                    return $this->rows[$this->stringifyParameterSet($params)];
                } else {
                    return [];
                }
            }

            throw new PseudoException("Cannot get rows with parameters on a non-parameterized result");
        } else {
            if (!$this->isParameterized && isset($this->rows)) {
                return $this->rows;
            }
            throw new PseudoException("Cannot get rows without parameters on a parameterized result");
        }
    }

    /**
     * Returns the next row if it exists, otherwise returns false
     *
     * @param  array<int|string,mixed>  $rows  Rows to get row from
     *
     * @return false|array<int|string,mixed> Next row (false if it doesn't exist)
     */
    private function getRowIfExists(array $rows): false|array
    {
        if (!isset($rows[$this->rowOffset])) {
            return false;
        }

        /** @var array<int|string,mixed> $row */
        $row = $rows[$this->rowOffset];

        return $row;
    }

    /**
     * Returns the next available row if it exists, otherwise returns false
     *
     * @return false|array<int|string,mixed>
     */
    public function nextRow(): false|array
    {
        if (empty($this->rows)) {
            return false;
        }

        if ($this->isParameterized) {
            $row = $this->getRowIfExists($this->rows[$this->stringifyParameterSet($this->params)]);
        } else {
            $row = $this->getRowIfExists($this->rows);
        }

        if ($row) {
            $this->rowOffset++;
        }

        return $row;
    }


    public function setInsertId(int $insertId): void
    {
        $this->insertId = $insertId;
    }

    public function getInsertId(): int
    {
        return $this->insertId;
    }

    /**
     * @param  string  $errorCode
     *
     * @throws PseudoException
     */
    public function setErrorCode(string $errorCode): void
    {
        if (ctype_alnum($errorCode) && strlen($errorCode) == 5) {
            $this->errorCode = $errorCode;
        } else {
            throw new PseudoException("Error codes must be in ANSI SQL standard format");
        }
    }

    /**
     * @return string
     */
    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    /**
     * @param  string  $errorInfo
     */
    public function setErrorInfo(string $errorInfo): void
    {
        $this->errorInfo = $errorInfo;
    }

    /**
     * @return string
     */
    public function getErrorInfo(): string
    {
        return $this->errorInfo;
    }

    public function setAffectedRowCount(int $affectedRowCount): void
    {
        $this->affectedRowCount = $affectedRowCount;
    }

    public function getAffectedRowCount(): int
    {
        return $this->affectedRowCount;
    }

    /**
     * @param  array<int|string,mixed>  $arr
     *
     * @return bool
     */
    public function isOrdinalArray(array $arr): bool
    {
        return !(is_string(key($arr)));
    }

    public function reset(): void
    {
        $this->rowOffset = 0;
    }

    /**
     * @return bool
     */
    public function hasExecutionResult(): bool
    {
        return isset($this->executionResult);
    }

    /**
     * @return bool
     * @throws LogicException
     */
    public function getExecutionResult(): bool
    {
        if (!isset($this->executionResult)) {
            throw new LogicException('Execution result is not set');
        }

        return $this->executionResult;
    }

    /**
     * @param  array<int|string,mixed>  $params
     *
     * @return string
     */
    private function stringifyParameterSet(array $params): string
    {
        if ($this->isOrdinalArray($params)) {
            return implode(',', $params);
        } else {
            $returnArray = [];
            foreach ($params as $key => $value) {
                $returnArray[] = $key;
                $returnArray[] = $value;
            }

            return implode(',', $returnArray);
        }
    }

    /**
     * @param  string  $parameterKey
     * @param  array<int|string,mixed>  $row
     *
     * @return void
     */
    private function initializeParameterizedRows(string $parameterKey, array $row): void
    {
        if (empty($this->rows)) {
            $this->rows[$parameterKey][] = $row;
            $this->isParameterized       = true;
        }
    }

    /**
     * @param  array<int|string,mixed>  $row
     *
     * @return void
     * @throws PseudoException
     */
    private function addNonParameterizedRow(array $row): void
    {
        if ($this->isParameterized) {
            throw new PseudoException("Cannot mix parameterized and non-parameterized rowsets");
        }

        $this->rows[] = $row;
    }
}
