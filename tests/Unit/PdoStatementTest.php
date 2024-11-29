<?php

namespace Pseudo\UnitTest;

use Iterator;
use PDO;
use PHPUnit\Framework\TestCase;
use Pseudo\Exceptions\PseudoException;
use Pseudo\PdoStatement;
use Pseudo\QueryLog;
use Pseudo\Result;

class PdoStatementTest extends TestCase
{
    public function testFetchAllWithNoArguments()
    {
        $rows                = [
            'id'  => 1,
            'foo' => 'bar'
        ];
        $expectedFetchResult = [
            [
                'id'  => 1,
                0     => 1,
                'foo' => 'bar',
                1     => 'bar'
            ]
        ];
        $r                   = new Result([$rows]);
        $s                   = new PdoStatement();
        $s->setResult($r);
        $fetchResult = $s->fetchAll();
        $this->assertEquals($expectedFetchResult, $fetchResult);
    }

    public function testFetchAllWithFetchAssoc()
    {
        $rows = [
            'id'  => 1,
            'foo' => 'bar'
        ];
        $r    = new Result([$rows]);
        $s    = new PdoStatement();
        $s->setResult($r);
        $fetchResult = $s->fetchAll(PDO::FETCH_ASSOC);
        $this->assertEquals([$rows], $fetchResult);
    }

    public function testFetchAllWithFetchNum()
    {
        $rows                = [
            'id'  => 1,
            'foo' => 'bar'
        ];
        $expectedFetchResult = [
            [
                0 => 1,
                1 => 'bar'
            ]
        ];
        $r                   = new Result([$rows]);
        $s                   = new PdoStatement();
        $s->setResult($r);
        $fetchResult = $s->fetchAll(PDO::FETCH_NUM);
        $this->assertEquals($expectedFetchResult, $fetchResult);
    }

    public function testFetchAllWithFetchObj()
    {
        $rows                = [
            'id'  => 1,
            'foo' => 'bar'
        ];
        $expectedFetchResult = [
            (object)[
                'id'  => 1,
                'foo' => 'bar'
            ]
        ];
        $r                   = new Result([$rows]);
        $s                   = new PdoStatement();
        $s->setResult($r);
        $fetchResult = $s->fetchAll(PDO::FETCH_OBJ);
        $this->assertEquals($expectedFetchResult, $fetchResult);
    }

    public function testFetchUnrecognizedFetchMode(): void
    {
        $rows = [
            'id'  => 1,
            'foo' => 'bar'
        ];
        $r    = new Result([$rows]);
        $s    = new PdoStatement();
        $s->setResult($r);
        $fetchResult = $s->fetchAll(-1);
        $this->assertEquals([null], $fetchResult);
    }

    public function testFetchAllNonArrayData(): void
    {
        $r = new Result(null);
        $s = new PdoStatement();
        $s->setResult($r);
        $fetchResult = $s->fetchAll();
        $this->assertEmpty($fetchResult);
    }

    public function testFetchAllSubArrayNotArray(): void
    {
        $r = new Result([]);
        $s = new PdoStatement();
        $s->setResult($r);
        $fetchResult = $s->fetchAll();
        $this->assertEmpty($fetchResult);
    }

    public function testRowCount()
    {
        $s = new PdoStatement();
        $r = new Result();
        $s->setResult($r);
        $this->assertEquals(0, $s->rowCount());
        $r->setAffectedRowCount(5);
        $this->assertEquals(5, $s->rowCount());
    }

    public function testErrorCode()
    {
        $r = new Result();
        $r->setErrorCode("HY000");
        $p = new PdoStatement($r);
        $this->assertEquals("HY000", $p->errorCode());
    }

    public function testErrorInfo()
    {
        $r = new Result();
        $r->setErrorInfo("Storage engine error");
        $p = new PdoStatement($r);
        $this->assertEquals("Storage engine error", $p->errorInfo()[0]);
    }

    public function testColumnCount()
    {
        $r = new Result();
        $r->addRow(
            [
                'id'  => 1,
                'foo' => 'bar'
            ]
        );
        $p = new PdoStatement($r);
        $this->assertEquals(2, $p->columnCount());
    }

    public function testColumnCountWhenZeo(): void
    {
        $statement = new PdoStatement();

        $this->assertEquals(0, $statement->columnCount());
    }

    public function testSetFetchMode()
    {
        $p       = new PdoStatement();
        $success = $p->setFetchMode(PDO::FETCH_ASSOC);
        $this->assertEquals(1, $success);

        $this->expectException(PseudoException::class);
        $p->setFetchMode(456);
    }

    public function testFetch()
    {
        $row1 = [
            'id'  => 1,
            'foo' => 'bar',
        ];
        $row2 = [
            'id'  => 2,
            'foo' => 'baz'
        ];

        $r = new Result();
        $p = new PdoStatement($r);

        $data = $p->fetch();
        $this->assertFalse($data);

        $r->addRow($row1);
        $r->addRow($row2);

        $p->setFetchMode(PDO::FETCH_ASSOC);
        $this->assertEquals($row1, $p->fetch());
        $this->assertEquals($row2, $p->fetch());
    }

    public function testFetchCol()
    {
        $col1 = 1234;
        $row1 = [
            'id'  => $col1,
            'foo' => 'bar',
        ];

        $col2 = 5678;
        $row2 = [
            'id'  => $col2,
            'foo' => 'baz'
        ];

        $r = new Result();
        $p = new PdoStatement($r);

        $r->addRow($row1);
        $r->addRow($row2);

        $p->setFetchMode(PDO::FETCH_COLUMN);
        $this->assertEquals($col1, $p->fetch());
        $this->assertEquals($col2, $p->fetch());
    }

    public function testFetchWithBoundColumns()
    {
        $row1 = [
            'id'  => 1,
            'foo' => 'bar',
        ];
        $r    = new Result();
        $r->addRow($row1);
        $p = new PdoStatement($r);
        $p->bindColumn(2, $test);
        $p->fetch(PDO::FETCH_BOUND);
        $this->assertEquals('bar', $test);
        unset($test);

        $r->reset();
        $p = new PdoStatement($r);
        $p->bindColumn('foo', $test);
        $p->fetch(PDO::FETCH_BOUND);
        $this->assertEquals('bar', $test);
    }


    public function testFetchObject()
    {
        $row1       = [
            'id'  => 1,
            'foo' => 'bar',
        ];
        $testObject = (object)$row1;
        $r          = new Result();
        $r->addRow($row1);
        $s = new PdoStatement($r);
        $this->assertEquals($testObject, $s->fetchObject());
    }

    public function testFailsToFetchObject(): void
    {
        $statement = new PdoStatement();
        $this->assertFalse($statement->fetchObject());
    }

    public function testNullFetchObject(): void
    {
        $statement = new PdoStatement();
        $this->assertFalse($statement->fetchObject(null));
    }

    public function testExecute()
    {
        $row1    = [
            'id'  => 1,
            'foo' => 'bar',
        ];
        $params1 = [
            'bar'
        ];

        $r = new Result();
        $r->addRow($row1, $params1);
        $queryLog = new QueryLog();
        $s        = new PdoStatement($r, $queryLog, 'SELECT * FROM test');

        $this->assertTrue($s->execute($params1));
        $this->assertFalse($s->execute());
    }

    public function testBindParam()
    {
        $param = 'foo';
        $s     = new PdoStatement();
        $this->assertTrue($s->bindParam(1, $param));
        $param = 'bar';
        $this->assertEquals([1 => 'bar'], $s->getBoundParams());
    }

    public function testBindValue()
    {
        $param = 'foo';
        $s     = new PdoStatement();
        $this->assertTrue($s->bindValue(1, $param));
        $this->assertEquals([1 => 'foo'], $s->getBoundParams());
    }

    public function testFetchColumn()
    {
        $row1 = [
            'id'  => 1,
            'foo' => 'bar',
        ];

        $r = new Result();
        $r->addRow($row1);
        $s = new PdoStatement($r);

        $this->assertEquals('bar', $s->fetchColumn(1));
        $this->assertFalse($s->fetchColumn());
    }

    public function testGetIterator(): void
    {
        $rows                = [
            'id'  => 1,
            'foo' => 'bar'
        ];
        $expectedFetchResult = [
            [
                'id'  => 1,
                0     => 1,
                'foo' => 'bar',
                1     => 'bar'
            ]
        ];
        $r                   = new Result([$rows]);
        $s                   = new PdoStatement();
        $s->setResult($r);
        $fetchResult = $s->getIterator();
        $this->assertInstanceOf(Iterator::class, $fetchResult);
        $this->assertEquals($expectedFetchResult, [...$fetchResult]);
    }
}
