<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$adminTitle = 'Booking Details';
$reference = trim($_GET['reference'] ?? $_POST['reference'] ?? '');
$allowedStatuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newStatus = $_POST['status'] ?? '';
    if (!verifyAdminCsrf($_POST['csrf_token'] ?? '')) {
        $error = 'The form expired. Refresh and try again.';
    } elseif (!in_array($newStatus, $allowedStatuses, true)) {
        $error = 'Choose a valid booking status.';
    } else {
        $update = database()->prepare(
            'UPDATE bookings
             SET status = :status,
                 cancelled_at = CASE WHEN :cancel_status = "cancelled" THEN NOW() ELSE NULL END
             WHERE booking_reference = :reference'
        );
        $update->execute(['status' => $newStatus, 'cancel_status' => $newStatus, 'reference' => $reference]);
        $message = 'Booking status updated.';
    }
}

$statement = database()->prepare(
    'SELECT b.*, u.id AS customer_id, rt.name AS room_type_name, room.room_number, room.floor
     FROM bookings b
     LEFT JOIN users u ON u.id = b.user_id
     INNER JOIN booking_rooms br ON br.booking_id = b.id
     INNER JOIN rooms room ON room.id = br.room_id
     INNER JOIN room_types rt ON rt.id = room.room_type_id
     WHERE b.booking_reference = :reference
     LIMIT 1'
);
$statement->execute(['reference' => $reference]);
$booking = $statement->fetch();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Booking Details | Stay Easy Hotel</title><link rel="stylesheet" href="../StayEasyStyle.css"></head>
<body class="admin-body"><?php include __DIR__ . '/header.php'; ?>
<main id="main-content" class="admin-container" tabindex="-1">
    <a class="admin-back-link" href="bookings.php">← Back to bookings</a>
    <?php if (!$booking): ?><div class="form-message error-message">Booking not found.</div>
    <?php else: ?>
    <div class="admin-page-heading"><div><p>Booking reference</p><h1><?= htmlspecialchars($booking['booking_reference'], ENT_QUOTES, 'UTF-8') ?></h1></div><span class="status-badge status-<?= htmlspecialchars($booking['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $booking['status'])), ENT_QUOTES, 'UTF-8') ?></span></div>
    <?php if ($message): ?><div class="form-message success-message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if ($error): ?><div class="form-message error-message"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <div class="admin-detail-grid">
        <section class="admin-panel"><h2>Stay Details</h2><dl class="admin-detail-list">
            <div><dt>Room</dt><dd><?= htmlspecialchars($booking['room_type_name'] . ' — Room ' . $booking['room_number'], ENT_QUOTES, 'UTF-8') ?></dd></div>
            <div><dt>Check-in</dt><dd><?= date('j F Y', strtotime($booking['check_in_date'])) ?></dd></div>
            <div><dt>Check-out</dt><dd><?= date('j F Y', strtotime($booking['check_out_date'])) ?></dd></div>
            <div><dt>Guests</dt><dd><?= (int) $booking['adults'] ?> adult<?= (int) $booking['adults'] === 1 ? '' : 's' ?>, <?= (int) $booking['children'] ?> child<?= (int) $booking['children'] === 1 ? '' : 'ren' ?></dd></div>
            <div><dt>Total</dt><dd><?= htmlspecialchars($booking['currency'], ENT_QUOTES, 'UTF-8') ?> <?= number_format((float) $booking['total_amount'], 2) ?></dd></div>
        </dl></section>
        <section class="admin-panel"><h2>Guest Details</h2><dl class="admin-detail-list">
            <div><dt>Name</dt><dd><?= htmlspecialchars($booking['guest_first_name'] . ' ' . $booking['guest_last_name'], ENT_QUOTES, 'UTF-8') ?></dd></div>
            <div><dt>Email</dt><dd><?= htmlspecialchars($booking['guest_email'], ENT_QUOTES, 'UTF-8') ?></dd></div>
            <div><dt>Phone</dt><dd><?= htmlspecialchars($booking['guest_phone'] ?: 'Not provided', ENT_QUOTES, 'UTF-8') ?></dd></div>
            <div><dt>Special requests</dt><dd><?= nl2br(htmlspecialchars($booking['special_requests'] ?: 'None', ENT_QUOTES, 'UTF-8')) ?></dd></div>
        </dl></section>
    </div>
    <section class="admin-panel admin-status-form"><h2>Update Status</h2><form method="post" action="booking.php">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['admin_csrf'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="reference" value="<?= htmlspecialchars($reference, ENT_QUOTES, 'UTF-8') ?>">
        <div class="form-field"><label for="status">Booking status</label><select id="status" name="status"><?php foreach ($allowedStatuses as $option): ?><option value="<?= $option ?>" <?= $booking['status'] === $option ? 'selected' : '' ?>><?= ucwords(str_replace('_', ' ', $option)) ?></option><?php endforeach; ?></select></div>
        <button type="submit">Save Status</button>
    </form></section>
    <?php endif; ?>
</main></body></html>
