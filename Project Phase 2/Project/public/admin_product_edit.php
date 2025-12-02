<?php
$title = "Edit Product";

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

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = $_POST['name']        ?? '';
    $description = $_POST['description'] ?? '';
    $price       = $_POST['price']       ?? '';
    $quantity    = $_POST['quantity']    ?? '';

    $newImageName = null;

    if (!empty($_FILES['image']['name'])) {
        $original = basename($_FILES['image']['name']);
        $ext      = pathinfo($original, PATHINFO_EXTENSION);
        $safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($original, PATHINFO_FILENAME));
        $newImageName = $safeBase . '_' . uniqid() . '.' . $ext;

        $targetDir  = __DIR__ . '/images/products/';
        $targetFile = $targetDir . $newImageName;

        if (is_uploaded_file($_FILES['image']['tmp_name'])) {
            if (!move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                $message = 'Error saving new image file.';
                $newImageName = null;
            }
        } else {
            $message = 'Image upload failed.';
            $newImageName = null;
        }
    }

    if ($message === '') {
        $result = $inv->updateProduct($id, $name, $description, $price, $quantity, $newImageName);
        if ($result['ok']) {
            $message = 'Product updated.';
            $product = $inv->getProductById($id); // refresh values
        } else {
            $message = $result['error'] ?? 'Could not update product.';
        }
    }
}

require __DIR__ . '/../ui/header.php';
?>

<h1>Edit Product</h1>

<?php if ($message !== ''): ?>
  <p class="form-message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form action="admin_product_edit.php?id=<?= (int)$product['id'] ?>" method="post" enctype="multipart/form-data">
  <label for="p-name">Name</label>
  <input id="p-name" name="name" type="text" required
         value="<?= htmlspecialchars($product['name']) ?>">

  <label for="p-desc">Description</label>
  <textarea id="p-desc" name="description" rows="4" cols="40" required><?= htmlspecialchars($product['short_desc']) ?></textarea>

  <label for="p-price">Price ($)</label>
  <input id="p-price" name="price" type="number" step="0.01" min="0" required
         value="<?= htmlspecialchars(number_format($product['price_cents'] / 100, 2, '.', '')) ?>">

  <label for="p-qty">Quantity in stock</label>
  <input id="p-qty" name="quantity" type="number" min="0" required
         value="<?= (int)$product['quantity'] ?>">

  <?php if (!empty($product['image'])): ?>
    <p>Current image:</p>
    <div class="item-thumb">
      <img src="images/products/<?= htmlspecialchars($product['image']) ?>"
           alt="<?= htmlspecialchars($product['name']) ?>">
    </div>
  <?php endif; ?>

  <label for="p-image">Replace image (optional)</label>
  <input id="p-image" name="image" type="file" accept="image/*">

  <button type="submit">Save Changes</button>
</form>

<p class="mt"><a href="shop.php">Back to all products</a></p>

<?php require __DIR__ . '/../ui/footer.php'; ?>
