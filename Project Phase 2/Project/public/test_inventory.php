<?php
require __DIR__ . '/../classes/Database.php';
require __DIR__ . '/../classes/Inventory.php';

$db = new Database();
$pdo = $db->getConnection();
$inv = new Inventory($pdo);

echo "<pre>";

// Test fetch all products
$products = $inv->getAllProducts();
print_r($products);

echo "</pre>";
