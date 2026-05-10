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

    public function testSelectSingleRow(): void
    {
        $this->pdo->mock(
            'SELECT * FROM users ORDER BY id DESC LIMIT 1',
            [],
            [
                [
                    'id' => 1,
                    'name' => 'John Doe',
                ]
            ]
        );

        $data = $this->pdoQueries->selectSingleRow();
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

        $data = $this->pdoQueries->selectQueryWithNamedPlaceholdersAndFetchAll();
        $this->assertEquals(
            [['id' => 1, 0 => 1, 'name' => 'John Doe', 1 => 'John Doe']],
            $data
        );
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
            false
        );

        $this->expectException(RuntimeException::class);
        $this->pdoQueries->deleteWithPlaceholder(1);
    }

    public function testSelectMultipleRowsUsingFetchAll(): void
    {
        $this->pdo->mock(
            'SELECT * FROM users',
            [],
            [
                ['id' => 1, 'name' => 'John Doe'],
                ['id' => 2, 'name' => 'Jane Doe'],
            ]
        );

        $data = $this->pdoQueries->selectMultipleRowsUsingFetchAll();
        $this->assertEquals(['John Doe', 'Jane Doe'], $data);
    }

    public function testSelectingMultipleRowsUsingPlaceholdersAndFetchAll(): void
    {
        $this->pdo->mock(
            'SELECT * FROM users LIMIT ?, ?',
            [10, 3],
            [
                ['id' => 1, 'name' => 'John Doe'],
                ['id' => 2, 'name' => 'Jane Doe'],
                ['id' => 3, 'name' => 'Bob Smith'],
            ]
        );

        $data = $this->pdoQueries->selectingMultipleRowsUsingPlaceholdersAndFetchAll();
        $this->assertEquals(['John Doe', 'Jane Doe', 'Bob Smith'], $data);
    }

    public function testFindAllByIdsWithEmptyArray(): void
    {
        $data = $this->pdoQueries->findAllByIds([]);
        $this->assertEquals([], $data);
    }

    public function testFindAllByIdsWithMultipleIds(): void
    {
        $this->pdo->mock(
            'SELECT * FROM users WHERE id IN (:userIds) ORDER BY created_at DESC',
            [':userIds' => '1,2,3'],
            [
                ['id' => 3, 'name' => 'Bob Smith'],
                ['id' => 2, 'name' => 'Jane Doe'],
                ['id' => 1, 'name' => 'John Doe'],
            ]
        );

        $data = $this->pdoQueries->findAllByIds([1, 2, 3]);
        $this->assertEquals(
            [
                ['id' => 3, 'name' => 'Bob Smith'],
                ['id' => 2, 'name' => 'Jane Doe'],
                ['id' => 1, 'name' => 'John Doe'],
            ],
            $data
        );
    }

    public function testInsertUser(): void
    {
        $result = new Result(null, null, true);
        $result->setInsertId(42);
        $this->pdo->mock('INSERT INTO users (name) VALUES (:name)', null, $result);

        $insertedId = $this->pdoQueries->insertUser('Jane Doe');
        $this->assertEquals(42, $insertedId);
    }

    public function testUpdateUserName(): void
    {
        $result = new Result(null, null, true);
        $result->setAffectedRowCount(1);
        $this->pdo->mock('UPDATE users SET name = :name WHERE id = :id', null, $result);

        $affected = $this->pdoQueries->updateUserName(1, 'Jane Doe');
        $this->assertEquals(1, $affected);
    }

    public function testGetUserNameById(): void
    {
        $this->pdo->mock(
            'SELECT name FROM users WHERE id = ?',
            [1],
            [
                ['name' => 'John Doe']
            ]
        );

        $name = $this->pdoQueries->getUserNameById(1);
        $this->assertEquals('John Doe', $name);
    }

    public function testGetUserObjectById(): void
    {
        $this->pdo->mock(
            'SELECT * FROM users WHERE id = ?',
            [1],
            [
                ['id' => 1, 'name' => 'John Doe']
            ]
        );

        $user = $this->pdoQueries->getUserObjectById(1);
        $this->assertIsObject($user);
        $this->assertEquals(1, $user->id);
        $this->assertEquals('John Doe', $user->name);
    }

    public function testGetUsersWithBoundValue(): void
    {
        $this->pdo->mock(
            'SELECT * FROM users WHERE id > ?',
            [5],
            [
                ['id' => 6, 'name' => 'Jane Doe'],
                ['id' => 7, 'name' => 'Bob Smith'],
            ]
        );

        $data = $this->pdoQueries->getUsersWithBoundValue(5);
        $this->assertEquals(
            [
                ['id' => 6, 'name' => 'Jane Doe'],
                ['id' => 7, 'name' => 'Bob Smith'],
            ],
            $data
        );
    }
}
