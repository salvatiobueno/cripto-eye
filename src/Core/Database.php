<?php
declare(strict_types=1);

namespace App\Core;

use PDO;

final class Database
{
    private PDO $pdo;

    public function __construct(string $dbPath)
    {
        $dir = dirname($dbPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $this->pdo = new PDO('sqlite:' . $dbPath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->migrate();
    }

    private function migrate(): void
    {
        $this->pdo->exec('CREATE TABLE IF NOT EXISTS market_snapshots (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            created_at TEXT NOT NULL,
            payload TEXT NOT NULL
        )');
    }

    public function insertSnapshot(array $payload): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO market_snapshots(created_at, payload) VALUES(:created_at, :payload)');
        $stmt->execute([
            ':created_at' => gmdate('c'),
            ':payload' => json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        ]);
    }

    public function latestSnapshots(int $limit = 40): array
    {
        $stmt = $this->pdo->prepare('SELECT created_at, payload FROM market_snapshots ORDER BY id DESC LIMIT :limit');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];

        return array_map(static function (array $row): array {
            return [
                'created_at' => $row['created_at'],
                'payload' => json_decode($row['payload'], true) ?? [],
            ];
        }, $rows);
    }
}
