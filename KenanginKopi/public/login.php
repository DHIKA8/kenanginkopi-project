<?php
require_once '../partials/header.php';
require_once '../config/db.php';
require_once '../config/helpers.php';

if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$username = '';
$password = '';
$remember = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = sanitize($_POST['username'] ?? '');
  $password = $_POST['password'] ?? '';
  $remember = isset($_POST['remember']);

  if ($username === '' || $password === '') {
    flash('error', 'Username and Password must be filled');
  } else {
    $stmt = $mysqli->prepare("SELECT UserID, FullName, UserName, UserEmail, UserRole, UserPassword FROM users WHERE UserName = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $res = $stmt->get_result();
    $stmt->close();

    if ($user = $res->fetch_assoc()) {
     

      if ($password === $user['UserPassword']) {
        $_SESSION['user'] = [
          'UserID'     => $user['UserID'],
          'FullName'   => $user['FullName'],
          'UserName'   => $user['UserName'],
          'UserEmail'  => $user['UserEmail'],
          'UserRole'   => $user['UserRole']
        ];

        if ($remember) {
          setcookie('remember_user_id', (string)$user['UserID'], time() + (7 * 24 * 60 * 60), '/');
        }


        header('Location: index.php');
        exit;
      }
    }
    flash('error', 'Invalid credentials');
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KenanginKopi - Login</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="theme-coffee">

<header>
  <?php include '../partials/navbar.php'; ?>
</header>

<main class="container form-page">
  <section>
    <h2 class="section-title text-center">Login ke KenanginKopi</h2>
    <?php include '../partials/flash.php'; ?>

    <form method="post" class="login-form">
      <div class="form-group">
        <label for="username">Username</label>
        <input type="text" id="username" name="username"
               value="<?= htmlspecialchars($username ?? '') ?>"
               class="form-input" placeholder="Masukkan Username Anda" required>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password"
               class="form-input" placeholder="Password" required>
      </div>

      <div class="form-group form-checkbox">
        <label class="non1" for="remember">
          <input class="non" type="checkbox" id="remember" name="remember" class="checkbox-input"
          <?= $remember ? 'checked' : '' ?>> Remember me (1 week)
        </label>
      </div>

      <button type="submit" class="btn btn1">Login</button>

      <p class="register-link-text text-center">
        Belum punya akun? <a href="register.php" class="link-secondary">Register di sini</a>
      </p>
    </form>
  </section>
</main>

<footer>
  <?php include '../partials/footer.php'; ?>
</footer>
</body>
</html>