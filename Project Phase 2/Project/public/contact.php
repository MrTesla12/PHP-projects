<?php $title="Contact"; require __DIR__ . '/../ui/header.php'; ?>

<h1>Contact</h1>

<section class="contact-wrap">
  <div class="contact-card">
    <p class="contact-lede">Questions about stock or events? Send a note.</p>

    <form method="post" action="#">
      <div class="field">
        <label for="c-name">Name</label>
        <input id="c-name" type="text" placeholder="Your name">
      </div>

      <div class="field">
        <label for="c-email">Email</label>
        <input id="c-email" type="email" placeholder="you@example.com">
      </div>

      <div class="field field-full">
        <label for="c-message">Message</label>
        <textarea id="c-message" placeholder="What do you need?"></textarea>
      </div>

      <div class="form-actions">
        <button type="submit" disabled>Send (does nothing)</button>
      </div>
    </form>
  </div>
</section>

<?php require __DIR__ . '/../ui/footer.php'; ?>
