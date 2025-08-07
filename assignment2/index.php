<?php
// index.php

// 1) Load DB connection
require __DIR__ . '/inc/config.php';

// 2) Load header (also starts session)
require __DIR__ . '/inc/header.php';

// 3) Fetch all items with their owners
$stmt = $pdo->query('
  SELECT 
    i.id, i.title, i.description, i.image_path, i.created_at,
    u.username, u.id AS owner_id
  FROM items i
  JOIN users u ON i.user_id = u.id
  ORDER BY i.created_at DESC
');
$items = $stmt->fetchAll();
?>

<div class="container">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1>All Items</h1>
    <?php if (!empty($_SESSION['user_id'])): ?>
      <a href="src/create_item.php" class="btn btn-success">
        + Add New Item
      </a>
    <?php endif; ?>
  </div>

  <?php if (empty($items)): ?>
    <p class="text-muted">No items yet. <?php if (!empty($_SESSION['user_id'])): ?>Why not <a href="src/create_item.php">add one</a>?<?php endif; ?></p>
  <?php else: ?>
    <div class="row">
      <?php foreach ($items as $item): ?>
        <div class="col-md-4 mb-4">
          <div class="card h-100">
            <img 
              src="<?= htmlspecialchars($item['image_path']) ?>" 
              class="card-img-top" 
              alt="<?= htmlspecialchars($item['title']) ?>"
            >
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($item['title']) ?></h5>
              <p class="card-text"><?= nl2br(htmlspecialchars($item['description'])) ?></p>
              <p class="mt-auto">
                <small class="text-muted">
                  By <?= htmlspecialchars($item['username']) ?> 
                  on <?= date('F j, Y', strtotime($item['created_at'])) ?>
                </small>
              </p>
            </div>
            <?php if (!empty($_SESSION['user_id']) && $_SESSION['user_id'] === $item['owner_id']): ?>
              <div class="card-footer">
                <a 
                  href="src/update_item.php?id=<?= $item['id'] ?>" 
                  class="btn btn-sm btn-primary"
                >Edit</a>
                <a 
                  href="src/delete_item.php?id=<?= $item['id'] ?>" 
                  class="btn btn-sm btn-danger"
                  onclick="return confirm('Are you sure you want to delete this item?');"
                >Delete</a>
              </div>
            <?php endif; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<?php
// 4) Load footer
require __DIR__ . '/inc/footer.php';
