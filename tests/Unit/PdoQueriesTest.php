<?php

declare(strict_types=1);

namespace Pseudo\UnitTest;

use PHPUnit\Framework\TestCase;
use Pseudo\Pdo;
use Pseudo\Result;
use Pseudo\UnitTest\SampleModels\PdoQueries;
use RuntimeException;

class PdoQueriesTest extends TestCase
{
    private Pdo $pdo;
    private PdoQueries $pdoQueries;

    public function setUp(): void
    {
        parent::setUp();

        $this->pdo = new Pdo();
        $this->pdoQueries = new PdoQueries($this->pdo);
    }


    public function testSelectQueryWithNoParameters(): void
    {
        $this->pdo->mock(
            'SELECT * FROM users',
            [],
            [
                [
                    'id' => 1,
                    'name' => 'John Doe',
                ]
            ]
        );

        $data = $this->pdoQueries->selectQueryWithNoParameters();
        $this->assertEquals(
            [
                'id' => 1,
                'name' => 'John Doe',
            ],
            $data
        );
    }

    public function testSelectQueryWithPlaceholders(): void
    {
        $this->pdo->mock(
            'SELECT * FROM users WHERE id=?',
            [1],
            [
                ['id' => 1, 'name' => 'John Doe']
            ]
        );

        $data = $this->pdoQueries->selectQueryWithPlaceholders();
        $this->assertEquals(['id' => 1, 'name' => 'John Doe'], $data);
    }

    public function testSelectQueryWithNamedPlaceholders(): void
    {
        $this->pdo->mock(
            'SELECT * FROM users WHERE id=:id',
            ['id' => 1],
            [
                ['id' => 1, 'name' => 'John Doe']
            ]
        );

        $data = $this->pdoQueries->selectQueryWithNamedPlaceholders();
        $this->assertEquals(['id' => 1, 'name' => 'John Doe'], $data);
    }

    public function testSelectQueryWithNamedPlaceholdersAndFetchAll(): void
    {
        $this->pdo->mock(
            'SELECT * FROM users WHERE id=:id',
            ['id' => 1],
            [
                ['id' => 1, 'name' => 'John Doe']
            ]
        );

        $data = $this->pdoQueries->selectQueryWithNamedPlaceholders();
        $this->assertEquals(['id' => 1, 'name' => 'John Doe'], $data);
    }

    public function testFindAllByIds(): void
    {
        $this->pdo->mock(
            'SELECT * FROM users WHERE id IN (:userIds) ORDER BY created_at DESC',
            [':userIds' => '1'],
            [
                ['id' => 1, 'name' => 'John Doe']
            ]
        );

        $data = $this->pdoQueries->findAllByIds([1]);
        $this->assertEquals([['id' => 1, 'name' => 'John Doe']], $data);
    }

    public function testSelectQueryWithNamedPlaceholdersAndFetchAllFails(): void
    {
        $this->pdo->mock(
            'SELECT * FROM users WHERE id=:id',
            ['id' => 1],
            false
        );

        $this->assertFalse($this->pdoQueries->selectQueryWithNamedPlaceholdersAndFetchAll());
    }

    public function testDeleteWithPlaceholder(): void
    {
        $this->pdo->mock(
            'DELETE FROM users WHERE id = ?',
            [1],
            true
        );

        $this->expectNotToPerformAssertions();
        $this->pdoQueries->deleteWithPlaceholder(1);
    }

    public function testFailToDeleteWithPlaceholder(): void
    {
        $this->pdo->mock(
            'DELETE FROM users WHERE id = ?',
            [1],
            null,
            false
        );

        $this->expectException(RuntimeException::class);
        $this->pdoQueries->deleteWithPlaceholder(1);
    }
}
