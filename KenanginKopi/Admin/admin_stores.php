<?php
require_once '../partials/header.php';
require_once '../config/db.php';
require_once '../config/auth.php';
require_once '../config/helpers.php';
requireRole(['Admin']);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'delete') {
    $sid = $_POST['store_id'] ?? '';
    if ($sid !== '') {
      $stmt = $mysqli->prepare("DELETE FROM store WHERE StoreID = ?");
      $stmt->bind_param('s', $sid);
      $stmt->execute();
    }
    header('Location: admin_stores.php'); exit;
  }
}


$stores = $mysqli->query("SELECT StoreID, StoreName, StoreLocation FROM store ORDER BY StoreID DESC");
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KenanginKopi - Manage Stores</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="theme-coffee">


<header>
  <?php include '../partials/navbar.php'; ?>
</header>


<main class="container admin-page">
  <h2 class="section-title text-center">Manage Stores</h2>
  <?php include '../partials/flash.php'; ?>


  <div class="action-bar mb-3">
    <a href="index.php" class="btn btn-logout">&larr; Back to Dashboard</a>
    <a href="admin_store_add.php" class="btn btn-danger btn1">Add New Store</a>
  </div>


  <div class="table-responsive">
    <table class="admin-table">
      <thead>
        <tr>
          <th>StoreID</th>
          <th>Name</th>
          <th>Location</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($stores->num_rows === 0): ?>
          <tr>
            <td colspan="4" class="text-center">No stores found. Please add a new store.</td>
          </tr>
        <?php else: ?>
          <?php while ($s = $stores->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($s['StoreID']); ?></td>
            <td><?php echo htmlspecialchars($s['StoreName']); ?></td>
            <td><?php echo htmlspecialchars($s['StoreLocation']); ?></td>
            <td class="action-buttons">
              <a class="btn btn1 btn-logoout" href="admin_coffees.php?store_id=<?php echo $s['StoreID']; ?>" class="btn btn-secondary btn-sm">Manage Coffees</a>
              <form method="post" class="inline-form" onsubmit="return confirm('Are you sure you want to delete store <?php echo htmlspecialchars($s['StoreName']); ?>? This will also affect associated data.');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="store_id" value="<?php echo htmlspecialchars($s['StoreID']); ?>">
                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
              </form>
            </td>
          </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</main>


<footer>
  <?php include '../partials/footer.php'; ?>
</footer>
</body>
</html>