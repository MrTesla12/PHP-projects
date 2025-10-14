<?php
//error_reporting(E_ALL);
//ini_set('display_errors', '1'); (Used for troubleshooting, comment out for production)

$optA = dirname(__DIR__) . '/application_code';
$optB = __DIR__ . '/application_code';
$appDir = is_dir($optA) ? $optA : (is_dir($optB) ? $optB : null);
if (!$appDir) { die("App dir not found"); }

require_once $appDir . '/Repository.php';

$repo = new Repository();
$profiles = $repo->fetchAllProfiles();

require $appDir . '/header.php';
?>
<h2>All Profiles</h2>
<p><a href="submission.php">Create a new profile</a></p>

<?php if (!$profiles): ?>
  <p class="muted">No profiles yet. Be the first to create one.</p>
<?php else: ?>
  <ul class="grid">
    <?php foreach ($profiles as $p): ?>
      <li class="card">
        <?php if (!empty($p['image_path'])): ?>
          <img src="<?= htmlspecialchars($p['image_path']) ?>"
               alt="Profile image of <?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?>">
        <?php endif; ?>
        <h3><?= htmlspecialchars($p['first_name'] . ' ' . $p['last_name']) ?></h3>
        <div class="muted"><?= htmlspecialchars($p['email']) ?></div>
        <?php if (!empty($p['bio'])): ?>
          <p><?= nl2br(htmlspecialchars($p['bio'])) ?></p>
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>
<?php endif; ?>

<?php require $appDir . '/footer.php'; ?>
