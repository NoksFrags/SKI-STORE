<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;

final class Wishlist
{

    public static function toggle(int $userId, int $productId): bool
    {
        if (self::has($userId, $productId)) {
            self::remove($userId, $productId);

            return false;
        }

        Database::pdo()->prepare('INSERT IGNORE INTO wishlist_items (user_id, product_id) VALUES (?, ?)')
            ->execute([$userId, $productId]);

        return true;
    }

    public static function has(int $userId, int $productId): bool
    {
        $statement = Database::pdo()->prepare('SELECT COUNT(*) FROM wishlist_items WHERE user_id = ? AND product_id = ?');
        $statement->execute([$userId, $productId]);

        return (int) $statement->fetchColumn() > 0;
    }

    public static function ids(int $userId): array
    {
        $statement = Database::pdo()->prepare('SELECT product_id FROM wishlist_items WHERE user_id = ?');
        $statement->execute([$userId]);

        return array_map('intval', $statement->fetchAll(\PDO::FETCH_COLUMN));
    }

    public static function items(int $userId): array
    {
        $statement = Database::pdo()->prepare(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug, w.created_at AS saved_at
             FROM wishlist_items w
             INNER JOIN products p ON p.id = w.product_id
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE w.user_id = ?
             ORDER BY w.created_at DESC'
        );
        $statement->execute([$userId]);

        return $statement->fetchAll();
    }

    public static function remove(int $userId, int $productId): void
    {
        Database::pdo()->prepare('DELETE FROM wishlist_items WHERE user_id = ? AND product_id = ?')
            ->execute([$userId, $productId]);
    }

    public static function count(int $userId): int
    {
        $statement = Database::pdo()->prepare('SELECT COUNT(*) FROM wishlist_items WHERE user_id = ?');
        $statement->execute([$userId]);

        return (int) $statement->fetchColumn();
    }
}
