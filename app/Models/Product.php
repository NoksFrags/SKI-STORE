<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class Product
{
    private const SELECT = 'SELECT p.*, c.name AS category_name, c.slug AS category_slug
        FROM products p
        LEFT JOIN categories c ON c.id = p.category_id';

    /**
     * Filterable, paginated product listing used by the shop and admin screens.
     *
     * @param array{search?: string, category?: string, sort?: string, featured?: bool, status?: string, include_inactive?: bool} $filters
     * @return array{items: array, total: int, page: int, pages: int}
     */
    public static function paginate(array $filters = [], int $page = 1, int $perPage = 9): array
    {
        $pdo = Database::pdo();
        $page = max(1, $page);
        $where = [];
        $params = [];

        if (empty($filters['include_inactive'])) {
            $where[] = 'p.is_active = 1';
        }

        if (! empty($filters['search'])) {
            $where[] = '(p.name LIKE ? OR p.brand LIKE ? OR p.short_description LIKE ?)';
            $term = '%' . $filters['search'] . '%';
            array_push($params, $term, $term, $term);
        }

        if (! empty($filters['category'])) {
            $where[] = 'c.slug = ?';
            $params[] = $filters['category'];
        }

        if (! empty($filters['featured'])) {
            $where[] = 'p.is_featured = 1';
        }

        if (! empty($filters['status'])) {
            if ($filters['status'] === 'out_of_stock') {
                $where[] = 'p.stock <= 0';
            } elseif ($filters['status'] === 'low_stock') {
                $where[] = 'p.stock > 0 AND p.stock <= ' . (int) config('app.low_stock_threshold', 5);
            } elseif ($filters['status'] === 'inactive') {
                $where[] = 'p.is_active = 0';
            } elseif ($filters['status'] === 'active') {
                $where[] = 'p.is_active = 1';
            }
        }

        $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);

        $count = $pdo->prepare('SELECT COUNT(*) FROM products p LEFT JOIN categories c ON c.id = p.category_id' . $whereSql);
        $count->execute($params);
        $total = (int) $count->fetchColumn();
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $pages);

        $order = match ($filters['sort'] ?? 'newest') {
            'price_asc'  => 'p.price ASC',
            'price_desc' => 'p.price DESC',
            'name_asc'   => 'p.name ASC',
            'stock'      => 'p.stock DESC',
            default      => 'p.is_featured DESC, p.created_at DESC',
        };

        $sql = self::SELECT . $whereSql . ' ORDER BY ' . $order . ' LIMIT ' . $perPage . ' OFFSET ' . (($page - 1) * $perPage);
        $statement = $pdo->prepare($sql);
        $statement->execute($params);

        return ['items' => $statement->fetchAll(), 'total' => $total, 'page' => $page, 'pages' => $pages];
    }

    public static function featured(int $limit = 4): array
    {
        $statement = Database::pdo()->prepare(
            self::SELECT . ' WHERE p.is_active = 1 AND p.is_featured = 1 ORDER BY p.created_at DESC LIMIT ' . $limit
        );
        $statement->execute();

        return $statement->fetchAll();
    }

    public static function newest(int $limit = 4): array
    {
        $statement = Database::pdo()->prepare(
            self::SELECT . ' WHERE p.is_active = 1 ORDER BY p.created_at DESC LIMIT ' . $limit
        );
        $statement->execute();

        return $statement->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $statement = Database::pdo()->prepare(self::SELECT . ' WHERE p.id = ? LIMIT 1');
        $statement->execute([$id]);
        $product = $statement->fetch();

        return $product ?: null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $statement = Database::pdo()->prepare(self::SELECT . ' WHERE p.slug = ? LIMIT 1');
        $statement->execute([$slug]);
        $product = $statement->fetch();

        return $product ?: null;
    }

    public static function slugExists(string $slug, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM products WHERE slug = ?';
        $params = [$slug];

        if ($exceptId !== null) {
            $sql .= ' AND id <> ?';
            $params[] = $exceptId;
        }

        $statement = Database::pdo()->prepare($sql);
        $statement->execute($params);

        return (int) $statement->fetchColumn() > 0;
    }

    public static function related(array $product, int $limit = 3): array
    {
        $statement = Database::pdo()->prepare(
            self::SELECT . ' WHERE p.is_active = 1 AND p.id <> ? AND (p.category_id = ? OR ? IS NULL)
             ORDER BY p.category_id = ? DESC, RAND() LIMIT ' . $limit
        );
        $statement->execute([$product['id'], $product['category_id'], $product['category_id'], $product['category_id']]);

        return $statement->fetchAll();
    }

    public static function create(array $data): int
    {
        $pdo = Database::pdo();
        $statement = $pdo->prepare(
            'INSERT INTO products
                (category_id, name, slug, brand, sku, short_description, description, price, compare_at_price, stock, image, is_featured, is_active)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $statement->execute([
            $data['category_id'] ?: null,
            trim((string) $data['name']),
            (string) $data['slug'],
            $data['brand'] ?: null,
            $data['sku'] ?: null,
            $data['short_description'] ?: null,
            $data['description'] ?: null,
            (float) $data['price'],
            $data['compare_at_price'] !== null && $data['compare_at_price'] !== '' ? (float) $data['compare_at_price'] : null,
            (int) $data['stock'],
            $data['image'] ?: null,
            ! empty($data['is_featured']) ? 1 : 0,
            ! empty($data['is_active']) ? 1 : 0,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        $statement = Database::pdo()->prepare(
            'UPDATE products SET category_id = ?, name = ?, slug = ?, brand = ?, sku = ?, short_description = ?,
                description = ?, price = ?, compare_at_price = ?, stock = ?, image = ?, is_featured = ?, is_active = ?
             WHERE id = ?'
        );

        $statement->execute([
            $data['category_id'] ?: null,
            trim((string) $data['name']),
            (string) $data['slug'],
            $data['brand'] ?: null,
            $data['sku'] ?: null,
            $data['short_description'] ?: null,
            $data['description'] ?: null,
            (float) $data['price'],
            $data['compare_at_price'] !== null && $data['compare_at_price'] !== '' ? (float) $data['compare_at_price'] : null,
            (int) $data['stock'],
            $data['image'] ?: null,
            ! empty($data['is_featured']) ? 1 : 0,
            ! empty($data['is_active']) ? 1 : 0,
            $id,
        ]);
    }

    public static function delete(int $id): void
    {
        Database::pdo()->prepare('DELETE FROM products WHERE id = ?')->execute([$id]);
    }

    public static function decreaseStock(int $productId, int $quantity): bool
    {
        $statement = Database::pdo()->prepare(
            'UPDATE products SET stock = stock - ? WHERE id = ? AND stock >= ?'
        );
        $statement->execute([$quantity, $productId, $quantity]);

        return $statement->rowCount() === 1;
    }

    public static function increaseStock(int $productId, int $quantity): void
    {
        Database::pdo()->prepare('UPDATE products SET stock = stock + ? WHERE id = ?')->execute([$quantity, $productId]);
    }

    public static function lowStock(int $limit = 6): array
    {
        $threshold = (int) config('app.low_stock_threshold', 5);
        $statement = Database::pdo()->prepare(
            self::SELECT . ' WHERE p.is_active = 1 AND p.stock <= ' . $threshold . ' ORDER BY p.stock ASC LIMIT ' . $limit
        );
        $statement->execute();

        return $statement->fetchAll();
    }

    public static function topSelling(int $limit = 5): array
    {
        $statement = Database::pdo()->prepare(
            'SELECT p.id, p.name, p.price, p.image, p.slug, SUM(oi.quantity) AS units_sold, SUM(oi.line_total) AS revenue
             FROM order_items oi
             INNER JOIN products p ON p.id = oi.product_id
             INNER JOIN orders o ON o.id = oi.order_id
             WHERE o.status <> "cancelled"
             GROUP BY p.id, p.name, p.price, p.image, p.slug
             ORDER BY units_sold DESC
             LIMIT ' . $limit
        );
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @return array{total: int, active: int, out_of_stock: int, low_stock: int, inventory_value: float}
     */
    public static function stats(): array
    {
        $row = Database::pdo()->query(
            'SELECT COUNT(*) AS total,
                    SUM(is_active = 1) AS active,
                    SUM(stock <= 0) AS out_of_stock,
                    SUM(stock > 0 AND stock <= ' . (int) config('app.low_stock_threshold', 5) . ') AS low_stock,
                    SUM(stock * price) AS inventory_value
             FROM products'
        )->fetch();

        return [
            'total'           => (int) ($row['total'] ?? 0),
            'active'          => (int) ($row['active'] ?? 0),
            'out_of_stock'    => (int) ($row['out_of_stock'] ?? 0),
            'low_stock'       => (int) ($row['low_stock'] ?? 0),
            'inventory_value' => (float) ($row['inventory_value'] ?? 0),
        ];
    }
}
