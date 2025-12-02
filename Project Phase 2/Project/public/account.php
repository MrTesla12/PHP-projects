<?php $title="Home"; require __DIR__ . '/../ui/header.php'; ?>
<h1>Account</h1>
<div class="grid-two">
  <section>
    <h2>Register</h2>
    <form action="#" method="post">
      <label for="r-name">Name</label>
      <input id="r-name" type="text">
      <label for="r-email">Email</label>
      <input id="r-email" type="email">
      <label for="r-pass">Password</label>
      <input id="r-pass" type="password">
      <button type="submit" disabled>Create Account</button>
    </form>
  </section>
  <section>
    <h2>Login</h2>
    <form action="#" method="post">
      <label for="l-email">Email</label>
      <input id="l-email" type="email">
      <label for="l-pass">Password</label>
      <input id="l-pass" type="password">
      <button type="submit" disabled>Sign In</button>
    </form>
  </section>
</div>
<?php require __DIR__ . '/../ui/footer.php'; ?>
