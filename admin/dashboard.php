<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$adminTitle = 'Overview';
$pdo = database();
$today = (new DateTimeImmutable('today', new DateTimeZone('Asia/Kuala_Lumpur')))->format('Y-m-d');

$statistics = [
    'rooms' => (int) $pdo->query('SELECT COUNT(*) FROM rooms')->fetchColumn(),
    'operational_rooms' => (int) $pdo->query("SELECT COUNT(*) FROM rooms WHERE status = 'available'")->fetchColumn(),
    'active_bookings' => (int) $pdo->query("SELECT COUNT(*) FROM bookings WHERE status IN ('pending', 'confirmed', 'checked_in')")->fetchColumn(),
    'customers' => (int) $pdo->query("SELECT COUNT(*) FROM users u INNER JOIN roles r ON r.id = u.role_id WHERE r.name = 'customer'")->fetchColumn(),
];

$dateStatement = $pdo->prepare(
    "SELECT
        SUM(check_in_date = :arrival_date AND status IN ('confirmed', 'checked_in')) AS arrivals,
        SUM(check_out_date = :departure_date AND status IN ('confirmed', 'checked_in')) AS departures
     FROM bookings"
);
$dateStatement->execute(['arrival_date' => $today, 'departure_date' => $today]);
$todayCounts = $dateStatement->fetch() ?: ['arrivals' => 0, 'departures' => 0];

$recentBookings = $pdo->query(
    'SELECT b.booking_reference, b.guest_first_name, b.guest_last_name, b.check_in_date,
            b.total_amount, b.currency, b.status, rt.name AS room_type_name
     FROM bookings b
     INNER JOIN booking_rooms br ON br.booking_id = b.id
     INNER JOIN rooms room ON room.id = br.room_id
     INNER JOIN room_types rt ON rt.id = room.room_type_id
     ORDER BY b.created_at DESC
     LIMIT 8'
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Admin Overview | Stay Easy Hotel</title><link rel="stylesheet" href="../StayEasyStyle.css"></head>
<body class="admin-body">
<?php include __DIR__ . '/header.php'; ?>
<main id="main-content" class="admin-container" tabindex="-1">
    <div class="admin-page-heading"><div><p>Administrator dashboard</p><h1>Overview</h1></div><span><?= date('j F Y', strtotime($today)) ?></span></div>
    <section class="admin-stats" aria-label="Hotel statistics">
        <article><span>Total rooms</span><strong><?= $statistics['rooms'] ?></strong></article>
        <article><span>Operational rooms</span><strong><?= $statistics['operational_rooms'] ?></strong></article>
        <article><span>Active bookings</span><strong><?= $statistics['active_bookings'] ?></strong></article>
        <article><span>Registered customers</span><strong><?= $statistics['customers'] ?></strong></article>
        <article><span>Arrivals today</span><strong><?= (int) ($todayCounts['arrivals'] ?? 0) ?></strong></article>
        <article><span>Departures today</span><strong><?= (int) ($todayCounts['departures'] ?? 0) ?></strong></article>
    </section>
    <section class="admin-panel">
        <div class="admin-panel-heading"><h2>Recent Bookings</h2><a href="bookings.php">View all</a></div>
        <?php if (!$recentBookings): ?><p>No bookings have been created yet.</p><?php else: ?>
        <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Reference</th><th>Guest</th><th>Room</th><th>Check-in</th><th>Total</th><th>Status</th></tr></thead><tbody>
        <?php foreach ($recentBookings as $booking): ?><tr>
            <td><a href="booking.php?reference=<?= urlencode($booking['booking_reference']) ?>"><?= htmlspecialchars($booking['booking_reference'], ENT_QUOTES, 'UTF-8') ?></a></td>
            <td><?= htmlspecialchars($booking['guest_first_name'] . ' ' . $booking['guest_last_name'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= htmlspecialchars($booking['room_type_name'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= date('j M Y', strtotime($booking['check_in_date'])) ?></td>
            <td><?= htmlspecialchars($booking['currency'], ENT_QUOTES, 'UTF-8') ?> <?= number_format((float) $booking['total_amount'], 2) ?></td>
            <td><span class="status-badge status-<?= htmlspecialchars($booking['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $booking['status'])), ENT_QUOTES, 'UTF-8') ?></span></td>
        </tr><?php endforeach; ?>
        </tbody></table></div><?php endif; ?>
    </section>
</main>
</body></html>
