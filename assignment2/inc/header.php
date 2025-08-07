<?php
// inc/header.php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Assignment 2</title>
  <!-- Bootstrap CSS -->
  <link 
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" 
    rel="stylesheet"
  >
  <!-- Your custom CSS -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body class="d-flex flex-column min-vh-100">
  <nav class="navbar navbar-light bg-light mb-4">
    <div class="container">
      <a class="navbar-brand" href="/assignment2/index.php">My App</a>
      <!-- Removed the toggler button entirely -->
      <ul class="navbar-nav ms-auto flex-row">
        <?php if (empty($_SESSION['user_id'])): ?>
          <li class="nav-item me-2">
            <a class="btn btn-outline-primary" href="/assignment2/src/login.php">Log In</a>
          </li>
          <li class="nav-item">
            <a class="btn btn-primary" href="/assignment2/src/register.php">Register</a>
          </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="btn btn-secondary" href="/assignment2/src/logout.php">Log Out</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </nav>
