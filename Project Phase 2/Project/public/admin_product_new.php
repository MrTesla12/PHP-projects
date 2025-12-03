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

    // --- simple validation on raw input ---
    if (trim($name) === '' || trim($description) === '') {
        $message = 'Name and description are required.';
    }

    $priceVal = filter_var($price, FILTER_VALIDATE_FLOAT);
    if ($message === '' && ($priceVal === false || $priceVal <= 0)) {
        $message = 'Price must be a positive number.';
    }

    $qtyVal = filter_var($quantity, FILTER_VALIDATE_INT, [
        'options' => ['min_range' => 0],
    ]);
    if ($message === '' && $qtyVal === false) {
        $message = 'Quantity must be zero or more.';
    }

    // --- file upload handling ---
    $imageName = '';
    if ($message === '') {
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $imageName = basename($_FILES['image']['name']);

            // NOTE: admin_product_new.php is in /public
            // images/products is one level up, in /images/products
            $targetDir = __DIR__ . '/../images/products/';

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
    }

    // --- call Inventory only if no earlier error ---
    if ($message === '') {
        $result = $inv->createProduct($name, $description, $priceVal, $qtyVal, $imageName);

        if (!empty($result['ok'])) {
            // success – go back to the shop so you can immediately see it
            header('Location: shop.php');
            exit;
        }

        $message = $result['error'] ?? 'Could not create product.';
    }
}

require __DIR__ . '/../ui/header.php';
?>

<h1>New Product</h1>

<?php if ($message !== ''): ?>
  <p class="form-message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<form action="admin_product_new.php" method="post" enctype="multipart/form-data">
  <p>
    <label for="p-name">Name</label><br>
    <input id="p-name" name="name" type="text" required>
  </p>

  <p>
    <label for="p-desc">Description</label><br>
    <textarea id="p-desc" name="description" required></textarea>
  </p>

  <p>
    <label for="p-price">Price (dollars)</label><br>
    <input id="p-price" name="price" type="number" step="0.01" min="0.01" required>
  </p>

  <p>
    <label for="p-qty">Quantity in stock</label><br>
    <input id="p-qty" name="quantity" type="number" min="0" value="0" required>
  </p>

  <p>
    <label for="p-img">Image file</label><br>
    <input id="p-img" name="image" type="file" accept="image/*" required>
  </p>

  <p>
    <button type="submit">Create Product</button>
  </p>
</form>

<p class="mt">
  <a href="shop.php">Back to all products</a>
</p>

<?php require __DIR__ . '/../ui/footer.php'; ?>
