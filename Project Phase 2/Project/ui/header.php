<?php
require_once __DIR__ . '/../config/session.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title><?= isset($title) ? "$title · Rule Lawyers" : "Rule Lawyers" ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <link href="../styles/rulebook.css" rel="stylesheet">

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&family=Spectral+SC:wght@500;700&display=swap" rel="stylesheet">
</head>

<body>

<header class="site-header">
  <div class="bar container">

  
    <a class="brand" href="index.php">Rule Lawyers</a>

  
    <nav class="nav">
      <a href="shop.php">Shop</a>
      <a href="about.php">About</a>
      <a href="contact.php">Contact</a>
      <a class="cta" href="account.php">Register / Login</a>
    </nav>

  
    <div class="header-login">
      <?php if (is_admin_logged_in()): ?>
        <span class="header-login-text">
          Admin: <?= htmlspecialchars(current_admin_name() ?? '') ?>
        </span>
        <a class="header-logout" href="logout.php">Logout</a>
      <?php else: ?>
        <form method="post" action="login.php" class="header-login-form">
          <input type="email" name="email" placeholder="Email" required>
          <input type="password" name="password" placeholder="Password" required>
          <button type="submit">Sign in</button>
        </form>
      <?php endif; ?>
    </div>

  </div>
</header>

<main class="container">
