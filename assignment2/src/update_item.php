<?php
// src/update_item.php

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
    header('Location: /assignment2/index.php');
    exit;
}

// 6) Prepare errors array
$errors = [];

// 7) Handle the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // a) Trim inputs
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $newImage    = $item['image_path']; // default to old path

    // b) Validate required fields
    if ($title === '') {
        $errors[] = 'Title is required.';
    }

    // c) If a new file was uploaded, validate & move it
    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // c.1) Enforce max file size (2 MB)
        $maxSize = 2 * 1024 * 1024;
        if ($_FILES['image']['size'] > $maxSize) {
            $errors[] = 'Image must be 2 MB or smaller.';
        }

        $tmpPath  = $_FILES['image']['tmp_name'];
        $origName = $_FILES['image']['name'];
        $ext      = pathinfo($origName, PATHINFO_EXTENSION);

        // c.2) Extension whitelist
        if (!in_array(strtolower($ext), ['jpg','jpeg','png','gif'])) {
            $errors[] = 'Only JPG, PNG or GIF images are allowed.';
        } else {
            // c.3) Move new image and delete old
            $newName = uniqid('img_') . '.' . $ext;
            $dest    = __DIR__ . '/../images/' . $newName;
            if (!move_uploaded_file($tmpPath, $dest)) {
                $errors[] = 'Failed to move uploaded image.';
            } else {
                // Delete the old image file
                $oldFile = __DIR__ . '/../' . $item['image_path'];
                if (file_exists($oldFile)) {
                    unlink($oldFile);
                }
                $newImage = 'images/' . $newName;
            }
        }
    }

    // d) If no errors, update the record
    if (empty($errors)) {
        $update = $pdo->prepare('
          UPDATE items
          SET title = ?, description = ?, image_path = ?
          WHERE id = ? AND user_id = ?
        ');
        $update->execute([
            $title,
            $description,
            $newImage,
            $id,
            $_SESSION['user_id']
        ]);

        header('Location: /assignment2/index.php');
        exit;
    }
}

// 8) Use old values if form not submitted or there were errors
$title       = $title       ?? $item['title'];
$description = $description ?? $item['description'];
$imagePath   = $item['image_path'];
?>

<div class="container">
  <h2 class="mb-4">Edit Item</h2>

  <!-- Display validation errors -->
  <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- Edit Item Form -->
  <form 
    action="update_item.php?id=<?= $id ?>" 
    method="post" 
    enctype="multipart/form-data" 
    novalidate
  >
    <div class="mb-3">
      <label for="title" class="form-label">Title</label>
      <input 
        type="text" 
        id="title" 
        name="title" 
        class="form-control" 
        value="<?= htmlspecialchars($title) ?>" 
        required
      >
    </div>

    <div class="mb-3">
      <label for="description" class="form-label">Description</label>
      <textarea 
        id="description" 
        name="description" 
        class="form-control" 
        rows="4"
      ><?= htmlspecialchars($description) ?></textarea>
    </div>

    <div class="mb-3">
      <p>Current Image:</p>
      <img 
        src="/assignment2/<?= htmlspecialchars($imagePath) ?>" 
        alt="Current Image" 
        class="img-thumbnail mb-3" 
        style="max-width: 200px;"
      >
    </div>

    <div class="mb-3">
      <label for="image" class="form-label">Replace Image (optional)</label>
      <input 
        type="file" 
        id="image" 
        name="image" 
        class="form-control" 
        accept="image/*"
      >
      <div class="form-text">Max size: 2 MB. Allowed types: JPG, PNG, GIF.</div>
    </div>

    <button type="submit" class="btn btn-primary">Save Changes</button>
    <a href="/assignment2/index.php" class="btn btn-secondary ms-2">Cancel</a>
  </form>
</div>

<?php
// 9) Include footer
require __DIR__ . '/../inc/footer.php';
