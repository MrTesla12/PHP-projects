<?php
// src/create_item.php

// 1) Load database connection
require __DIR__ . '/../inc/config.php';

// 2) Load header (starts session)
require __DIR__ . '/../inc/header.php';

// 3) Protect this page: only for logged-in users
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// 4) Prepare an errors array
$errors = [];

// 5) Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // a) Collect & trim inputs
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // b) Validate required fields
    if ($title === '') {
        $errors[] = 'Title is required.';
    }

    // c) Handle file upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // c.1) Enforce max file size (2 MB)
        $maxSize = 2 * 1024 * 1024;
        if ($_FILES['image']['size'] > $maxSize) {
            $errors[] = 'Image must be 2 MB or smaller.';
        }

        $tmpPath  = $_FILES['image']['tmp_name'];
        $origName = $_FILES['image']['name'];
        $ext      = pathinfo($origName, PATHINFO_EXTENSION);

        // c.2) Only allow common image extensions
        if (!in_array(strtolower($ext), ['jpg','jpeg','png','gif'])) {
            $errors[] = 'Only JPG, PNG, or GIF images are allowed.';
        } else {
            // c.3) Create a unique filename and move the file
            $newName  = uniqid('img_') . '.' . $ext;
            $destPath = __DIR__ . '/../images/' . $newName;
            if (!move_uploaded_file($tmpPath, $destPath)) {
                $errors[] = 'Failed to move uploaded image.';
            }
        }
    } else {
        $errors[] = 'Image is required.';
    }

    // d) If no errors so far, insert into database
    if (empty($errors)) {
        $stmt = $pdo->prepare('
            INSERT INTO items (user_id, title, description, image_path)
            VALUES (?, ?, ?, ?)
        ');
        $stmt->execute([
            $_SESSION['user_id'],
            $title,
            $description,
            'images/' . $newName
        ]);

        // e) Redirect back to home page
        header('Location: ../index.php');
        exit;
    }
}
?>

<div class="container">
  <h2 class="mb-4">Add New Item</h2>

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

  <!-- Create Item Form -->
  <form action="create_item.php" method="post" enctype="multipart/form-data" novalidate>
    <div class="mb-3">
      <label for="title" class="form-label">Title</label>
      <input 
        type="text" 
        id="title" 
        name="title" 
        class="form-control" 
        value="<?= htmlspecialchars($title ?? '') ?>" 
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
      ><?= htmlspecialchars($description ?? '') ?></textarea>
    </div>

    <div class="mb-3">
      <label for="image" class="form-label">Upload Image</label>
      <input 
        type="file" 
        id="image" 
        name="image" 
        class="form-control" 
        accept="image/*" 
        required
      >
      <div class="form-text">Max size: 2 MB. Allowed types: JPG, PNG, GIF.</div>
    </div>

    <button type="submit" class="btn btn-success">Create Item</button>
  </form>
</div>

<?php
// 6) Include footer
require __DIR__ . '/../inc/footer.php';
