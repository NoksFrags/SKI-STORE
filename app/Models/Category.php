<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Category
{

    public static function all(): array
    {
        return Database::pdo()->query(
            'SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id AND p.is_active = 1) AS product_count
             FROM categories c
             ORDER BY c.sort_order ASC, c.name ASC'
        )->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $statement = Database::pdo()->prepare('SELECT * FROM categories WHERE id = ? LIMIT 1');
        $statement->execute([$id]);
        $category = $statement->fetch();

        return $category ?: null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $statement = Database::pdo()->prepare('SELECT * FROM categories WHERE slug = ? LIMIT 1');
        $statement->execute([$slug]);
        $category = $statement->fetch();

        return $category ?: null;
    }

    public static function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM categories WHERE slug = ?';
        $params = [$slug];

        if ($exceptId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $exceptId;
        }

        $statement = Database::pdo()->prepare($sql);
        $statement->execute($params);

        return (int) $statement->fetchColumn() > 0;
    }

    public static function create(array $data): int
    {
        $pdo = Database::pdo();
        $statement = $pdo->prepare(
            'INSERT INTO categories (name, slug, description, icon, sort_order) VALUES (?, ?, ?, ?, ?)'
        );
        $statement->execute([
            trim((string) $data['name']),
            (string) $data['slug'],
            trim((string) ($data['description'] ?? '')) ?: null,
            (string) ($data['icon'] ?? 'snow'),
            (int) ($data['sort_order'] ?? 0),
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $statement = Database::pdo()->prepare(
            'UPDATE categories SET name = ?, slug = ?, description = ?, icon = ?, sort_order = ? WHERE id = ?'
        );
        $statement->execute([
            trim((string) $data['name']),
            (string) $data['slug'],
            trim((string) ($data['description'] ?? '')) ?: null,
            (string) ($data['icon'] ?? 'snow'),
            (int) ($data['sort_order'] ?? 0),
            $id,
        ]);
    }

    public static function delete(int $id): void
    {
        Database::pdo()->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
    }

    public static function productCount(int $id): int
    {
        $statement = Database::pdo()->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
        $statement->execute([$id]);

        return (int) $statement->fetchColumn();
    }
}
