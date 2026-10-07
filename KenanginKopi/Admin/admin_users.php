<?php
require_once '../partials/header.php';
require_once '../config/db.php';
require_once '../config/auth.php';
require_once '../config/helpers.php';
requireRole(['Admin']);


if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
  $uid = trim($_POST['user_id'] ?? ''); 
 
  if ($uid !== '' && $uid !== currentUser()['UserID']) {
    try {
        $stmt_td = $mysqli->prepare("DELETE td FROM transactiondetails td JOIN transactions t ON td.TransactionID = t.TransactionID WHERE t.UserID = ?");
        $stmt_td->bind_param('s', $uid);
        $stmt_td->execute();
        $stmt_td->close();

        $stmt_t = $mysqli->prepare("DELETE FROM transactions WHERE UserID = ?");
        $stmt_t->bind_param('s', $uid);
        $stmt_t->execute();
        $stmt_t->close();
      
        $stmt_u = $mysqli->prepare("DELETE FROM users WHERE UserID = ?");
        $stmt_u->bind_param('s', $uid);
       
        if ($stmt_u->execute()) {
            if ($mysqli->affected_rows > 0) {
                flash('success', 'User ' . htmlspecialchars($uid) . ' and all associated data deleted successfully!');
            } else {
                flash('error', 'User ' . htmlspecialchars($uid) . ' not found.');
            }
        } else {
            flash('error', 'Failed to delete user: ' . $mysqli->error);
        }
        $stmt_u->close();
    } catch (mysqli_sql_exception $e) {
        flash('error', 'Database Error: ' . $e->getMessage());
    }
  } else if ($uid === currentUser()['UserID']) {
    flash('error', 'Cannot delete your own account.');
}
  header('Location: admin_users.php'); exit;
}

$users = $mysqli->query("SELECT UserID, FullName, UserName, UserEmail, UserRole FROM users ORDER BY UserID DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KenanginKopi - Manage Users</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="theme-coffee">

<header>
  <?php include '../partials/navbar.php'; ?>
</header>

<main class="container admin-page">
  <h2 class="section-title text-center">Manage Users</h2>
  <?php include '../partials/flash.php'; ?>

  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>UserID</th>
          <th>Full Name</th>
          <th>Username</th>
          <th>Email</th>
          <th>Role</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($u = $users->fetch_assoc()): ?>
        <tr>
          <td><?php echo htmlspecialchars($u['UserID']); ?></td>
          <td><?php echo htmlspecialchars($u['FullName']); ?></td>
          <td><?php echo htmlspecialchars($u['UserName']); ?></td>
          <td><?php echo htmlspecialchars($u['UserEmail']); ?></td>
          <td><?php echo htmlspecialchars($u['UserRole']); ?></td>
          <td>
            <?php if ($u['UserID'] !== currentUser()['UserID']): ?>
              <form method="post" onsubmit="return confirm('Are you sure you want to delete user <?php echo htmlspecialchars($u['UserName']); ?>? This action cannot be undone.');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($u['UserID']); ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            <?php else: ?>
              <span class="text-secondary">Self (Cannot Delete)</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>

  <div class="text-center mt-3">
    <a class="btn btn1 btn-logout" href="index.php" class="btn btn-light">&larr; Back to Admin Dashboard</a>
  </div>
</main>

<footer>
  <?php include '../partials/footer.php'; ?>
</footer>
</body>
</html>