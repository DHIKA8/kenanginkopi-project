<?php
require_once '../partials/header.php';
require_once '../config/db.php';
require_once '../config/helpers.php'; 


$userID = $_SESSION['user']['UserID']; 
$transactionID = $_GET['TransactionID'] ?? ''; 

// Ambil semua transaksi user
$stmt = $mysqli->prepare("SELECT TransactionID, StoreID, TransactionDate, TotalPrice 
                          FROM Transactions 
                          WHERE UserID = ? 
                          ORDER BY TransactionDate DESC");
$stmt->bind_param('s', $userID);
$stmt->execute();
$transactions = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Jika TransactionID dipilih, ambil detailnya
$details = [];
if (!empty($transactionID)) {
    $stmt = $mysqli->prepare("
        SELECT td.Qty, td.Subtotal, c.CoffeeName
        FROM TransactionDetails td
        JOIN Coffee c ON td.CoffeeID = c.CoffeeID 
        WHERE td.TransactionID = ?
    ");
    $stmt->bind_param('s', $transactionID);
    if (!$stmt->execute()) {
        flash('error', 'Failed to load details: ' . $mysqli->error);
    } else {
        $details = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }
    $stmt->close();
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KenanginKopi - Order History</title>
  <link rel="stylesheet" href="../css/style.css"> 
</head>
<body class="theme-coffee">

<header>
  <?php include '../partials/navbar.php'; ?>
</header>

<main class="container">
    <div class="card">
        <h2 class="section-title">Order History</h2>
        <?php include '../partials/flash.php'; ?>

        <?php if (!empty($transactionID)): ?>
            <h3>Transaction ID: <?php echo htmlspecialchars($transactionID); ?></h3>
            
            <?php if (!empty($details)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($details as $d): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($d['CoffeeName']); ?></td>
                            <td><?php echo htmlspecialchars($d['Qty']); ?></td>
                            <td>Rp <?php echo number_format($d['Subtotal']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p><a href="history.php">← Back to All Transactions</a></p>
            <?php else: ?>
                <p>No details found for this transaction ID, or failed to retrieve data.</p>
            <?php endif; ?>

        <?php else: ?>
            <h3>All Transactions</h3>
            <?php if (!empty($transactions)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($transactions as $t): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($t['TransactionID']); ?></td>
                            <td><?php echo htmlspecialchars($t['TransactionDate']); ?></td>
                            <td>Rp <?php echo number_format($t['TotalPrice']); ?></td>
                            <td><a class="btn btn1" href="history.php?TransactionID=<?php echo urlencode($t['TransactionID']); ?>">View Details</a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>You have no transaction history yet.</p>
            <?php endif; ?>
        <?php endif; ?>

    </div>
</main>

<footer>
  <?php include '../partials/footer.php'; ?>
</footer>
</body>
</html>