<?php
require_once '../partials/header.php';
require_once '../config/db.php';
require_once '../config/auth.php';
require_once '../config/helpers.php';


if (session_status() === PHP_SESSION_NONE) {
  session_start();
}


$store_id = $_GET['id'] ?? '';
if ($store_id === '') { header('Location: index.php'); exit; }


if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_cart') {
  if (!isUser()) {
    flash('error', 'Only users can add to cart');
    header("Location: store.php?id=$store_id"); exit;
  }


  $coffee_id = $_POST['coffee_id'] ?? '';
  if ($coffee_id !== '') {
    $_SESSION['cart'] = $_SESSION['cart'] ?? ['store_id' => $store_id, 'items' => []];


    if ($_SESSION['cart']['store_id'] !== $store_id) {
      $_SESSION['cart'] = ['store_id' => $store_id, 'items' => []];
    }


    $qty = $_SESSION['cart']['items'][$coffee_id]['qty'] ?? 0;
    $_SESSION['cart']['items'][$coffee_id] = ['qty' => $qty + 1];
    flash('success', 'Added to cart');
  }


  header("Location: store.php?id=$store_id"); exit;
}


$stmt = $mysqli->prepare("SELECT StoreID, StoreName, StoreLocation FROM store WHERE StoreID = ?");
$stmt->bind_param('s', $store_id);
$stmt->execute();
$store = $stmt->get_result()->fetch_assoc();
$stmt->close();


if (!$store) { header('Location: index.php'); exit; }


$stmt = $mysqli->prepare("
  SELECT c.CoffeeID, c.CoffeeName, c.CoffeeDesc, sc.Price
  FROM storecoffee sc
  JOIN coffee c ON sc.CoffeeID = c.CoffeeID
  WHERE sc.StoreID = ?
  ORDER BY c.CoffeeName
");
$stmt->bind_param('s', $store_id);
$stmt->execute();
$coffees = $stmt->get_result();
$stmt->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KenanginKopi - <?php echo htmlspecialchars($store['StoreName']); ?></title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="theme-coffee">


<header>
  <?php include '../partials/navbar.php'; ?>
</header>


<main class="container store-page">
  <section class="stores-list">
    <h2 class="section-title text-center">
      <?php echo htmlspecialchars($store['StoreName']); ?>
      <span class="location-tag">(<?php echo htmlspecialchars($store['StoreLocation']); ?>)</span>
    </h2>


    <?php include '../partials/flash.php'; ?>


    <?php if ($coffees->num_rows === 0): ?>
      <p class="text-center">No coffee products available at this store yet.</p>
    <?php else: ?>
      <div class="coffee-list grid-view">
        <?php while ($c = $coffees->fetch_assoc()): ?>
          <div class="store-card">
            <h3 class="description-text"><?php echo htmlspecialchars($c['CoffeeName']); ?></h3>
            <p class="description-text"><?php echo htmlspecialchars($c['CoffeeDesc']); ?></p>
            <p class="description-text">
              <strong>Price:</strong>
              <span class="description-text">Rp <?php echo number_format($c['Price'], 0, ',', '.'); ?></span>
            </p>


            <?php if (isUser()): ?>
              <form method="post" class="add-to-cart-form">
                <input type="hidden" name="action" value="add_cart">
                <input type="hidden" name="coffee_id" value="<?php echo htmlspecialchars($c['CoffeeID']); ?>">
                <button type="submit" class="btn btn-primary btn-sm">Add to Cart</button>
              </form>
            <?php else: ?>
              <p class="description-text">Login to order.</p>
            <?php endif; ?>
          </div>
        <?php endwhile; ?>
      </div>


      <?php if (isUser()): ?>
        <div class="text-center mt-4">
          <a href="cart.php" class="btn btn1">Go to Cart</a>
        </div>
      <?php endif; ?>
    <?php endif; ?>


    <div class="text-center mt-3">
      <a class="btn btn1 btn-login" href="index.php" class="link-secondary">&larr; Back to Store List</a>
    </div>
  </section>
</main>


<footer>
  <?php include '../partials/footer.php'; ?>
</footer>
</body>
</html>