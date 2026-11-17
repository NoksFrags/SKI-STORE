<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

final class Database
{
    private static ?PDO $connection = null;

    public static function pdo(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $config = config('database');

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['host'],
            $config['port'],
            $config['database'],
            $config['charset']
        );

        try {
            self::$connection = new PDO($dsn, $config['username'], $config['password'], [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $exception) {
            abort(500, self::connectionHint($exception));
        }

        return self::$connection;
    }

    private static function connectionHint(PDOException $exception): string
    {
        $message = 'The store could not reach its database. ';

        if (str_contains($exception->getMessage(), 'Unknown database')) {
            return $message . 'Create the database by importing database/schema.sql (see README.md).';
        }

        return $message . 'Check that MySQL is running in XAMPP and that the credentials in the .env file are correct.';
    }
}
