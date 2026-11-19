<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Database;
use PDO;

final class User
{
    public static function find(int $id): ?array
    {
        $statement = Database::pdo()->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $statement->execute([$id]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public static function findByEmail(string $email): ?array
    {
        $statement = Database::pdo()->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $statement->execute([mb_strtolower(trim($email))]);
        $user = $statement->fetch();

        return $user ?: null;
    }

    public static function emailExists(string $email, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM users WHERE email = ?';
        $params = [mb_strtolower(trim($email))];

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
            'INSERT INTO users (full_name, email, password_hash, phone, address, city, postal_code, country)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $statement->execute([
            trim((string) $data['full_name']),
            mb_strtolower(trim((string) $data['email'])),
            (string) $data['password_hash'],
            ($data['phone'] ?? null) ?: null,
            ($data['address'] ?? null) ?: null,
            ($data['city'] ?? null) ?: null,
            ($data['postal_code'] ?? null) ?: null,
            ($data['country'] ?? null) ?: 'Bosnia and Herzegovina',
        ]);

        return (int) $pdo->lastInsertId();
    }

    public static function updateProfile(int $id, array $data): void
    {
        $statement = Database::pdo()->prepare(
            'UPDATE users SET full_name = ?, email = ?, phone = ?, address = ?, city = ?, postal_code = ?, country = ? WHERE id = ?'
        );

        $statement->execute([
            trim((string) $data['full_name']),
            mb_strtolower(trim((string) $data['email'])),
            $data['phone'] ?: null,
            $data['address'] ?: null,
            $data['city'] ?: null,
            $data['postal_code'] ?: null,
            $data['country'] ?: null,
            $id,
        ]);
    }

    public static function updatePassword(int $id, string $hash): void
    {
        Database::pdo()->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, $id]);
    }

    public static function touchLogin(int $id): void
    {
        Database::pdo()->prepare('UPDATE users SET last_login_at = NOW() WHERE id = ?')->execute([$id]);
    }

    /**
     * @return array{items: array, total: int, page: int, pages: int}
     */
    public static function paginate(string $search = '', int $page = 1, int $perPage = 12): array
    {
        $pdo = Database::pdo();
        $page = max(1, $page);
        $where = '';
        $params = [];

        if ($search !== '') {
            $where = 'WHERE full_name LIKE ? OR email LIKE ?';
            $params = ['%' . $search . '%', '%' . $search . '%'];
        }

        $count = $pdo->prepare('SELECT COUNT(*) FROM users ' . $where);
        $count->execute($params);
        $total = (int) $count->fetchColumn();
        $pages = max(1, (int) ceil($total / $perPage));
        $page = min($page, $pages);

        $sql = 'SELECT id, full_name, email, phone, city, role, is_active, created_at, last_login_at
                FROM users ' . $where . ' ORDER BY created_at DESC LIMIT ' . $perPage . ' OFFSET ' . (($page - 1) * $perPage);

        $statement = $pdo->prepare($sql);
        $statement->execute($params);

        return ['items' => $statement->fetchAll(), 'total' => $total, 'page' => $page, 'pages' => $pages];
    }

    public static function countCustomers(): int
    {
        return (int) Database::pdo()->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
    }

    public static function countNewThisMonth(): int
    {
        return (int) Database::pdo()
            ->query('SELECT COUNT(*) FROM users WHERE created_at >= DATE_FORMAT(CURDATE(), "%Y-%m-01")')
            ->fetchColumn();
    }

    public static function adminCount(): int
    {
        return (int) Database::pdo()->query("SELECT COUNT(*) FROM users WHERE role = 'admin'")->fetchColumn();
    }

    public static function setRole(int $id, string $role): void
    {
        Database::pdo()->prepare('UPDATE users SET role = ? WHERE id = ?')->execute([$role, $id]);
    }

    public static function setActive(int $id, bool $active): void
    {
        Database::pdo()->prepare('UPDATE users SET is_active = ? WHERE id = ?')->execute([$active ? 1 : 0, $id]);
    }
}
