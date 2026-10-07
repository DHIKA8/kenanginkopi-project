<?php
require_once '../partials/header.php';
require_once '../config/db.php';
require_once '../config/auth.php';
require_once '../config/helpers.php';
requireRole(['Admin']);

$store_id = $_GET['store_id'] ?? '';
if ($store_id === '') { header('Location: admin_stores.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = sanitize($_POST['name'] ?? '');
  $price = intval($_POST['price'] ?? 0);
  $description = sanitize($_POST['description'] ?? '');

  $errors = [];
  if ($name === '' || !onlyAlphaSpaces($name)) $errors[] = 'Name must be alphabetic only';
  if ($price < 10000 || $price > 100000) $errors[] = 'Price must be between 10000 and 100000';
  if (count(preg_split('/\s+/', trim($description))) < 3) $errors[] = 'Description must be minimum 3 words';

  if (!empty($errors)) {
    foreach ($errors as $e) flash('error', $e);
  } else {
    // Cek apakah kopi sudah ada
    $stmt = $mysqli->prepare("SELECT CoffeeID FROM coffee WHERE CoffeeName = ? AND CoffeeDesc = ?");
    $stmt->bind_param('ss', $name, $description);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
      $coffee_id = $row['CoffeeID'];
    } else {
      // Generate CoffeeID baru
      $res2 = $mysqli->query("SELECT MAX(CoffeeID) AS max_id FROM coffee");
      $row2 = $res2->fetch_assoc();
      $lastId = $row2['max_id'] ?? 'C0000';
      $num = intval(substr($lastId, 1)) + 1;
      $coffee_id = 'C' . str_pad($num, 4, '0', STR_PAD_LEFT);

      // Insert kopi baru
      $stmt = $mysqli->prepare("INSERT INTO coffee (CoffeeID, CoffeeName, CoffeeDesc) VALUES (?, ?, ?)");
      $stmt->bind_param('sss', $coffee_id, $name, $description);
      $stmt->execute();
    }

    // Map kopi ke store dengan harga
    $stmt = $mysqli->prepare("INSERT INTO storecoffee (StoreID, CoffeeID, Price) VALUES (?, ?, ?)");
    $stmt->bind_param('ssi', $store_id, $coffee_id, $price);
    if ($stmt->execute()) {
      header("Location: admin_coffees.php?store_id=$store_id"); exit;
    } else {
      flash('error', 'Failed to add coffee');
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KenanginKopi - Add Coffee</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="theme-coffee">

<header>
  <?php include '../partials/navbar.php'; ?>
</header>

<main class="container form-page">
  <section class="admin-form-section">
    <h2 class="section-title text-center">Add New Coffee</h2>
    <?php include '../partials/flash.php'; ?>

    <form method="post" class="standard-form">
      <div class="form-group">
        <label for="name">Coffee Name</label>
        <input type="text" id="name" name="name"
               value="<?php echo htmlspecialchars($name ?? ''); ?>"
               class="form-input" placeholder="e.g., Cold Brew" required>
        <small class="form-text text-muted">Must be alphabetic only (including spaces).</small>
      </div>

      <div class="form-group">
        <label for="price">Price (Rp)</label>
        <input type="number" id="price" name="price"
               value="<?php echo htmlspecialchars($price ?? ''); ?>"
               min="10000" max="100000" class="form-input" required>
        <small class="form-text text-muted">Price must be between Rp 10,000 and Rp 100,000.</small>
      </div>

      <div class="form-group">
        <label for="description">Description</label>
        <textarea id="description" name="description" class="form-input"
                  placeholder="Provide a brief description of the coffee." required><?php echo htmlspecialchars($description ?? ''); ?></textarea>
        <small class="form-text text-muted">Minimum 3 words.</small>
      </div>

      <button type="submit" class="btn btn-primary btn-block">Add Coffee</button>

      <div class="text-center mt-3">
        <a href="admin_coffees.php?store_id=<?php echo $store_id; ?>" class="link-secondary">&larr; Back to Manage Coffees</a>
      </div>
    </form>
  </section>
</main>

<footer>
  <?php include '../partials/footer.php'; ?>
</footer>
</body>
</html>