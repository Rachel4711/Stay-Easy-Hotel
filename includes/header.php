<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$navigationItems = [
	'search.php' => 'Book a Room',
	'about.php' => 'About',
	'contact.php' => 'Contact Us',
	'room.php' => 'Room Tour',
	'food.php' => 'Eat & Drink',
	'neighbourhood.php' => 'Neighbourhood',
	'gallery.php' => 'Gallery',
];
?>
<header>
	<div id="logo">
		<a href="HomePage.php"><img src="assets/Stay_Easy_Hotel_Logo.png" alt="Stay Easy Hotel home"></a>
	</div>
	<h1 class="pageTitle"><a href="HomePage.php">Stay Easy Hotel</a></h1>
	<div class="account-links" aria-label="Account features">
		<?php if (isset($_SESSION['user_id'])): ?>
			<?php if (($_SESSION['user_role'] ?? '') === 'admin'): ?>
				<a href="admin/dashboard.php">Admin Dashboard</a>
			<?php else: ?>
				<a href="account.php">My Account</a>
			<?php endif; ?>
			<a href="logout.php">Log Out</a>
		<?php else: ?>
			<a href="register.php">Sign Up</a>
			<a href="login.php">Log In</a>
		<?php endif; ?>
	</div>
</header>

<nav id="navBar" aria-label="Main navigation">
	<ul>
		<?php foreach ($navigationItems as $file => $label): ?>
			<li>
				<a href="<?= htmlspecialchars($file, ENT_QUOTES, 'UTF-8') ?>"
					<?= $currentPage === $file ? 'class="active" aria-current="page"' : '' ?>>
					<?= htmlspecialchars($label, ENT_QUOTES, 'UTF-8') ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>
