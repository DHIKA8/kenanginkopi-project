<?php
require_once '../partials/header.php';
require_once '../config/db.php';
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>KenanginKopi - Home</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header>
    <?php
    include '../partials/navbar.php';
    ?>
</header>
<main class="container">
    <h1 class="main-title">KenanginKopi</h1>
    <p class="tagline">Favorable taste for your mood</p>
   
    <h2 class="section-title">Available Stores</h2>
   
    <div class="stores-list">
       
        <?php
          $res = $mysqli->query("SELECT StoreID, StoreName, StoreLocation FROM Store ORDER BY StoreName");
          while ($s = $res->fetch_assoc()):
        ?>
            <div class="store-card">
                <div class="store-info">
                    <h3 class="store-name"><?php echo htmlspecialchars($s['StoreName']); ?></h3>
                    <p class="store-location"><strong>Location:</strong> <?php echo htmlspecialchars($s['StoreLocation']); ?></p>
                </div>
                <a href="store.php?id=<?php echo urlencode($s['StoreID']); ?>" class="view-details-button">View Details</a>
            </div>
        <?php
          endwhile;
         
          if ($res->num_rows === 0) {
              echo '<p class="no-stores-message">No stores are currently available.</p>';
          }
        ?>
    </div>
</main>
</body>
<footer>
  <?php
  include '../partials/footer.php';
  ?>
</footer>
</html>