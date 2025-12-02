<?php
$title = "Shop";

require __DIR__ . '/../config/session.php';
require __DIR__ . '/../classes/Database.php';
require __DIR__ . '/../classes/Inventory.php';

$db  = new Database();
$pdo = $db->getConnection();
$inv = new Inventory($pdo);

$products = $inv->getAllProducts();

require __DIR__ . '/../ui/header.php';
?>

<h1>Shop</h1>

<!-- All Products Page – populated from database -->
<div class="grid-three">
  <?php if (empty($products)): ?>
    <p>No products available yet.</p>
  <?php else: ?>
    <?php foreach ($products as $product): ?>
      <?php
        $priceDollars = number_format($product['price_cents'] / 100, 2);
      ?>
      <article class="item-card">
        <?php if (!empty($product['image'])): ?>
          <div class="item-thumb">
            <img src="images/products/<?= htmlspecialchars($product['image']) ?>"
                 alt="<?= htmlspecialchars($product['name']) ?>">
          </div>
        <?php endif; ?>

        <h2><?= htmlspecialchars($product['name']) ?></h2>
        <p><?= htmlspecialchars($product['short_desc']) ?></p>
        <p><strong>$<?= $priceDollars ?></strong></p>
        <p>In stock: <?= (int)$product['quantity'] ?></p>

        <p>
          <a href="product.php?id=<?= (int)$product['id'] ?>">View details</a>
        </p>

        <?php if (is_admin_logged_in()): ?>
          <p class="admin-links">
            <a href="admin_product_edit.php?id=<?= (int)$product['id'] ?>">Edit</a> |
            <a href="admin_product_delete.php?id=<?= (int)$product['id'] ?>">Delete</a>
          </p>
        <?php endif; ?>
      </article>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/../ui/footer.php'; ?>
