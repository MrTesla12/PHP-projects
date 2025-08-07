<?php
// src/register.php

// 1) Load DB connection and header
require __DIR__ . '/../inc/config.php';
require __DIR__ . '/../inc/header.php';

// 2) Prepare an errors array
$errors = [];

// 3) Handle submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username     = trim($_POST['username'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $password     = $_POST['password'] ?? '';
    $confirm_pass = $_POST['confirm_password'] ?? '';

    // Basic validation
    if ($username === '' || $email === '' || $password === '' || $confirm_pass === '') {
        $errors[] = 'All fields are required.';
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Invalid email address.';
    }
    if ($password !== '' && $password !== $confirm_pass) {
        $errors[] = 'Passwords do not match.';
    }

    // Uniqueness
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = ? OR email = ?');
        $stmt->execute([$username, $email]);
        if ($stmt->fetchColumn() > 0) {
            $errors[] = 'Username or email already taken.';
        }
    }

    // Insert user
    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare('
            INSERT INTO users (username, email, password)
            VALUES (?, ?, ?)
        ')->execute([$username, $email, $hash]);
        header('Location: login.php');
        exit;
    }
}
?>

<div class="container">
  <h2 class="mb-4">Register a New Account</h2>

  <!-- Show errors if any -->
  <?php if ($errors): ?>
    <div class="alert alert-danger">
      <ul class="mb-0">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <!-- **THIS IS YOUR FORM** -->
  <form action="register.php" method="post" novalidate>
    <div class="mb-3">
      <label for="username" class="form-label">Username</label>
      <input 
        type="text" 
        id="username" 
        name="username" 
        class="form-control" 
        value="<?= htmlspecialchars($username ?? '') ?>" 
        required
      >
    </div>

    <div class="mb-3">
      <label for="email" class="form-label">Email</label>
      <input 
        type="email" 
        id="email" 
        name="email" 
        class="form-control" 
        value="<?= htmlspecialchars($email ?? '') ?>" 
        required
      >
    </div>

    <div class="mb-3">
      <label for="password" class="form-label">Password</label>
      <input 
        type="password" 
        id="password" 
        name="password" 
        class="form-control" 
        required
      >
    </div>

    <div class="mb-3">
      <label for="confirm_password" class="form-label">Confirm Password</label>
      <input 
        type="password" 
        id="confirm_password" 
        name="confirm_password" 
        class="form-control" 
        required
      >
    </div>

    <button type="submit" class="btn btn-primary">Register</button>
  </form>
</div>

<?php
// 4) Footer
require __DIR__ . '/../inc/footer.php';
