<?php
require_once '../partials/header.php';
require_once '../config/db.php';
require_once '../config/auth.php';
require_once '../config/helpers.php';
requireLogin();

$user = currentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = sanitize($_POST['username'] ?? '');
  $email    = sanitize($_POST['email'] ?? '');
  $errors = [];

  if ($username === '' || !onlyAlpha($username)) $errors[] = 'Username must be alphabetic only';
  if ($email === '' || !validEmailKenangin($email)) $errors[] = 'Invalid email format';

  // cek email unik jika berubah
  if ($email !== $user['UserEmail']) {
    $stmt = $mysqli->prepare("SELECT 1 FROM users WHERE UserEmail = ? AND UserID <> ?");
    $stmt->bind_param('ss', $email, $user['UserID']);
    $stmt->execute();
    if ($stmt->get_result()->fetch_row()) $errors[] = 'Email already in use';
  }

  // cek username unik jika berubah
  if ($username !== $user['UserName']) {
    $stmt = $mysqli->prepare("SELECT 1 FROM users WHERE UserName = ? AND UserID <> ?");
    $stmt->bind_param('ss', $username, $user['UserID']);
    $stmt->execute();
    if ($stmt->get_result()->fetch_row()) $errors[] = 'Username already in use';
  }

  if (!empty($errors)) {
    foreach ($errors as $e) flash('error', $e);
  } else {
    $stmt = $mysqli->prepare("UPDATE users SET UserName = ?, UserEmail = ? WHERE UserID = ?");
    $stmt->bind_param('sss', $username, $email, $user['UserID']);
    if ($stmt->execute()) {
      $_SESSION['user']['UserName'] = $username;
      $_SESSION['user']['UserEmail'] = $email;
      header('Location: profile.php'); exit;
    } else {
      flash('error', 'Update failed');
    }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KenanginKopi - Edit Profile</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="theme-coffee">

<header>
    <?php include '../partials/navbar.php'; ?>
</header>

<main class="container form-page">
    <section class="edit-profile-section">
      <h2 class="section-title text-center">Edit Profile</h2>
     
      <?php include '../partials/flash.php'; ?>
             
      <form class="" method="post" class="registration-form">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" 
                 value="<?php echo htmlspecialchars($username ?? $user['UserName']); ?>" 
                 class="form-input" placeholder="Masukkan Username Baru" required>
        </div>
                 
        <div class="form-group">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" 
                 value="<?php echo htmlspecialchars($email ?? $user['UserEmail']); ?>" 
                 class="form-input" placeholder="Masukkan Email Baru" required>
        </div>
                 
        <button type="submit" class="btn btn1">Save Changes</button>
                 
        <p class="text-center">
          <a class="btn btn-logout" href="profile.php" class="link-secondary">&larr; Back to Profile</a>
        </p>
      </form>
    </section>
</main>

<footer>
    <?php include '../partials/footer.php'; ?>
</footer>
</body>
</html>