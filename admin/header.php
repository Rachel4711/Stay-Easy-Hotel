<?php
$adminPage = basename($_SERVER['PHP_SELF']);
$adminTitle = $adminTitle ?? 'Dashboard';
?>
<header class="admin-header">
    <div class="admin-header-inner">
        <a class="admin-brand" href="dashboard.php">
            <img src="../assets/Stay_Easy_Hotel_Logo.png" alt="">
            <span>Stay Easy Admin</span>
        </a>
        <div class="admin-user">
            <span><?= htmlspecialchars($adminUser['first_name'], ENT_QUOTES, 'UTF-8') ?></span>
            <a href="../logout.php">Log Out</a>
        </div>
    </div>
</header>
<nav class="admin-nav" aria-label="Administrator navigation">
    <a href="dashboard.php" <?= $adminPage === 'dashboard.php' ? 'aria-current="page"' : '' ?>>Overview</a>
    <a href="bookings.php" <?= in_array($adminPage, ['bookings.php', 'booking.php'], true) ? 'aria-current="page"' : '' ?>>Bookings</a>
    <a href="rooms.php" <?= $adminPage === 'rooms.php' ? 'aria-current="page"' : '' ?>>Rooms</a>
    <a href="customers.php" <?= $adminPage === 'customers.php' ? 'aria-current="page"' : '' ?>>Customers</a>
    <a href="../HomePage.php">View Website</a>
</nav>
