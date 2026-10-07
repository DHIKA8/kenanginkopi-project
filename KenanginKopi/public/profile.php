<?php
require_once '../partials/header.php';
require_once '../config/auth.php';
requireLogin();
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KenanginKopi - User Profile</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<header>
    <?php include '../partials/navbar.php'; ?>
</header>

<main class="container3 form-page">
    <section class="profile-section">
      <h2 class="section-title text-center">Your Profile</h2>
     
      <?php include '../partials/flash.php'; ?>
     
      <div class="container1">
        <p ><strong class="non2 btn-login">User ID:</strong> <?php echo htmlspecialchars($user['UserID']); ?></p>
        <p ><strong class="non2 btn-login">Full Name:</strong> <?php echo htmlspecialchars($user['FullName']); ?></p>
        <p ><strong class="non2 btn-login">Username:</strong> <?php echo htmlspecialchars($user['UserName']); ?></p>
        <p ><strong class="non2 btn-login">Email:</strong> <?php echo htmlspecialchars($user['UserEmail']); ?></p>
        <p ><strong class="non2 btn-login">Role:</strong> <?php echo htmlspecialchars($user['UserRole']); ?></p>
      </div>

      <div class="btn-group">
        <a href="edit_profile.php" class="btn btn-login">Edit Profile</a>
        <a href="history.php" class="btn btn-login">Order History</a>
        <a href="index.php" class="btn btn-login">Back to Home</a>
      </div>
     
      
    </section>
   
</main>
 <footer>
    <?php include '../partials/footer.php'; ?>
</footer>
</body>
</html>