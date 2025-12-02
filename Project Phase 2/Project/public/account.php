<?php
$title = "Account";

require __DIR__ . '/../config/session.php';
require __DIR__ . '/../classes/Database.php';
require __DIR__ . '/../classes/Inventory.php';

$db  = new Database();
$pdo = $db->getConnection();
$inv = new Inventory($pdo);

$registerMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'register') {
    $result = $inv->createAdmin(
        $_POST['name'] ?? '',
        $_POST['email'] ?? '',
        $_POST['password'] ?? '',
        $_POST['password_confirm'] ?? ''
    );

    if ($result['ok']) {
        $registerMessage = 'Admin account created. You can sign in from the header.';
    } else {
        $registerMessage = $result['error'] ?? 'Registration failed.';
    }
}

require __DIR__ . '/../ui/header.php';
?>
<h1>Account</h1>

<?php if ($registerMessage !== ''): ?>
  <p class="form-message"><?= htmlspecialchars($registerMessage) ?></p>
<?php endif; ?>

<div class="grid-two">
  <section>
    <h2>Register Admin</h2>
    <form action="account.php" method="post">
      <input type="hidden" name="action" value="register">

      <label for="r-name">Name</label>
      <input id="r-name" name="name" type="text" required>

      <label for="r-email">Email</label>
      <input id="r-email" name="email" type="email" required>

      <label for="r-pass">Password</label>
      <input id="r-pass" name="password" type="password" required>

      <label for="r-pass2">Confirm Password</label>
      <input id="r-pass2" name="password_confirm" type="password" required>

      <button type="submit">Create Admin Account</button>
    </form>
  </section>

  <section>
    <h2>Login</h2>
    <p>For now please use the header.</p>
  </section>
</div>

<?php require __DIR__ . '/../ui/footer.php'; ?>
