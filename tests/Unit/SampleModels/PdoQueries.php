<?php

declare(strict_types=1);

namespace Pseudo\UnitTest\SampleModels;

use PDO;
use RuntimeException;

class PdoQueries
{

    public function __construct(private PDO $pdo)
    {
    }

    public function selectQueryWithNoParameters(): bool|array
    {
        // select all users
        $stmt = $this->pdo->query("SELECT * FROM users");

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function selectSingleRow(): ?array
    {
        // getting the last registered user
        $stmt = $this->pdo->query("SELECT * FROM users ORDER BY id DESC LIMIT 1");

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function selectMultipleRowsUsingFetch(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM users");

        return array_map(
            fn ($row) => $row['name'],
            $stmt->fetch(PDO::FETCH_ASSOC)
        );
    }

    public function selectMultipleRowsUsingFetchAll(): array
    {
        $data = $this->pdo->query("SELECT * FROM users")->fetchAll();

        // and somewhere later:
        return array_map(
            fn ($row) => $row['name'],
            $data
        );
    }

    public function selectQueryWithPlaceholders(): ?array
    {
        // select a particular user by id
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id=?");
        $stmt->execute([1]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function selectQueryWithNamedPlaceholders(): ?array
    {
        // select a particular user by id
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id=:id");
        $stmt->execute(['id' => 1]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function selectQueryWithNamedPlaceholdersAndFetchAll(): array|bool
    {
        // select a particular user by id
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id=:id");
        $stmt->execute(['id' => 1]);

        return $stmt->fetchAll();
    }

    public function selectingMultipleRowsUsingPlaceholdersAndFetch(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM users LIMIT ?, ?");
        $stmt->execute([10, 3]);

        return array_map(
            fn ($row) => $row['name'],
            $stmt->fetch(PDO::FETCH_ASSOC)
        );
    }

    public function selectingMultipleRowsUsingPlaceholdersAndFetchAll(): array
    {
        $stmt = $this->pdo->query("SELECT * FROM users LIMIT ?, ?");
        $stmt->execute([10, 3]);

        return array_map(
            fn ($row) => $row['name'],
            $stmt->fetchAll()
        );
    }

    /**
     * @param array<int> $ids
     * @return array
     */
    public function findAllByIds(array $ids): array
    {
        // Guard if empty array of UUIDs
        if (count($ids) === 0) {
            return [];
        }

        $select = $this->pdo->prepare(
            sprintf(
                'SELECT * FROM users WHERE id IN (:userIds) ORDER BY created_at DESC',
            )
        );

        $userIds = implode(',', $ids);

        $select->execute([':userIds' => $userIds]);

        /** @var array<array<int|string,mixed>> $rows */
        $rows = $select->fetchAll();

        return array_map(
            fn ($row) => self::parse($row),
            $rows
        );
    }

    public function deleteWithPlaceholder(int $id): void
    {
        $deleted = $this
            ->pdo
            ->prepare('DELETE FROM users WHERE id = ?')
            ->execute([$id]);

        if (!$deleted) {
            throw new RuntimeException(sprintf('Failed to delete user with ID "%s"', $id));
        }
    }

    private static function parse(array $row): array
    {
        return [
            'id' => (int) $row['id'],
            'name' => $row['name'],
        ];
    }
}
