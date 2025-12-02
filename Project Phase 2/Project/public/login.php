<?php
require __DIR__ . '/../config/session.php';
require __DIR__ . '/../classes/Database.php';
require __DIR__ . '/../classes/Inventory.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$db  = new Database();
$pdo = $db->getConnection();
$inv = new Inventory($pdo);

$admin = $inv->getAdminByEmail($email);

if (!$admin || !password_verify($password, $admin['password'])) {
    $_SESSION['login_error'] = 'Invalid email or password.';
    header('Location: account.php');
    exit;
}

$_SESSION['admin_id']   = (int)$admin['id'];
$_SESSION['admin_name'] = $admin['name'];

header('Location: shop.php'); 
exit;
