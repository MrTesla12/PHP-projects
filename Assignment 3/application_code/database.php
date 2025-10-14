<?php
declare(strict_types=1);

class Database
{
    private \PDO $pdo;

    public function __construct()
    {
        $cfg = require __DIR__ . '/config.php';

        $dsn = 'mysql:host=' . $cfg['DB_HOST'] . ';dbname=' . $cfg['DB_NAME'] . ';charset=utf8mb4';

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // throw exceptions on errors
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       
            PDO::ATTR_EMULATE_PREPARES   => false,                  
        ];

        $this->pdo = new PDO($dsn, $cfg['DB_USER'], $cfg['DB_PASS'], $options);
    }

    public function pdo(): \PDO
    {
        return $this->pdo;
    }
}
