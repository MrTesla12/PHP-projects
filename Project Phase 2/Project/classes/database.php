<?php

class Database
{
    public function getConnection(): PDO
    {
       
        if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
            // LOCAL (XAMPP)
            $dsn  = 'mysql:host=127.0.0.1;dbname=rule_lawyers;charset=utf8mb4';
            $user = 'root';
            $pass = '';
        } else {
            // SERVER (Georgian)
            $dsn  = '';
            $user = '';
            $pass = '';
        }

        $pdo = new PDO($dsn, $user, $pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        ]);

        return $pdo;
    }
}
