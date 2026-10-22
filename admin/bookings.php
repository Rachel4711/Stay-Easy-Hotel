<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$adminTitle = 'Bookings';
$allowedStatuses = ['pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show'];
$status = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');
$conditions = [];
$parameters = [];

if (in_array($status, $allowedStatuses, true)) {
    $conditions[] = 'b.status = :status';
    $parameters['status'] = $status;
}
if ($search !== '') {
    $conditions[] = '(b.booking_reference LIKE :search_reference OR b.guest_email LIKE :search_email OR CONCAT(b.guest_first_name, " ", b.guest_last_name) LIKE :search_name)';
    $parameters['search_reference'] = '%' . $search . '%';
    $parameters['search_email'] = '%' . $search . '%';
    $parameters['search_name'] = '%' . $search . '%';
}

$sql = 'SELECT b.booking_reference, b.guest_first_name, b.guest_last_name, b.guest_email,
               b.check_in_date, b.check_out_date, b.total_amount, b.currency, b.status,
               GROUP_CONCAT(DISTINCT rt.name ORDER BY rt.name SEPARATOR ", ") AS room_types
        FROM bookings b
        INNER JOIN booking_rooms br ON br.booking_id = b.id
        INNER JOIN rooms room ON room.id = br.room_id
        INNER JOIN room_types rt ON rt.id = room.room_type_id';
if ($conditions) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}
$sql .= ' GROUP BY b.id, b.booking_reference, b.guest_first_name, b.guest_last_name, b.guest_email,
                  b.check_in_date, b.check_out_date, b.total_amount, b.currency, b.status
          ORDER BY b.created_at DESC';

$statement = database()->prepare($sql);
$statement->execute($parameters);
$bookings = $statement->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Manage Bookings | Stay Easy Hotel</title><link rel="stylesheet" href="../StayEasyStyle.css"></head>
<body class="admin-body">
<?php include __DIR__ . '/header.php'; ?>
<main id="main-content" class="admin-container" tabindex="-1">
    <div class="admin-page-heading"><div><p>Reservations</p><h1>Bookings</h1></div><strong><?= count($bookings) ?> result<?= count($bookings) === 1 ? '' : 's' ?></strong></div>
    <form class="admin-filters" method="get" action="bookings.php">
        <div class="form-field"><label for="search">Reference, guest, or email</label><input id="search" name="search" type="search" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>"></div>
        <div class="form-field"><label for="status">Status</label><select id="status" name="status"><option value="">All statuses</option><?php foreach ($allowedStatuses as $option): ?><option value="<?= $option ?>" <?= $status === $option ? 'selected' : '' ?>><?= ucwords(str_replace('_', ' ', $option)) ?></option><?php endforeach; ?></select></div>
        <button type="submit">Filter</button>
        <a href="bookings.php">Clear</a>
    </form>
    <section class="admin-panel">
        <?php if (!$bookings): ?><p>No bookings match the selected filters.</p><?php else: ?>
        <div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Reference</th><th>Guest</th><th>Room</th><th>Stay</th><th>Total</th><th>Status</th><th></th></tr></thead><tbody>
        <?php foreach ($bookings as $booking): ?><tr>
            <td><?= htmlspecialchars($booking['booking_reference'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><strong><?= htmlspecialchars($booking['guest_first_name'] . ' ' . $booking['guest_last_name'], ENT_QUOTES, 'UTF-8') ?></strong><small><?= htmlspecialchars($booking['guest_email'], ENT_QUOTES, 'UTF-8') ?></small></td>
            <td><?= htmlspecialchars($booking['room_types'], ENT_QUOTES, 'UTF-8') ?></td>
            <td><?= date('j M Y', strtotime($booking['check_in_date'])) ?> – <?= date('j M Y', strtotime($booking['check_out_date'])) ?></td>
            <td><?= htmlspecialchars($booking['currency'], ENT_QUOTES, 'UTF-8') ?> <?= number_format((float) $booking['total_amount'], 2) ?></td>
            <td><span class="status-badge status-<?= htmlspecialchars($booking['status'], ENT_QUOTES, 'UTF-8') ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $booking['status'])), ENT_QUOTES, 'UTF-8') ?></span></td>
            <td><a href="booking.php?reference=<?= urlencode($booking['booking_reference']) ?>">View</a></td>
        </tr><?php endforeach; ?>
        </tbody></table></div><?php endif; ?>
    </section>
</main></body></html>
