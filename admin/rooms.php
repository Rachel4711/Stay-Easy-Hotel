<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$adminTitle = 'Rooms';
$allowedStatuses = ['available', 'maintenance', 'inactive'];
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $roomId = filter_var($_POST['room_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $status = $_POST['status'] ?? '';
    if (!verifyAdminCsrf($_POST['csrf_token'] ?? '')) {
        $error = 'The form expired. Refresh and try again.';
    } elseif (!$roomId || !in_array($status, $allowedStatuses, true)) {
        $error = 'Choose a valid room and status.';
    } else {
        $update = database()->prepare('UPDATE rooms SET status = :status WHERE id = :id');
        $update->execute(['status' => $status, 'id' => $roomId]);
        $message = 'Room status updated.';
    }
}

$rooms = database()->query(
    "SELECT room.id, room.room_number, room.floor, room.status, rt.name AS room_type_name,
            EXISTS (
                SELECT 1 FROM active_booking_rooms abr
                WHERE abr.room_id = room.id AND CURDATE() >= abr.check_in_date AND CURDATE() < abr.check_out_date
            ) AS occupied_today
     FROM rooms room
     INNER JOIN room_types rt ON rt.id = room.room_type_id
     ORDER BY room.floor, room.room_number"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Manage Rooms | Stay Easy Hotel</title><link rel="stylesheet" href="../StayEasyStyle.css"></head>
<body class="admin-body"><?php include __DIR__ . '/header.php'; ?>
<main id="main-content" class="admin-container" tabindex="-1">
    <div class="admin-page-heading"><div><p>Physical room inventory</p><h1>Rooms</h1></div><strong><?= count($rooms) ?> rooms</strong></div>
    <?php if ($message): ?><div class="form-message success-message"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <?php if ($error): ?><div class="form-message error-message"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div><?php endif; ?>
    <section class="admin-panel"><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Room</th><th>Type</th><th>Floor</th><th>Today</th><th>Operational status</th></tr></thead><tbody>
    <?php foreach ($rooms as $room): ?><tr><td><strong><?= htmlspecialchars($room['room_number'], ENT_QUOTES, 'UTF-8') ?></strong></td><td><?= htmlspecialchars($room['room_type_name'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars((string) $room['floor'], ENT_QUOTES, 'UTF-8') ?></td><td><?= $room['occupied_today'] ? 'Occupied' : 'Not occupied' ?></td><td>
        <form class="inline-status-form" method="post" action="rooms.php"><input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['admin_csrf'], ENT_QUOTES, 'UTF-8') ?>"><input type="hidden" name="room_id" value="<?= (int) $room['id'] ?>"><select name="status" aria-label="Status for room <?= htmlspecialchars($room['room_number'], ENT_QUOTES, 'UTF-8') ?>"><?php foreach ($allowedStatuses as $option): ?><option value="<?= $option ?>" <?= $room['status'] === $option ? 'selected' : '' ?>><?= ucfirst($option) ?></option><?php endforeach; ?></select><button type="submit">Save</button></form>
    </td></tr><?php endforeach; ?>
    </tbody></table></div></section>
</main></body></html>
