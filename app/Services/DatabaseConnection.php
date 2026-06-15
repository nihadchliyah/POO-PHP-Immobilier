<?php

class DatabaseConnection
{
    private static ?self $instance = null;

    private static string $dsn      = 'sqlite::memory:';
    private static string $user     = '';
    private static string $password = '';

    private PDO $pdo;

    private function __construct()
    {
        $this->pdo = new PDO(
            self::$dsn,
            self::$user,
            self::$password,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    }

    public static function configure(string $dsn, string $user = '', string $password = ''): void
    {
        if (self::$instance !== null) {
            throw new RuntimeException("Connexion déjà établie : configurez avant le premier getInstance().");
        }
        self::$dsn      = $dsn;
        self::$user     = $user;
        self::$password = $password;
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection(): PDO
    {
        return $this->pdo;
    }

    public function __clone(): void
    {
        throw new RuntimeException("Le clonage du Singleton DatabaseConnection est interdit.");
    }

    public function __wakeup(): void
    {
        throw new RuntimeException("La désérialisation du Singleton DatabaseConnection est interdite.");
    }
}
