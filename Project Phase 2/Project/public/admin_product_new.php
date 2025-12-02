<?php
$title = "New Product";

require __DIR__ . '/../config/session.php';
require_admin();

require __DIR__ . '/../classes/Database.php';
require __DIR__ . '/../classes/Inventory.php';

$db  = new Database();
$pdo = $db->getConnection();
$inv = new Inventory($pdo);

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = $_POST['name']        ?? '';
    $description = $_POST['description'] ?? '';
    $price       = $_POST['price']       ?? '';
    $quantity    = $_POST['quantity']    ?? '';

    $imageName = '';
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageName = basename($_FILES['image']['name']);
        $targetDir = __DIR__ . '/images/products/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }
        $targetPath = $targetDir . $imageName;
        if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $message = 'Could not save uploaded image.';
        }
    } else {
        $message = 'Image is required.';
    }

    if ($message === '') {
        $result = $inv->createProduct($name, $description, $price, $quantity, $imageName);

        if ($result['ok']) {
            $message = 'Product created.';
        } else {
            $message = $result['error'] ?? 'Could not create product.';
        }
    }
}

require __DIR__ . '/../ui/header.php';
?>

<h1>New Product</h1>

<?php if ($message !== ''): ?>
  <p class="form-message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form action="admin_product_new.php" method="post" enctype="multipart/form-data">
  <label for="p-name">Name</label>
  <input id="p-name" name="name" type="text" required>

  <label for="p-desc">Description</label>
  <textarea id="p-desc" name="description" required></textarea>

  <label for="p-price">Price (dollars)</label>
  <input id="p-price" name="price" type="number" step="0.01" min="0" required>

  <label for="p-qty">Quantity in stock</label>
  <input id="p-qty" name="quantity" type="number" min="0" required>

  <label for="p-img">Image file</label>
  <input id="p-img" name="image" type="file" accept="image/*" required>

  <button type="submit">Create Product</button>
</form>

<p class="mt">
  <a href="shop.php">Back to all products</a>
</p>

<?php require __DIR__ . '/../ui/footer.php'; ?>
