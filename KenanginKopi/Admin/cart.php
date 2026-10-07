<?php
require_once '../partials/header.php';
require_once '../config/db.php';
require_once '../config/auth.php';
require_once '../config/helpers.php';

requireLogin();

$cart = $_SESSION['cart'] ?? null;
$items = [];
$total_items = 0;
$total_price = 0;

if ($cart && !empty($cart['items'])) {
  $ids = "'" . implode("','", array_keys($cart['items'])) . "'";
  $store_id = $cart['store_id'];

  $stmt = $mysqli->prepare("
    SELECT c.CoffeeID, c.CoffeeName, c.CoffeeDesc, sc.Price
    FROM storecoffee sc
    JOIN coffee c ON sc.CoffeeID = c.CoffeeID
    WHERE sc.StoreID = ? AND sc.CoffeeID IN ($ids)
  ");
  $stmt->bind_param('s', $store_id);
  $stmt->execute();
  $res = $stmt->get_result();
  $stmt->close();

  while ($row = $res->fetch_assoc()) {
    $cid = $row['CoffeeID'];
    $qty = $cart['items'][$cid]['qty'];
    $price = $row['Price']; 
    $subtotal = $price * $qty;
    $items[] = [
      'id'          => $cid,
      'name'        => $row['CoffeeName'],
      'description' => $row['CoffeeDesc'],
      'price'       => $price,
      'qty'         => $qty,
      'subtotal'    => $subtotal
    ];
    $total_items += $qty;
    $total_price += $subtotal;
  }
}

// Update qty / delete / pay
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if ($action === 'update') {
    $cid = $_POST['coffee_id'] ?? '';
    $qty = intval($_POST['qty'] ?? 0);
    if ($qty <= 0) {
      unset($_SESSION['cart']['items'][$cid]);
      flash('error', 'Quantity must be more than 0. Item removed.');
    } else {
      $_SESSION['cart']['items'][$cid]['qty'] = $qty;
      flash('success', 'Quantity updated');
    }
    header('Location: cart.php'); exit;
  } elseif ($action === 'delete') {
    $cid = $_POST['coffee_id'] ?? '';
    unset($_SESSION['cart']['items'][$cid]);
    flash('success', 'Item deleted');
    header('Location: cart.php'); exit;
  } elseif ($action === 'pay') {
    if (!$cart || empty($cart['items'])) {
      flash('error', 'Cart is empty'); header('Location: cart.php'); exit;
    }
    // Create transaction
    $user_id = $_SESSION['user']['UserID']; // 
    $store_id = $cart['store_id'];
    $mysqli->begin_transaction();
    try {
      $stmt = $mysqli->prepare("INSERT INTO Transactions (TransactionID, UserID, StoreID, TransactionDate, TotalPrice) VALUES (?, ?, ?, CURDATE(), ?)");
      $txId = 'T' . str_pad(rand(1,9999), 4, '0', STR_PAD_LEFT);
      $stmt->bind_param('sssd', $txId, $user_id, $store_id, $total_price);
      $stmt->execute();

      foreach ($items as $it) {
        $stmt = $mysqli->prepare("INSERT INTO TransactionDetails (TransactionID, CoffeeID, Qty, Subtotal) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('ssii', $txId, $it['id'], $it['qty'], $it['subtotal']);
        $stmt->execute();
      }
      $mysqli->commit();
      $_SESSION['cart'] = null; unset($_SESSION['cart']);
      flash('success', "Payment successful. Transaction ID: $txId");
      header('Location: cart.php?paid=1&tx='.$txId); exit;
    } catch (Exception $e) {
      $mysqli->rollback();
      flash('error', 'Payment failed');
      header('Location: cart.php'); exit;
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KenanginKopi - Shopping Cart</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body class="theme-coffee"> 

<header>
    <?php include '../partials/navbar.php'; ?>
</header>

<main class="container form-page"> 
    <section class="shopping-cart-section">
      <h2 class="section-title text-center">Shopping Cart</h2>
      
      <?php include '../partials/flash.php'; ?>
      
      <?php if (isset($_GET['paid'])): ?>
      <div class="alert alert-success">
        Payment Success: Transaction ID: <?php echo htmlspecialchars($_GET['tx']); ?>
      </div>
      <?php endif; ?>

      <?php if (empty($items)): ?>
        <div class="container1">
            <p><strong>0 item(s) - Total: Rp 0</strong></p>
            <p>Cart is Empty. Order Coffee Now</p>
        </div>
      <?php else: ?>
        <div class="cart-summary">
            <p><strong><?php echo $total_items; ?> item(s) - Total: Rp <?php echo number_format($total_price, 0, ',', '.'); ?></strong></p>
        </div>

        <div class="table-responsive">
         <table class="cart-table">
            <thead>
             <tr>
                <th>Coffee</th>
                <th>Description</th>
                <th>Price</th>
                <th>Qty</th>
                <th>Subtotal</th>
                <th>Action</th>
              </tr>
           </thead>
           <tbody>
              <?php foreach ($items as $it): ?>
               <tr>
                  <td><?php echo htmlspecialchars($it['name']); ?></td>
                  <td><?php echo htmlspecialchars($it['description']); ?></td>
                  <td>Rp <?php echo number_format($it['price'], 0, ',', '.'); ?></td>
                  <td>
                    <form method="post" class="qty-form">
                      <input type="hidden" name="action" value="update">
                      <input type="hidden" name="coffee_id" value="<?php echo htmlspecialchars($it['id']); ?>">
                      <input type="number" min="1" name="qty" value="<?php echo htmlspecialchars($it['qty']); ?>" class="input-qty" required>      
                      <button type="submit" class="btn btn-update">Update</button>
                    </form>
                  </td>
                  <td>Rp <?php echo number_format($it['subtotal'], 0, ',', '.'); ?></td>
                  <td>
                    <form method="post">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="coffee_id" value="<?php echo htmlspecialchars($it['id']); ?>">                      
                      <button type="submit" class="btn btn-danger">Delete</button> 
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
       
        <form method="post" class="pay-form">
          <input type="hidden" name="action" value="pay">
          <button type="submit" class="btn btn-success btn-block">Pay</button>
        </form>
      <?php endif; ?>
    </section>
</main>

<footer>
    <?php include '../partials/footer.php'; ?>
</footer>
</body>
</html>