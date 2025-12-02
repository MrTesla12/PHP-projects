<?php

class Database
{
    private string $host = '127.0.0.1';
    private string $db   = 'rule_lawyers';
    private string $user = 'root';
    private string $pass = '';
    private string $charset = 'utf8mb4';

    public function getConnection(): PDO
    {
        $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        return new PDO($dsn, $this->user, $this->pass, $options);
    }
}
