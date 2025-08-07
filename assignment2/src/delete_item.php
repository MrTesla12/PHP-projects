<?php
// src/delete_item.php

// 1) Load DB connection
require __DIR__ . '/../inc/config.php';

// 2) Load header (starts session)
require __DIR__ . '/../inc/header.php';

// 3) Protect this page: only for logged-in users
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 4) Get & validate the item ID from the query string
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: /assignment2/index.php');
    exit;
}

// 5) Fetch the existing item, ensure it belongs to this user
$stmt = $pdo->prepare('SELECT * FROM items WHERE id = ? AND user_id = ?');
$stmt->execute([$id, $_SESSION['user_id']]);
$item = $stmt->fetch();
if (!$item) {
    // Item not found or not owned by this user
    header('Location: /assignment2/index.php');
    exit;
}

// 6) If this is a POST request, proceed with deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // a) Delete the database record
    $delStmt = $pdo->prepare('DELETE FROM items WHERE id = ? AND user_id = ?');
    $delStmt->execute([$id, $_SESSION['user_id']]);

    // b) Remove the image file
    $imageFile = __DIR__ . '/../' . $item['image_path'];
    if (file_exists($imageFile)) {
        unlink($imageFile);
    }

    // c) Redirect back to home
    header('Location: /assignment2/index.php');
    exit;
}
?>

<div class="container">
  <h2 class="mb-4">Delete Item</h2>

  <div class="alert alert-warning">
    <p>Are you sure you want to delete this item?</p>
    <p><strong><?= htmlspecialchars($item['title']) ?></strong></p>
    <img 
      src="/assignment2/<?= htmlspecialchars($item['image_path']) ?>" 
      alt="<?= htmlspecialchars($item['title']) ?>" 
      class="img-thumbnail mb-3" 
      style="max-width: 200px;"
    >
  </div>

  <form action="delete_item.php?id=<?= $id ?>" method="post">
    <button type="submit" class="btn btn-danger">Yes, Delete</button>
    <a href="/assignment2/index.php" class="btn btn-secondary ms-2">Cancel</a>
  </form>
</div>

<?php
// 7) Include footer
require __DIR__ . '/../inc/footer.php';
