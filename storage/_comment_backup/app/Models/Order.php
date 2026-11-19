<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use DateTimeImmutable;
use PDO;
use Throwable;

final class Order
{
    public const STATUSES = [
        'pending'    => 'Pending',
        'processing' => 'Processing',
        'shipped'    => 'Shipped',
        'completed'  => 'Completed',
        'cancelled'  => 'Cancelled',
    ];

    public const PAYMENT_METHODS = [
        'card'             => 'Card on delivery (POS)',
        'cash_on_delivery' => 'Cash on delivery',
        'bank_transfer'    => 'Bank transfer',
    ];

    public static function statusLabel(string $status): string
    {
        return self::STATUSES[$status] ?? ucfirst($status);
    }

    public static function find(int $id): ?array
    {
        $statement = Database::pdo()->prepare(
            'SELECT o.*, u.full_name AS account_name, u.email AS account_email
             FROM orders o LEFT JOIN users u ON u.id = o.user_id WHERE o.id = ? LIMIT 1'
        );
        $statement->execute([$id]);
        $order = $statement->fetch();

        return $order ?: null;
    }

    public static function findForUser(int $id, int $userId): ?array
    {
        $statement = Database::pdo()->prepare('SELECT * FROM orders WHERE id = ? AND user_id = ? LIMIT 1');
        $statement->execute([$id, $userId]);
        $order = $statement->fetch();

        return $order ?: null;
    }

    public static function items(int $orderId): array
    {
        $statement = Database::pdo()->prepare(
            'SELECT oi.*, p.slug, p.image, p.is_active FROM order_items oi
             LEFT JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ? ORDER BY oi.id ASC'
        );
        $statement->execute([$orderId]);

        return $statement->fetchAll();
    }

    public static function addItem(int $orderId, array $line): void
    {
        $statement = Database::pdo()->prepare(
            'INSERT INTO order_items (order_id, product_id, product_name, unit_price, quantity, line_total)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $statement->execute([
            $orderId,
            $line['product_id'] ?? null,
            (string) $line['name'],
            (float) $line['price'],
            (int) $line['quantity'],
            (float) $line['total'],
        ]);
    }

    public static function create(array $data): int
    {
        $pdo = Database::pdo();
        $statement = $pdo->prepare(
            'INSERT INTO orders
                (order_number, user_id, status, payment_method, subtotal, shipping_cost, total,
                 customer_name, email, phone, shipping_address, shipping_city, shipping_postal_code, shipping_country, notes)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $statement->execute([
            (string) $data['order_number'],
            $data['user_id'] ?? null,
            (string) ($data['status'] ?? 'pending'),
            (string) $data['payment_method'],
            (float) $data['subtotal'],
            (float) $data['shipping_cost'],
            (float) $data['total'],
            (string) $data['customer_name'],
            (string) $data['email'],
            (string) $data['phone'],
            (string) $data['shipping_address'],
            (string) $data['shipping_city'],
            (string) $data['shipping_postal_code'],
            (string) $data['shipping_country'],
            $data['notes'] ?: null,
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function generateNumber(): string
    {
        $pdo = Database::pdo();

        for ($attempt = 0; $attempt < 10; $attempt++) {
            $number = 'SK-' . date('ymd') . '-' . strtoupper(bin2hex(random_bytes(2)));

            $statement = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE order_number = ?');
            $statement->execute([$number]);

            if ((int) $statement->fetchColumn() === 0) {
                return $number;
            }
        }

        return 'SK-' . time();
    }

    /**
     * @return array{items: array, total: int, page: int, pages: int}
     */
    public static function paginateForUser(int $userId, int $page = 1, int $perPage = 6): array
    {
        $pdo = Database::pdo();
        $page = max(1, $page);

        $count = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE user_id = ?');
        $count->execute([$userId]);
        $total = (int) $count->fetchColumn();
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $pages);

        $statement = $pdo->prepare(
            'SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC
             LIMIT ' . $perPage . ' OFFSET ' . (($page - 1) * $perPage)
        );
        $statement->execute([$userId]);

        return ['items' => $statement->fetchAll(), 'total' => $total, 'page' => $page, 'pages' => $pages];
    }

    /**
     * @return array{items: array, total: int, page: int, pages: int}
     */
    public static function paginateAdmin(string $search = '', string $status = '', int $page = 1, int $perPage = 12): array
    {
        $pdo = Database::pdo();
        $page = max(1, $page);
        $where = [];
        $params = [];

        if ($search !== '') {
            $where[] = '(o.order_number LIKE ? OR o.customer_name LIKE ? OR o.email LIKE ?)';
            $term = '%' . $search . '%';
            array_push($params, $term, $term, $term);
        }

        if ($status !== '' && array_key_exists($status, self::STATUSES)) {
            $where[] = 'o.status = ?';
            $params[] = $status;
        }

        $whereSql = $where === [] ? '' : ' WHERE ' . implode(' AND ', $where);

        $count = $pdo->prepare('SELECT COUNT(*) FROM orders o' . $whereSql);
        $count->execute($params);
        $total = (int) $count->fetchColumn();
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $pages);

        $statement = $pdo->prepare(
            'SELECT o.*, u.full_name AS account_name FROM orders o
             LEFT JOIN users u ON u.id = o.user_id' . $whereSql . '
             ORDER BY o.created_at DESC LIMIT ' . $perPage . ' OFFSET ' . (($page - 1) * $perPage)
        );
        $statement->execute($params);

        return ['items' => $statement->fetchAll(), 'total' => $total, 'page' => $page, 'pages' => $pages];
    }

    public static function recent(int $limit = 6): array
    {
        $statement = Database::pdo()->prepare(
            'SELECT o.*, u.full_name AS account_name FROM orders o
             LEFT JOIN users u ON u.id = o.user_id
             ORDER BY o.created_at DESC LIMIT ' . $limit
        );
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * @return array<string, float|int>
     */
    public static function stats(): array
    {
        $row = Database::pdo()->query(
            'SELECT COUNT(*) AS total_orders,
                    COALESCE(SUM(CASE WHEN status <> "cancelled" THEN total ELSE 0 END), 0) AS revenue,
                    COALESCE(SUM(CASE WHEN status <> "cancelled" AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY) THEN total ELSE 0 END), 0) AS revenue_30d,
                    COALESCE(AVG(CASE WHEN status <> "cancelled" THEN total END), 0) AS avg_order,
                    SUM(status = "pending") AS pending,
                    SUM(status = "processing") AS processing,
                    SUM(status = "shipped") AS shipped,
                    SUM(status = "completed") AS completed,
                    SUM(status = "cancelled") AS cancelled
             FROM orders'
        )->fetch();

        return [
            'total_orders' => (int) ($row['total_orders'] ?? 0),
            'revenue'      => (float) ($row['revenue'] ?? 0),
            'revenue_30d'  => (float) ($row['revenue_30d'] ?? 0),
            'avg_order'    => (float) ($row['avg_order'] ?? 0),
            'pending'      => (int) ($row['pending'] ?? 0),
            'processing'   => (int) ($row['processing'] ?? 0),
            'shipped'      => (int) ($row['shipped'] ?? 0),
            'completed'    => (int) ($row['completed'] ?? 0),
            'cancelled'    => (int) ($row['cancelled'] ?? 0),
        ];
    }

    /**
     * Revenue for the last N months, gaps filled with zeros for the chart.
     *
     * @return array<int, array{month: string, label: string, revenue: float, orders: int}>
     */
    public static function monthlyRevenue(int $months = 6): array
    {
        $start = (new DateTimeImmutable('first day of this month'))->modify('-' . ($months - 1) . ' months');

        $statement = Database::pdo()->prepare(
            'SELECT DATE_FORMAT(created_at, "%Y-%m") AS ym, SUM(total) AS revenue, COUNT(*) AS orders
             FROM orders WHERE status <> "cancelled" AND created_at >= ?
             GROUP BY ym'
        );
        $statement->execute([$start->format('Y-m-d 00:00:00')]);

        $rows = [];

        foreach ($statement->fetchAll() as $row) {
            $rows[(string) $row['ym']] = $row;
        }

        $series = [];

        for ($index = 0; $index < $months; $index++) {
            $month = $start->modify('+' . $index . ' months');
            $key = $month->format('Y-m');

            $series[] = [
                'month'   => $key,
                'label'   => $month->format('M'),
                'revenue' => (float) ($rows[$key]['revenue'] ?? 0),
                'orders'  => (int) ($rows[$key]['orders'] ?? 0),
            ];
        }

        return $series;
    }

    public static function countForUser(int $userId, string $status = ''): int
    {
        $sql = 'SELECT COUNT(*) FROM orders WHERE user_id = ?';
        $params = [$userId];

        if ($status !== '' && array_key_exists($status, self::STATUSES)) {
            $sql .= ' AND status = ?';
            $params[] = $status;
        }

        $statement = Database::pdo()->prepare($sql);
        $statement->execute($params);

        return (int) $statement->fetchColumn();
    }

    public static function totalSpentForUser(int $userId): float
    {
        $statement = Database::pdo()->prepare(
            'SELECT COALESCE(SUM(total), 0) FROM orders WHERE user_id = ? AND status <> "cancelled"'
        );
        $statement->execute([$userId]);

        return (float) $statement->fetchColumn();
    }

    /**
     * Change an order status, restoring or re-taking stock around cancellation.
     */
    public static function updateStatus(int $orderId, string $status): void
    {
        if (! array_key_exists($status, self::STATUSES)) {
            throw new \InvalidArgumentException('Unknown order status.');
        }

        $order = self::find($orderId);

        if ($order === null || $order['status'] === $status) {
            return;
        }

        $previous = (string) $order['status'];
        $pdo = Database::pdo();
        $pdo->beginTransaction();

        try {
            if ($status === 'cancelled' && $previous !== 'cancelled') {
                foreach (self::items($orderId) as $item) {
                    if ($item['product_id'] !== null) {
                        Product::increaseStock((int) $item['product_id'], (int) $item['quantity']);
                    }
                }
            } elseif ($previous === 'cancelled' && $status !== 'cancelled') {
                foreach (self::items($orderId) as $item) {
                    if ($item['product_id'] !== null) {
                        Product::decreaseStock((int) $item['product_id'], (int) $item['quantity']);
                    }
                }
            }

            $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$status, $orderId]);
            $pdo->commit();
        } catch (Throwable $exception) {
            $pdo->rollBack();
            throw $exception;
        }
    }
}
