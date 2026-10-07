<?php 
$user = currentUser();  
$date = date("l, d F Y");  
$is_admin = $user && isAdmin();
?>

<header class="navbar-container">
    
    <div class="navbar-left">
        <a href="index.php" class="brand-logo">KenanginKopi</a>
    </div>

    <div class="navbar-center">
        <span class="current-date"><?php echo $date; ?></span>
    </div>

    <nav class="navbar-right">
        
        <?php if (!$user): ?>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="register.php">Register</a></li>
            </ul>
            <a href="login.php" class="btn btn-login">Login</a>
            
        <?php elseif ($is_admin): ?>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                
                <li class="dropdown-menu">
                    <button class="btn btn-manage dropdown-toggle">Manage</button>
                    <ul class="dropdown-content">
                        <li><a href="admin_users.php">Manage User</a></li>
                        <li><a href="admin_stores.php">Manage Store</a></li>
                    </ul>
                </li>
            </ul>
            <a href="profile.php" class="user-link">
              <?php echo htmlspecialchars($user['UserName']); ?>
            </a> 
            <a href="logout.php" class="btn btn-logout">Logout</a>
            
        <?php else: ?>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="cart.php">Cart</a></li>
                <li><a href="history.php">History</a></li>
            </ul>
            <a href="profile.php" class="user-link">
              <?php echo htmlspecialchars($user['UserName']); ?>
            </a>
            <a href="logout.php" class="btn btn-logout">Logout</a>
            
        <?php endif; ?>

    </nav>
</header>