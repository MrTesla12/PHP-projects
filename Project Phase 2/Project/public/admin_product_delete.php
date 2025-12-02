<?php
$title = "Delete Product";

require __DIR__ . '/../config/session.php';
require_admin();

require __DIR__ . '/../classes/Database.php';
require __DIR__ . '/../classes/Inventory.php';

$db  = new Database();
$pdo = $db->getConnection();
$inv = new Inventory($pdo);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = $id > 0 ? $inv->getProductById($id) : null;

if (!$product) {
    header('Location: shop.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inv->deleteProduct($id);
    header('Location: shop.php');
    exit;
}

require __DIR__ . '/../ui/header.php';
?>

<h1>Delete Product</h1>

<p>Are you sure you want to delete <strong><?= htmlspecialchars($product['name']) ?></strong>?</p>

<form action="admin_product_delete.php?id=<?= (int)$product['id'] ?>" method="post">
  <button type="submit">Yes, delete</button>
  <a href="shop.php">Cancel</a>
</form>

<?php require __DIR__ . '/../ui/footer.php'; ?>
