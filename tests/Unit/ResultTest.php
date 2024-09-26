<?php

namespace Pseudo\UnitTest;

use PHPUnit\Framework\TestCase;
use Pseudo\Exception;
use Pseudo\Result;

class ResultTest extends TestCase
{
    public function testSetErrorCode()
    {
        $r = new Result;
        $r->setErrorCode("HY000");
        $this->assertEquals("HY000", $r->getErrorCode());
        $this->expectException(Exception::class);
        $r->setErrorCode("121");
    }

    public function testNextRow()
    {
        $row1 = [
            'id' => 1,
            'foo' => 'bar',
        ];
        $row2 = [
            'id'  => 2,
            'foo' => 'baz'
        ];
        $r = new Result();
        $r->addRow($row1);
        $r->addRow($row2);

        $this->assertEquals($row1, $r->nextRow());
        $this->assertEquals($row2, $r->nextRow());
        $this->assertEquals(false, $r->nextRow());
    }

    public function testAddRow()
    {
        $row = [
            'id'  => 1,
            'foo' => 'bar'
        ];
        $params = [
            'bar'
        ];

        $r = new Result;
        $r->addRow($row);
        $this->assertEquals(1, count($r->getRows()));

        $r = new Result;
        $r->addRow($row, $params);
        $this->assertEquals(1, count($r->getRows($params)));
    }

    public function testReset()
    {
        $row = [
            'id'  => 1,
            'foo' => 'bar'
        ];
        $r = new Result();
        $r->addRow($row);
        $this->assertEquals($row, $r->nextRow());
        $this->assertEquals(null, $r->nextRow());
        $r->reset();
        $this->assertEquals($row, $r->nextRow());
    }
}