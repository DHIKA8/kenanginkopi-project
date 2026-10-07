<?php
require_once '../partials/header.php';
require_once '../config/db.php';
require_once '../config/helpers.php';


$full_name = $_POST['full_name'] ?? '';
$username  = $_POST['username'] ?? '';
$email     = $_POST['email'] ?? '';
$password  = $_POST['password'] ?? '';
$errors    = [];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($full_name);
    $username  = sanitize($username);
    $email     = sanitize($email);


    if ($full_name === '' || !onlyAlphaSpaces($full_name)) $errors[] = 'Full Name must be alphabetic and spaces only';
    if ($username === '' || !onlyAlpha($username)) $errors[] = 'Username must be alphabetic only';
    if ($email === '' || !validEmailKenangin($email)) $errors[] = 'Invalid email format';
    if (strlen($password) < 8 || !preg_match('/[A-Z]/',$password) || !preg_match('/[a-z]/',$password) || !preg_match('/\d/',$password))
      $errors[] = 'Password must be 8+ chars and include uppercase, lowercase, and number';


    if (empty($errors)) {
      $stmt = $mysqli->prepare("SELECT 1 FROM users WHERE UserName = ? OR UserEmail = ?");
      $stmt->bind_param('ss', $username, $email);
      $stmt->execute();
      if ($stmt->get_result()->fetch_row()) {
        $errors[] = 'Username or Email already exists';
      }
      $stmt->close();
    }


    if (!empty($errors)) {
      foreach ($errors as $e) flash('error', $e);
    } else {
      $result = $mysqli->query("SELECT MAX(UserID) AS maxid FROM users");
      $row = $result->fetch_assoc();
      $lastId = $row['maxid'] ?? 'U0000';
      $num = intval(substr($lastId,1)) + 1;
      $newId = 'U' . str_pad($num,4,'0',STR_PAD_LEFT);

      $stmt = $mysqli->prepare("INSERT INTO users (UserID, FullName, UserName, UserEmail, UserPassword, UserRole) VALUES (?, ?, ?, ?, ?, 'User')");
     
      $stmt->bind_param('sssss', $newId, $full_name, $username, $email, $password);


      if ($stmt->execute()) {
        flash('success', 'Registration successful, please login');
        $stmt->close();
        header('Location: login.php'); exit;
      } else {
        flash('error', 'Registration failed');
        $stmt->close();
      }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KenanginKopi - Register Akun Baru</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="theme-coffee">


<header>
    <?php include '../partials/navbar.php'; ?>
</header>


<main class="container form-page">
    <section>
      <h2 class="section-title text-center">Register Akun Baru</h2>
      <?php include '../partials/flash.php'; ?>
             
      <form method="post" class="registration-form">
        <div class="form-group">
          <label for="full_name">Full Name</label>
          <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($full_name ?? '') ?>" class="form-input" placeholder="Masukkan Nama Lengkap Anda" required>
        </div>
                 
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" value="<?= htmlspecialchars($username ?? '') ?>" class="form-input" placeholder="Username (huruf saja)" required>
        </div>
                 
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" class="form-input" placeholder="Alamat Email Anda" required>
        </div>
                 
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" class="form-input" placeholder="Minimal 8 karakter (U/L/Angka)" required>
        </div>
                 
        <button type="submit" class="btn btn1">Register Akun</button>
                 
        <p class="login-link-text text-center">
          Sudah punya akun? <a href="login.php" class="link-secondary">Login di sini</a>
        </p>
      </form>
    </section>
</main>


<footer>
  <?php include '../partials/footer.php'; ?>
</footer>
</body>
</html>