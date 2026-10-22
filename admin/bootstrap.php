<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/includes/session.php';
require_once dirname(__DIR__) . '/includes/database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$adminStatement = database()->prepare(
    "SELECT u.id, u.first_name, u.last_name, u.email, r.name AS role_name
     FROM users u
     INNER JOIN roles r ON r.id = u.role_id
     WHERE u.id = :id AND u.status = 'active'
     LIMIT 1"
);
$adminStatement->execute(['id' => $_SESSION['user_id']]);
$adminUser = $adminStatement->fetch();

if (!$adminUser || $adminUser['role_name'] !== 'admin') {
    header('Location: ../account.php');
    exit;
}

$_SESSION['user_role'] = 'admin';

if (empty($_SESSION['admin_csrf'])) {
    $_SESSION['admin_csrf'] = bin2hex(random_bytes(32));
}

function verifyAdminCsrf(string $token): bool
{
    return isset($_SESSION['admin_csrf']) && hash_equals($_SESSION['admin_csrf'], $token);
}
