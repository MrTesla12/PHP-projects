<?php
declare(strict_types=1);

$appDir = dirname(__DIR__) . '/application_code';
require_once $appDir . '/Repository.php';

$config = require $appDir . '/config.php';
$uploadDir = $config['UPLOAD_DIR'];   
$uploadUrl = $config['UPLOAD_URL'];   

if (!is_dir($uploadDir)) {
  mkdir($uploadDir, 0775, true);
}

$errors = [];
$successMessage = '';
$posted = ['first_name'=>'','last_name'=>'','email'=>'','bio'=>''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    //  Read and trim inputs
    $first = trim($_POST['first_name'] ?? '');
    $last  = trim($_POST['last_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $bio   = trim($_POST['bio'] ?? '');

    $posted = ['first_name'=>$first,'last_name'=>$last,'email'=>$email,'bio'=>$bio];

    //  Validate text fields (simple and clear)
    if ($first === '') { $errors[] = 'First name is required.'; }
    if ($last  === '') { $errors[] = 'Last name is required.'; }
    if ($email === '') { $errors[] = 'Email is required.'; }
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Email format is invalid.';
    }

    //  Validate file upload
    if (!isset($_FILES['photo']) || $_FILES['photo']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors[] = 'Profile image is required.';
    }

    // If we have a file, do basic checks
    $finalRelPath = null; 
    $mime = null;
    $sizeBytes = null;

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $tmp  = $_FILES['photo']['tmp_name'];
        $name = $_FILES['photo']['name'];
        $sizeBytes = (int)$_FILES['photo']['size'];

        // Limit: 2 MB (Just the sweet spot)
        if ($sizeBytes > 2 * 1024 * 1024) {
            $errors[] = 'Image must be 2MB or smaller.';
        }

        
        $f = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($f, $tmp);
        finfo_close($f);

        $allowed = ['image/jpeg'=>'jpg', 'image/png'=>'png', 'image/webp'=>'webp'];
        if (!isset($allowed[$mime])) {
            $errors[] = 'Only JPG, PNG, or WEBP images are allowed.';
        }

        // Build a safe filename if no errors so far
        if (!$errors) {
            $ext = $allowed[$mime];
            // Make a simple unique name (timestamp + random number )
            $base = 'profile_' . time() . '_' . mt_rand(1000,9999) . '.' . $ext;

            $finalAbsPath = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $base;
            $finalRelPath = rtrim($uploadUrl, '/') . '/' . $base; // stored in DB and used in <img src>

            if (!move_uploaded_file($tmp, $finalAbsPath)) {
                $errors[] = 'Could not save uploaded file.';
            }
        }
    }

    //  If no errors, insert row
    if (!$errors) {
        try {
            $repo = new Repository();
            $newId = $repo->createProfile(
                $first,
                $last,
                $email,
                $bio === '' ? null : $bio,
                $finalRelPath ?? '',  
                $mime,
                $sizeBytes
            );
            $successMessage = 'Profile created successfully! (ID: ' . (int)$newId . ')';
            $posted = ['first_name'=>'','last_name'=>'','email'=>'','bio'=>''];
        } catch (PDOException $e) {
            $errors[] = 'Could not save profile. (Is this email already used?)';
        }
    }
}

// Show page
require $appDir . '/header.php';
?>

<h2>Create Profile</h2>

<?php if ($successMessage): ?>
  <p class="success"><?= htmlspecialchars($successMessage) ?></p>
  <p><a href="index.php">Go to All Profiles</a></p>
<?php endif; ?>

<?php if ($errors): ?>
  <div class="error">
    <p>Please fix the following:</p>
    <ul>
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form action="submission.php" method="post" enctype="multipart/form-data" novalidate>
  <div>
    <label for="first_name">First name</label>
    <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($posted['first_name']) ?>" required>
  </div>

  <div>
    <label for="last_name">Last name</label>
    <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($posted['last_name']) ?>" required>
  </div>

  <div>
    <label for="email">Email</label>
    <input type="email" id="email" name="email" value="<?= htmlspecialchars($posted['email']) ?>" required>
  </div>

  <div>
    <label for="bio">Bio <span class="help">(optional)</span></label>
    <textarea id="bio" name="bio" rows="4"><?= htmlspecialchars($posted['bio']) ?></textarea>
  </div>

  <div>
    <label for="photo">Profile image (JPG/PNG/WEBP, ≤ 2MB)</label>
    <input type="file" id="photo" name="photo" accept=".jpg,.jpeg,.png,.webp" required>
  </div>

  <button type="submit">Create Profile</button>
</form>

<?php require $appDir . '/footer.php'; ?>
