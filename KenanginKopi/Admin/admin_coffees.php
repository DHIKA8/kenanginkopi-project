  <?php
  require_once '../partials/header.php';
  require_once '../config/db.php';
  require_once '../config/auth.php';
  require_once '../config/helpers.php';
  requireRole(['Admin']);

  $store_id = trim($_GET['store_id'] ?? ''); 
  if ($store_id === '') { header('Location: admin_stores.php'); exit; }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
  $coffee_id = trim($_POST['coffee_id'] ?? ''); 
  
    if ($coffee_id !== '') {
  $stmt_sc = $mysqli->prepare("DELETE FROM storecoffee WHERE CoffeeID = ?");
          $stmt_sc->bind_param('s', $coffee_id);
          $stmt_sc->execute();
          $stmt_sc->close();
          
          $stmt = $mysqli->prepare("DELETE FROM coffee WHERE CoffeeID = ?");
          $stmt->bind_param('s', $coffee_id);
    
          if ($stmt->execute()) {
              $rows_deleted = $mysqli->affected_rows;
              if ($rows_deleted > 0) {
                  flash('success', 'Master Coffee record deleted successfully! (Rows deleted: ' . $rows_deleted . ')');
              } else {
                  flash('error', 'Error: Coffee ID ' . htmlspecialchars($coffee_id) . ' not found in master table.');
              }
          } else {
              flash('error', 'Database Error: Failed to delete master coffee record: ' . $mysqli->error);
          }
          $stmt->close();
      }
      header("Location: admin_coffees.php?store_id=$store_id"); exit;
  }

  $stmt_store = $mysqli->prepare("SELECT StoreName FROM store WHERE StoreID = ?");
  $stmt_store->bind_param('s', $store_id);
  $stmt_store->execute();
  $store = $stmt_store->get_result()->fetch_assoc();
  $stmt_store->close();

  if (!$store) {
      header('Location: admin_stores.php');
      exit;
  }


  $stmt_coffees = $mysqli->prepare("
    SELECT c.CoffeeID, c.CoffeeName, c.CoffeeDesc, sc.Price
    FROM storecoffee sc
    JOIN coffee c ON sc.CoffeeID = c.CoffeeID
    WHERE sc.StoreID = ?
    ORDER BY c.CoffeeName
  ");
  $stmt_coffees->bind_param('s', $store_id);
  $stmt_coffees->execute();
  $coffees = $stmt_coffees->get_result();
  $stmt_coffees->close();
  ?>

  <!DOCTYPE html>
  <html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KenanginKopi - Manage Coffee</title>
    <link rel="stylesheet" href="../css/style.css">
  </head>
  <body class="theme-coffee">

  <header>
    <?php include '../partials/navbar.php'; ?>
  </header>

  <main class="container admin-page">
    <h2 class="section-title text-center">
      Manage Coffee — <?php echo htmlspecialchars($store['StoreName']); ?>
    </h2>

    <?php include '../partials/flash.php'; ?>

    <div class="action-bar mb-3">
      <a href="admin_coffee_add.php?store_id=<?php echo $store_id; ?>" class="btn btn-primary">Add New Coffee</a>
      <a href="admin_stores.php" class="btn btn-light float-right">&larr; Back to Stores</a>
    </div>

    <div class="table-responsive">
      <table class="admin-table">
        <thead>
          <tr>
            <th>CoffeeID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Description</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($coffees->num_rows === 0): ?>
            <tr>
              <td colspan="5" class="text-center">No coffees are currently mapped to this store.</td>
            </tr>
          <?php else: ?>
            <?php while ($c = $coffees->fetch_assoc()): ?>
            <tr>
              <td><?php echo htmlspecialchars($c['CoffeeID']); ?></td>
              <td><?php echo htmlspecialchars($c['CoffeeName']); ?></td>
              <td>Rp <?php echo number_format($c['Price'], 0, ',', '.'); ?></td>
              <td><?php echo htmlspecialchars($c['CoffeeDesc']); ?></td>
              <td class="action-buttons">
                <form method="post" class="inline-form" onsubmit="return confirm('Are you sure you want to remove <?php echo htmlspecialchars($c['CoffeeName']); ?> from this store? Note: This action only unmaps the item, it does not delete the coffee itself.');">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="coffee_id" value="<?php echo htmlspecialchars($c['CoffeeID']); ?>">
                  <button type="submit" class="btn btn-danger btn-sm">Remove</button>
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