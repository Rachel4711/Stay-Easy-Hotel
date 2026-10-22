<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$reference = trim($_GET['reference'] ?? '');
$booking = null;
$error = '';

if ($reference === '') {
    $error = 'No booking reference was provided.';
} else {
    try {
        $statement = database()->prepare(
            'SELECT b.booking_reference, b.check_in_date, b.check_out_date, b.adults, b.children,
                    b.status, b.total_amount, b.currency, b.special_requests, b.created_at,
                    rt.name AS room_type_name, rt.image_path, r.room_number
             FROM bookings b
             INNER JOIN booking_rooms br ON br.booking_id = b.id
             INNER JOIN rooms r ON r.id = br.room_id
             INNER JOIN room_types rt ON rt.id = r.room_type_id
             WHERE b.booking_reference = :reference AND b.user_id = :user_id
             LIMIT 1'
        );
        $statement->execute(['reference' => $reference, 'user_id' => $_SESSION['user_id']]);
        $booking = $statement->fetch();
        if (!$booking) {
            $error = 'The requested booking could not be found.';
        }
    } catch (Throwable $exception) {
        $error = 'The booking confirmation cannot be loaded right now.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stay Easy Hotel: Booking Confirmation</title>
        <link rel="stylesheet" href="StayEasyStyle.css">
    </head>
    <body>
        <?php include 'includes/header.php'; ?>
        <main class="container booking-page">
            <?php if ($error !== ''): ?>
                <div class="form-message error-message" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                <a class="button" href="search.php">Search Rooms</a>
            <?php else: ?>
                <section class="confirmation-card">
                    <div class="confirmation-mark" aria-hidden="true">✓</div>
                    <p class="confirmation-eyebrow">Booking confirmed</p>
                    <h1 class="sectionTitle">Your Stay Is Reserved</h1>
                    <p class="bodyText">Keep this booking reference for check-in:</p>
                    <strong class="booking-reference"><?= htmlspecialchars($booking['booking_reference'], ENT_QUOTES, 'UTF-8') ?></strong>
                    <dl class="account-details confirmation-details">
                        <div><dt>Room</dt><dd><?= htmlspecialchars($booking['room_type_name'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                        <div><dt>Room number</dt><dd><?= htmlspecialchars($booking['room_number'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                        <div><dt>Check-in</dt><dd><?= date('j F Y', strtotime($booking['check_in_date'])) ?></dd></div>
                        <div><dt>Check-out</dt><dd><?= date('j F Y', strtotime($booking['check_out_date'])) ?></dd></div>
                        <div><dt>Guests</dt><dd><?= (int) $booking['adults'] ?> adult<?= (int) $booking['adults'] === 1 ? '' : 's' ?>, <?= (int) $booking['children'] ?> child<?= (int) $booking['children'] === 1 ? '' : 'ren' ?></dd></div>
                        <div><dt>Total due on arrival</dt><dd><?= htmlspecialchars($booking['currency'], ENT_QUOTES, 'UTF-8') ?> <?= number_format((float) $booking['total_amount'], 2) ?></dd></div>
                    </dl>
                    <div class="confirmation-actions">
                        <a class="button" href="account.php">Go to My Account</a>
                        <a class="button secondary-button" href="HomePage.php">Return Home</a>
                    </div>
                </section>
            <?php endif; ?>
        </main>
        <?php include 'includes/footer.php'; ?>
    </body>
</html>
