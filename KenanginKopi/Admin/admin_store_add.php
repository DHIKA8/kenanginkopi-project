<?php
require_once '../partials/header.php';
require_once '../config/db.php';
require_once '../config/auth.php';
require_once '../config/helpers.php';


requireRole(['Admin']);



$locations = ['Jakarta','Bandung','Surabaya','Yogyakarta','Semarang','Bali'];



$name = $_POST['name'] ?? '';
$location = $_POST['location'] ?? $locations[0];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($name);
    $location = sanitize($location);


    $errors = [];


    if ($name === '' || count(preg_split('/\s+/', trim($name))) < 2) {
        $errors[] = 'Store Name must be at least 2 words';
    }



    if (!in_array($location, $locations)) {
        $errors[] = 'Invalid location selected';
    }


    if (!empty($errors)) {
        foreach ($errors as $e) flash('error', $e);
    } else {
        $res = $mysqli->query("SELECT MAX(StoreID) AS max_id FROM store");
        $row = $res->fetch_assoc();
        $lastId = $row['max_id'] ?? 'S0000';
        $num = intval(substr($lastId, 1)) + 1;
        $newId = 'S' . str_pad($num, 4, '0', STR_PAD_LEFT);

        $stmt = $mysqli->prepare("INSERT INTO store (StoreID, StoreName, StoreLocation) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $newId, $name, $location);


        if ($stmt->execute()) {
            flash('success', 'Store "' . htmlspecialchars($name) . '" added successfully!');
            header('Location: admin_stores.php');
            exit;
        } else {
            flash('error', 'Failed to add store due to a database error: ' . $mysqli->error);
        }
        $stmt->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>KenanginKopi - Add Store</title>
  <link rel="stylesheet" href="../css/style.css">
</head>
<body class="theme-coffee">


<header>
  <?php include '../partials/navbar.php'; ?>
</header>


<main class="container form-page">
  <section class="admin-form-section">
    <h2 class="section-title text-center">Add New Store</h2>
    <?php include '../partials/flash.php'; ?>


    <form method="post" class="container1">
      <div class="form-group">
        <label for="name">Store Name</label>
        <input type="text" id="name" name="name"
               value="<?php echo htmlspecialchars($name); ?>"
               class="form-input" placeholder="e.g., KenanginKopi Sudirman" required>
        <small class="form-text text-muted">Must be at least 2 words.</small>
      </div>


      <div class="form-group">
        <label for="location">Location</label>
        <select id="location" name="location" class="form-input" required>
          <?php foreach ($locations as $loc): ?>
            <option value="<?php echo htmlspecialchars($loc); ?>"
              <?php echo ($location === $loc) ? 'selected' : ''; ?>>
              <?php echo htmlspecialchars($loc); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>


      <button type="submit" class="btn btn1">Add Store</button>


      <div class="text-center mt-3">
        <a href="admin_stores.php" class="btn btn1 btn-logout">&larr; Back to Manage Stores</a>
      </div>
    </form>
  </section>
</main>


<footer>
  <?php include '../partials/footer.php'; ?>
</footer>
</body>
</html>

