<?php

require_once __DIR__ . '/../../vendor/autoload.php';
use Dotenv\Dotenv;

class BankImportHelper
{
    private static $envLoaded = false;

    public static function loadEnv()
    {
        if (self::$envLoaded) return;

        $dotenv = Dotenv::createImmutable(__DIR__ . '/../..'); // Modulstamm
        $dotenv->safeLoad(); // lädt nur, existiert sie nicht, kein Fehler
        self::$envLoaded = true;
    }

    public static function getEnv($key, $default = null)
    {
        self::loadEnv();
        return $_ENV[$key] ?? getenv($key) ?? $default;
    }
}
