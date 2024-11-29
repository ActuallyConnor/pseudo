<?php

namespace Pseudo\UnitTest;

use PHPUnit\Framework\TestCase;
use Pseudo\Exceptions\LogicException;
use Pseudo\Exceptions\PseudoException;
use Pseudo\Result;

class ResultTest extends TestCase
{
    public function testSetErrorCode()
    {
        $r = new Result;
        $r->setErrorCode("HY000");
        $this->assertEquals("HY000", $r->getErrorCode());
        $this->expectException(PseudoException::class);
        $r->setErrorCode("121");
    }

    public function testNextRow()
    {
        $row1 = [
            'id' => 1,
            'foo' => 'bar',
        ];
        $row2 = [
            'id' => 2,
            'foo' => 'baz'
        ];
        $r = new Result();
        $r->addRow($row1);
        $r->addRow($row2);

        $this->assertEquals($row1, $r->nextRow());
        $this->assertEquals($row2, $r->nextRow());
        $this->assertFalse($r->nextRow());
    }

    public function testAddRow()
    {
        $row = [
            'id' => 1,
            'foo' => 'bar'
        ];
        $params = [
            'bar'
        ];

        $r = new Result;
        $r->addRow($row);
        $this->assertCount(1, $r->getRows());

        $r = new Result;
        $r->addRow($row, $params);
        $this->assertCount(1, $r->getRows($params));
    }

    public function testReset()
    {
        $row = [
            'id' => 1,
            'foo' => 'bar'
        ];
        $r = new Result();
        $r->addRow($row);
        $this->assertEquals($row, $r->nextRow());
        $this->assertEquals(null, $r->nextRow());
        $r->reset();
        $this->assertEquals($row, $r->nextRow());
    }

    public function testSetParametersParameterized(): void
    {
        $result = new Result();
        $result->setParams(['param'], true);

        $this->assertEquals([], $result->getRows());
    }

    public function testSetParametersNotParameterized(): void
    {
        $result = new Result();
        $result->setParams(['param']);

        $this->expectException(PseudoException::class);
        $result->getRows();
    }

    public function testEmptyRowProvided(): void
    {
        $result = new Result();
        $result->addRow([]);

        $this->assertCount(0, $result->getRows());
    }

    public function testSetAffectedRowCount(): void
    {
        $query = "SELECT * FROM test";
        $rows = [['id' => 1], ['id' => 2], ['id' => 3]];
        $result = new Result($rows);
        $result->setAffectedRowCount(count($rows));
        $this->assertEquals(3, $result->getAffectedRowCount());
    }

    public function testGetNextRowWhenRowsNotSet(): void
    {
        $result = new Result();
        $this->assertFalse($result->nextRow());
    }

    public function testGetNexRowWhenParameterized(): void
    {
        $result = new Result();
        $result->setParams(['param'], true);

        $result->addRow([['id' => 1], ['id' => 2], ['id' => 3]], ['param']);
        $result->addRow([['id' => 1], ['id' => 2], ['id' => 3]], ['param']);

        $this->assertEquals(
            [
                ['id' => 1],
                ['id' => 2],
                ['id' => 3]
            ],
            $result->nextRow()
        );
    }

    public function testFailToAddNonParameterizedRowToParameterizedResults(): void
    {
        $result = new Result();
        $result->setParams(['param'], true);

        $this->expectException(PseudoException::class);
        $result->addRow([['id' => 1], ['id' => 2], ['id' => 3]]);
    }

    public function testFailToGetUnsetExecutionResult(): void
    {
        $result = new Result();

        $this->expectException(LogicException::class);
        $result->getExecutionResult();
    }
}
