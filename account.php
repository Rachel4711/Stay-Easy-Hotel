<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = null;
$bookings = [];
$upcomingBookings = [];
$bookingHistory = [];
$error = '';

try {
    $statement = database()->prepare(
        'SELECT u.first_name, u.last_name, u.email, u.phone, u.created_at, r.name AS role_name
         FROM users u
         INNER JOIN roles r ON r.id = u.role_id
         WHERE u.id = :id AND u.status = :status
         LIMIT 1'
    );
    $statement->execute(['id' => $_SESSION['user_id'], 'status' => 'active']);
    $user = $statement->fetch();

    if (!$user) {
        $_SESSION = [];
        session_destroy();
        header('Location: login.php');
        exit;
    }

    $bookingStatement = database()->prepare(
        'SELECT b.booking_reference, b.check_in_date, b.check_out_date, b.adults, b.children,
                b.status, b.total_amount, b.currency, b.created_at,
                GROUP_CONCAT(DISTINCT rt.name ORDER BY rt.name SEPARATOR ", ") AS room_types,
                GROUP_CONCAT(r.room_number ORDER BY r.room_number SEPARATOR ", ") AS room_numbers
         FROM bookings b
         INNER JOIN booking_rooms br ON br.booking_id = b.id
         INNER JOIN rooms r ON r.id = br.room_id
         INNER JOIN room_types rt ON rt.id = r.room_type_id
         WHERE b.user_id = :user_id
         GROUP BY b.id, b.booking_reference, b.check_in_date, b.check_out_date,
                  b.adults, b.children, b.status, b.total_amount, b.currency, b.created_at
         ORDER BY b.check_in_date DESC, b.created_at DESC'
    );
    $bookingStatement->execute(['user_id' => $_SESSION['user_id']]);
    $bookings = $bookingStatement->fetchAll();

    $today = (new DateTimeImmutable('today', new DateTimeZone('Asia/Kuala_Lumpur')))->format('Y-m-d');
    foreach ($bookings as $booking) {
        if (in_array($booking['status'], ['pending', 'confirmed', 'checked_in'], true)
            && $booking['check_out_date'] >= $today) {
            $upcomingBookings[] = $booking;
        } else {
            $bookingHistory[] = $booking;
        }
    }
} catch (Throwable $exception) {
    $error = 'Your account cannot be loaded right now. Please check the database connection.';
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stay Easy Hotel: My Account</title>
        <link rel="stylesheet" href="StayEasyStyle.css">
    </head>
    <body>
        <?php include 'includes/header.php'; ?>

        <main id="main-content" class="auth-page container" tabindex="-1">
            <section class="auth-card account-card">
                <h1 class="sectionTitle">My Account</h1>

                <?php if (isset($_GET['registered'])): ?>
                    <div class="form-message success-message">Your account was created successfully.</div>
                <?php endif; ?>

                <?php if ($error !== ''): ?>
                    <div class="form-message error-message" role="alert"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                <?php elseif ($user): ?>
                    <dl class="account-details">
                        <div><dt>Name</dt><dd><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                        <div><dt>Email</dt><dd><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                        <div><dt>Phone</dt><dd><?= htmlspecialchars($user['phone'] ?: 'Not provided', ENT_QUOTES, 'UTF-8') ?></dd></div>
                        <div><dt>Account type</dt><dd><?= htmlspecialchars(ucfirst($user['role_name']), ENT_QUOTES, 'UTF-8') ?></dd></div>
                        <div><dt>Member since</dt><dd><?= htmlspecialchars(date('j F Y', strtotime($user['created_at'])), ENT_QUOTES, 'UTF-8') ?></dd></div>
                    </dl>
                <?php endif; ?>
            </section>

            <?php if ($error === ''): ?>
                <section class="customer-stats" aria-label="Booking summary">
                    <article><span>Upcoming stays</span><strong><?= count($upcomingBookings) ?></strong></article>
                    <article><span>Completed stays</span><strong><?= count(array_filter($bookings, fn ($booking) => $booking['status'] === 'checked_out')) ?></strong></article>
                    <article><span>Cancelled</span><strong><?= count(array_filter($bookings, fn ($booking) => $booking['status'] === 'cancelled')) ?></strong></article>
                </section>

                <section class="my-bookings" aria-labelledby="my-bookings-title">
                    <div class="section-heading-row">
                        <h2 class="sectionTitle" id="my-bookings-title">Upcoming Bookings</h2>
                        <a class="button" href="search.php">Book Another Stay</a>
                    </div>

                    <?php if (!$upcomingBookings): ?>
                        <div class="empty-bookings">
                            <h3>No upcoming stays</h3>
                            <p class="bodyText">Your next confirmed reservation will appear here.</p>
                            <a class="button" href="search.php">Find a Room</a>
                        </div>
                    <?php else: ?>
                        <div class="booking-list">
                            <?php foreach ($upcomingBookings as $booking): ?>
                                <?php
                                $statusLabel = str_replace('_', ' ', $booking['status']);
                                $nights = (new DateTimeImmutable($booking['check_in_date']))
                                    ->diff(new DateTimeImmutable($booking['check_out_date']))
                                    ->days;
                                ?>
                                <article class="booking-list-card">
                                    <div class="booking-list-header">
                                        <div>
                                            <span class="booking-list-label">Booking reference</span>
                                            <h3><?= htmlspecialchars($booking['booking_reference'], ENT_QUOTES, 'UTF-8') ?></h3>
                                        </div>
                                        <span class="status-badge status-<?= htmlspecialchars($booking['status'], ENT_QUOTES, 'UTF-8') ?>">
                                            <?= htmlspecialchars(ucwords($statusLabel), ENT_QUOTES, 'UTF-8') ?>
                                        </span>
                                    </div>
                                    <div class="booking-list-details">
                                        <div><span>Room</span><strong><?= htmlspecialchars($booking['room_types'], ENT_QUOTES, 'UTF-8') ?></strong></div>
                                        <div><span>Stay</span><strong><?= date('j M Y', strtotime($booking['check_in_date'])) ?> – <?= date('j M Y', strtotime($booking['check_out_date'])) ?></strong></div>
                                        <div><span>Length</span><strong><?= (int) $nights ?> night<?= (int) $nights === 1 ? '' : 's' ?></strong></div>
                                        <div><span>Guests</span><strong><?= (int) $booking['adults'] ?> adult<?= (int) $booking['adults'] === 1 ? '' : 's' ?>, <?= (int) $booking['children'] ?> child<?= (int) $booking['children'] === 1 ? '' : 'ren' ?></strong></div>
                                        <div><span>Total</span><strong><?= htmlspecialchars($booking['currency'], ENT_QUOTES, 'UTF-8') ?> <?= number_format((float) $booking['total_amount'], 2) ?></strong></div>
                                    </div>
                                    <a class="booking-detail-link" href="booking_confirmation.php?reference=<?= urlencode($booking['booking_reference']) ?>">View booking details →</a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>

                <?php if ($bookingHistory): ?>
                    <section class="my-bookings" aria-labelledby="booking-history-title">
                        <h2 class="sectionTitle" id="booking-history-title">Booking History</h2>
                        <div class="booking-list">
                            <?php foreach ($bookingHistory as $booking): ?>
                                <?php
                                $statusLabel = str_replace('_', ' ', $booking['status']);
                                $nights = (new DateTimeImmutable($booking['check_in_date']))
                                    ->diff(new DateTimeImmutable($booking['check_out_date']))
                                    ->days;
                                ?>
                                <article class="booking-list-card">
                                    <div class="booking-list-header">
                                        <div><span class="booking-list-label">Booking reference</span><h3><?= htmlspecialchars($booking['booking_reference'], ENT_QUOTES, 'UTF-8') ?></h3></div>
                                        <span class="status-badge status-<?= htmlspecialchars($booking['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucwords($statusLabel), ENT_QUOTES, 'UTF-8') ?></span>
                                    </div>
                                    <div class="booking-list-details">
                                        <div><span>Room</span><strong><?= htmlspecialchars($booking['room_types'], ENT_QUOTES, 'UTF-8') ?></strong></div>
                                        <div><span>Stay</span><strong><?= date('j M Y', strtotime($booking['check_in_date'])) ?> – <?= date('j M Y', strtotime($booking['check_out_date'])) ?></strong></div>
                                        <div><span>Length</span><strong><?= (int) $nights ?> night<?= (int) $nights === 1 ? '' : 's' ?></strong></div>
                                        <div><span>Guests</span><strong><?= (int) $booking['adults'] ?> adult<?= (int) $booking['adults'] === 1 ? '' : 's' ?>, <?= (int) $booking['children'] ?> child<?= (int) $booking['children'] === 1 ? '' : 'ren' ?></strong></div>
                                        <div><span>Total</span><strong><?= htmlspecialchars($booking['currency'], ENT_QUOTES, 'UTF-8') ?> <?= number_format((float) $booking['total_amount'], 2) ?></strong></div>
                                    </div>
                                    <a class="booking-detail-link" href="booking_confirmation.php?reference=<?= urlencode($booking['booking_reference']) ?>">View booking details →</a>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    </section>
                <?php endif; ?>
            <?php endif; ?>
        </main>

        <?php include 'includes/footer.php'; ?>
    </body>
</html>
