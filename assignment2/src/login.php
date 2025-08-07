<?php

// 1) Load database & header
require __DIR__ . '/../inc/config.php';
require __DIR__ . '/../inc/header.php';

// 2) Prepare errors array
$errors = [];

// 3) If form submitted, handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';

    // Basic checks
    if ($email === '' || $password === '') {
        $errors[] = 'Both email and password are required.';
    }

    // If no errors, fetch user
    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Success! Save user ID in session and redirect
            $_SESSION['user_id'] = $user['id'];
            header('Location: /assignment2/index.php');
            exit;
        } else {
            $errors[] = 'Invalid email or password.';
        }
    }
}
?>

<div class="container">
  <h2 class="mb-4">Log In</h2>

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

  <!-- Login form -->
  <form action="login.php" method="post" novalidate>
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

    <button type="submit" class="btn btn-primary">Log In</button>
  </form>
</div>

<?php
// 4) Footer
require __DIR__ . '/../inc/footer.php';
