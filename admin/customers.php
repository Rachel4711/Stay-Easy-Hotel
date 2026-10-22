<?php

declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';

$adminTitle = 'Customers';
$customers = database()->query(
    "SELECT u.first_name, u.last_name, u.email, u.phone, u.status, u.created_at,
            COUNT(b.id) AS booking_count
     FROM users u
     INNER JOIN roles r ON r.id = u.role_id AND r.name = 'customer'
     LEFT JOIN bookings b ON b.user_id = u.id
     GROUP BY u.id, u.first_name, u.last_name, u.email, u.phone, u.status, u.created_at
     ORDER BY u.created_at DESC"
)->fetchAll();
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Customers | Stay Easy Hotel</title><link rel="stylesheet" href="../StayEasyStyle.css"></head>
<body class="admin-body"><?php include __DIR__ . '/header.php'; ?>
<main class="admin-container">
    <div class="admin-page-heading"><div><p>Registered accounts</p><h1>Customers</h1></div><strong><?= count($customers) ?> customer<?= count($customers) === 1 ? '' : 's' ?></strong></div>
    <section class="admin-panel"><?php if (!$customers): ?><p>No customers have registered yet.</p><?php else: ?><div class="admin-table-wrap"><table class="admin-table"><thead><tr><th>Customer</th><th>Email</th><th>Phone</th><th>Joined</th><th>Bookings</th><th>Status</th></tr></thead><tbody>
    <?php foreach ($customers as $customer): ?><tr><td><strong><?= htmlspecialchars($customer['first_name'] . ' ' . $customer['last_name'], ENT_QUOTES, 'UTF-8') ?></strong></td><td><?= htmlspecialchars($customer['email'], ENT_QUOTES, 'UTF-8') ?></td><td><?= htmlspecialchars($customer['phone'] ?: 'Not provided', ENT_QUOTES, 'UTF-8') ?></td><td><?= date('j M Y', strtotime($customer['created_at'])) ?></td><td><?= (int) $customer['booking_count'] ?></td><td><?= htmlspecialchars(ucfirst($customer['status']), ENT_QUOTES, 'UTF-8') ?></td></tr><?php endforeach; ?>
    </tbody></table></div><?php endif; ?></section>
</main></body></html>
