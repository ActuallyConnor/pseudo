<?php

namespace Pseudo;

use Pseudo\Exceptions\LogicException;
use Pseudo\Exceptions\PseudoException;

class Result
{
    private array|bool $rows = [];
    private ?bool $executionResult = null;
    private bool $isParameterized = false;
    private string $errorCode;
    private string $errorInfo;
    private int $affectedRowCount = 0;
    private int $insertId = 0;
    private int $rowOffset = 0;
    private array $params = [];

    public function __construct(array|bool $rows = null, $params = null, $executionResult = null)
    {
        if (is_array($rows)) {
            if ($params) {
                $this->rows[$this->stringifyParameterSet($params)] = $rows;
                $this->isParameterized = true;
            } else {
                $this->rows = $rows;
            }
        } elseif (is_bool($rows)) {
            $this->rows = $rows;
        }

        $this->executionResult = $executionResult;
    }

    /**
     * @throws PseudoException
     */
    public function addRow(array $row, $params = null): void
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

    public function setParams($params, bool $parameterize = false): void
    {
        $this->params = $params;
        if ($parameterize) {
            $this->isParameterized = true;
        }
    }

    public function getRows(array $params = [])
    {
        if (is_bool($this->rows)) {
            return $this->rows;
        }

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
     * @param array $rows Rows to get row from
     *
     * @return false|array Next row (false if doesn't exist)
     */
    private function getRowIfExists(array $rows): false|array
    {
        if (!isset($rows[$this->rowOffset])) {
            return false;
        }

        return $rows[$this->rowOffset];
    }

    /**
     * Returns the next available row if it exists, otherwise returns false
     *
     * @return false|array
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


    public function setInsertId($insertId): void
    {
        $this->insertId = $insertId;
    }

    public function getInsertId(): int
    {
        return $this->insertId;
    }

    /**
     * @param $errorCode
     *
     * @throws PseudoException
     */
    public function setErrorCode($errorCode): void
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
     * @param $errorInfo
     */
    public function setErrorInfo($errorInfo): void
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

    public function setAffectedRowCount($affectedRowCount): void
    {
        $this->affectedRowCount = $affectedRowCount;
    }

    public function getAffectedRowCount(): int
    {
        return $this->affectedRowCount;
    }

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

    private function initializeParameterizedRows(string $parameterKey, array $row): void
    {
        if (empty($this->rows)) {
            $this->rows[$parameterKey][] = $row;
            $this->isParameterized = true;
        }
    }

    /**
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
