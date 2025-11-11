<?php $title="Home"; require __DIR__ . '/../ui/header.php'; ?>
<h1>Contact</h1>
<form action="#" method="post">
  <label for="name">Name</label>
  <input id="name" name="name" type="text">
  <label for="email">Email</label>
  <input id="email" name="email" type="email">
  <label for="msg">Message</label>
  <textarea id="msg" name="message" rows="5"></textarea>
  <button type="submit" disabled>Send(Does nothing)</button>
</form>
<?php require __DIR__ . '/../ui/footer.php'; ?>
