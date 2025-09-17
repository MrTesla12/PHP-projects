<?php
// public/index.php
// --- Load the helper function from application_code/cat_api.php ---
$paths = [
  __DIR__ . '/../application_code/cat_api.php', // Option A: application_code is a sibling of public_html
];

$loaded = false;
foreach ($paths as $p) {
  if (is_file($p)) { require_once $p; $loaded = true; break; }
}
if (!$loaded) {
  die('Setup error: could not find application_code/cat_api.php');
}

// ---  read how many images to fetch from the URL ---

$count = filter_input(INPUT_GET, 'count', FILTER_VALIDATE_INT, [
  'options' => ['min_range' => 1, 'max_range' => 20]
]);

// --- Call helper function to fetch images from The Cat API ---
$result = ada_fetch_cat_images($count);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Ada – COMP1006 Assignment 1 (API Integration)</title>
  <meta name="description" content="My one-page PHP site that fetches images from The Cat API.">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <header class="site-header">
    <h1>API Integration – Cat Gallery (Ada)</h1>
    <p class="tagline">Cat Pictures for Days.</p>
  </header>

  <main>
    <!-- Small control to choose how many images -->
    <form method="get" class="controls" aria-label="Choose how many images (Between 4 and 20)">
      <label for="count">Number of images:</label>
      <select id="count" name="count">
        <?php
          // Build the dropdown keeping the current selection
          $selected = $count ?? 8;
          foreach ([4,8,12,16,20] as $n) {
            $sel = ($n === (int)$selected) ? 'selected' : '';
            echo "<option value=\"$n\" $sel>$n</option>";
          }
        ?>
      </select>
      <button type="submit">Refresh</button>
    </form>

    <section aria-labelledby="results-title">
      <h2 id="results-title">Results</h2>

      <?php if (isset($result['error'])): ?>
        <p role="alert" class="error"><?= htmlspecialchars($result['error']) ?></p>
      <?php else: ?>
        <ul class="cards">
          <?php foreach ($result['items'] as $i => $item): ?>
            <?php
              $img = $item['url'] ?? '';
              $alt = 'Reasons to live number ' . ($i + 1);
            ?>
            <li class="card">
              <figure>
                <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($alt) ?>">
                <figcaption><?= htmlspecialchars($alt) ?></figcaption>
              </figure>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php endif; ?>
    </section>
  </main>

  <footer class="site-footer">
    <small>© AdaAyman – COMP1006. Cats are from The Cat API.</small>
  </footer>
</body>
</html>
