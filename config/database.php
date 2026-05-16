<?php

declare(strict_types=1);

namespace App\Config;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Singleton database wrapper ensuring a single, reusable PDO connection instance.
 */
class Database
{
    private static ?PDO $instance = null;

    private function __construct() {}
    private function __clone() {}

    /**
     * Retrieves the active database connection instance.
     * Reads configuration parameters safely from environment defaults.
     *
     * @return PDO
     * @throws RuntimeException If connection setup fails.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            // Fallback parameters map to local dev defaults if environment variables are unset
            $host = getenv('DB_HOST') ?: 'localhost';
            $port = getenv('DB_PORT') ?: '3306';
            $db   = getenv('DB_NAME') ?: 'privacyknowledgebase';
            $user = getenv('DB_USER') ?: 'root';
            $pass = getenv('DB_PASS') ?: '';
            $charset = 'utf8mb4';

            $dsn = "mysql:host={$host};port={$port};dbname={$db};charset={$charset}";

            // Enforce strictly safe runtime defaults: throws exceptions, associative fetches, native preparation
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            try {
                self::$instance = new PDO($dsn, $user, $pass, $options);
            } catch (PDOException $e) {
                // Keep sensitive database logs internal; present a generic failure message to clients
                error_log("DB Connection failure: " . $e->getMessage());
                throw new RuntimeException("Service configuration unavailable. Please check system logs.");
            }
        }

        return self::$instance;
    }
}