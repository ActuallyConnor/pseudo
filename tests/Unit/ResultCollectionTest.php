<?php

namespace Pseudo\UnitTest;

use PHPUnit\Framework\TestCase;
use Pseudo\Exceptions\PseudoException;
use Pseudo\ResultCollection;
use RuntimeException;

class ResultCollectionTest extends TestCase
{
    public function testGetResultWithoutMocking()
    {
        $r = new ResultCollection();
        $this->expectException(PseudoException::class);
        $r->getResult("SELECT 1");
    }

    public function testDebuggingRawQueries()
    {
        $message = null;
        $r = new ResultCollection();
        try {
            $r->getResult('SELECT 123');
        } catch (PseudoException $e) {
            $message = $e->getMessage();
        }
        $this->assertMatchesRegularExpression('/SELECT 123/', $message);
    }

    public function testAddQuery(): void
    {
        $results = new ResultCollection();
        $results->addQuery("SELECT * FROM ?", ['table'], [['test']]);

        $this->assertEquals(1, $results->count());
    }

    public function testGetResultBool(): void
    {
        $collection = new ResultCollection();

        $collection->addQuery('SELECT 1', [], true);

        $result = $collection->getResult("SELECT 1");

        $this->assertEmpty($result->getRows());
        $this->assertTrue($result->getExecutionResult());
    }

    public function testGetResultException()
    {
        $collection = new ResultCollection();
        $collection->addQuery('SELECT 1', [], new RuntimeException());

        $this->expectException(RuntimeException::class);
        $collection->getResult('SELECT 1');
    }
}
