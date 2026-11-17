<?php

declare(strict_types=1);

namespace App\Core;

use App\Models\Cart;
use App\Models\User;

final class Auth
{
    private static ?array $user = null;

    private static bool $resolved = false;

    public static function user(): ?array
    {
        if (self::$resolved) {
            return self::$user;
        }

        self::$resolved = true;
        $id = (int) ($_SESSION['user_id'] ?? 0);

        self::$user = $id > 0 ? User::find($id) : null;

        return self::$user;
    }

    public static function id(): ?int
    {
        $user = self::user();

        return $user === null ? null : (int) $user['id'];
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function isAdmin(): bool
    {
        return (self::user()['role'] ?? null) === 'admin';
    }

    public static function attempt(string $email, string $password): ?array
    {
        $user = User::findByEmail($email);

        if ($user === null || ! password_verify($password, (string) $user['password_hash'])) {
            return null;
        }

        if ((int) $user['is_active'] !== 1) {
            return null;
        }

        return $user;
    }

    public static function login(array $user): void
    {
        session_regenerate_id(true);

        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['login_at'] = time();

        self::$resolved = true;
        self::$user = User::find((int) $user['id']);

        Cart::mergeGuestCart((int) $user['id']);
    }

    public static function logout(): void
    {
        self::$resolved = true;
        self::$user = null;

        unset($_SESSION['user_id'], $_SESSION['login_at']);
        session_regenerate_id(true);
    }
}
