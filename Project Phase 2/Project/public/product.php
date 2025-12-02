<?php
require __DIR__ . '/../config/session.php';
require __DIR__ . '/../classes/Database.php';
require __DIR__ . '/../classes/Inventory.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$db  = new Database();
$pdo = $db->getConnection();
$inv = new Inventory($pdo);

$product = $id > 0 ? $inv->getProductById($id) : null;

if ($product) {
    $title = $product['name'];
} else {
    $title = 'Product not found';
}

require __DIR__ . '/../ui/header.php';
?>

<?php if (!$product): ?>

  <h1>Product not found</h1>
  <p>We couldnt find the product that you are looking for.</p>
  <p><a href="shop.php">Back to all products</a></p>

<?php else: ?>

  <article class="product-detail">
    <header>
      <h1><?= htmlspecialchars($product['name']) ?></h1>
    </header>

    <div class="product-detail-body">

      <?php if (!empty($product['image'])): ?>
        <div class="product-detail-image">
          <img src="images/products/<?= htmlspecialchars($product['image']) ?>"
               alt="<?= htmlspecialchars($product['name']) ?>">
        </div>
      <?php endif; ?>

      <div class="product-detail-info">
        <p class="product-detail-price">
          <?php $priceDollars = number_format($product['price_cents'] / 100, 2); ?>
          <strong>$<?= $priceDollars ?></strong>
        </p>

        <p class="product-detail-stock">
          In stock: <?= (int)$product['quantity'] ?>
        </p>

        <p class="product-detail-desc">
          <?= nl2br(htmlspecialchars($product['short_desc'])) ?>
        </p>

        <p class="product-detail-back">
          <a href="shop.php">Back to all products</a>
        </p>
      </div>

    </div>
  </article>

<?php endif; ?>

<?php require __DIR__ . '/../ui/footer.php'; ?>
