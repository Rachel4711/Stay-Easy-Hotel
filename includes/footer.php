<footer class="site-footer">
    <div class="footer-cta">
        <div>
            <p class="footer-eyebrow">Your next easy stay awaits</p>
            <h2>Ready to relax, reset, and recharge?</h2>
            <p>Search our available rooms and plan a comfortable stay in the heart of Kuala Lumpur.</p>
        </div>
        <a class="button" href="search.php">Book Your Stay</a>
    </div>

    <div class="footer-main">
        <div class="footer-brand">
            <a class="footer-logo" href="HomePage.php">
                <img src="assets/Stay_Easy_Hotel_Logo.png" alt="">
                <span>Stay Easy Hotel</span>
            </a>
            <p>Comfort, convenience, and warm Malaysian hospitality—made easy.</p>
            <p class="footer-location">KL Sentral, Kuala Lumpur, Malaysia</p>
        </div>

        <nav class="footer-links" aria-label="Explore the hotel">
            <h2>Explore</h2>
            <a href="about.php">About Us</a>
            <a href="room.php">Our Rooms</a>
            <a href="food.php">Eat &amp; Drink</a>
            <a href="gallery.php">Gallery</a>
        </nav>

        <nav class="footer-links" aria-label="Guest information">
            <h2>Guest Information</h2>
            <a href="search.php">Book a Room</a>
            <a href="neighbourhood.php">Neighbourhood Guide</a>
            <a href="contact.php">Contact &amp; Location</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="<?= ($_SESSION['user_role'] ?? '') === 'admin' ? 'admin/dashboard.php' : 'account.php' ?>">Your Dashboard</a>
            <?php else: ?>
                <a href="login.php">Log In</a>
            <?php endif; ?>
        </nav>
    </div>

    <div class="footer-bottom">
        <p>&copy; <?= date('Y') ?> Stay Easy Hotel. All rights reserved.</p>
        <a href="contact.php">Need help with your booking?</a>
    </div>
</footer>
