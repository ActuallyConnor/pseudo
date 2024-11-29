<?php

namespace Pseudo\UnitTest;

use Iterator;
use PHPUnit\Framework\TestCase;
use Pseudo\QueryLog;

class QueryLogTest extends TestCase
{
    public function testGetIterator() : void
    {
        $queryLog = new QueryLog();

        $queryLog->offsetSet('offset', 'value');
        $queries = $queryLog->getIterator();

        $this->assertInstanceOf(Iterator::class, $queries);

        foreach ($queries as $offset => $value) {
            $this->assertEquals('offset', $offset);
            $this->assertEquals('value', $value);
        }
    }

    public function testAddQuery()
    {
        $sql      = "SELECT foo FROM bar";
        $queryLog = new QueryLog();
        $queryLog->addQuery($sql);
        $queries = $queryLog->getQueries();
        $this->assertCount(1, $queries);
        $this->assertTrue($queries[0]->isEqualTo($sql));
    }

    public function testOffsetSetUnset() : void
    {
        $queryLog = new QueryLog();

        $queryLog->offsetSet('offset', 'value');
        $queries = $queryLog->getQueries();

        $this->assertEquals(['offset' => 'value'], $queries);

        $queryLog->offsetUnset('offset');
        $queries = $queryLog->getQueries();

        $this->assertEquals([], $queries);
    }
}
