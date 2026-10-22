<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: ' . (($_SESSION['user_role'] ?? '') === 'admin' ? 'admin/dashboard.php' : 'account.php'));
    exit;
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $error = 'Enter your email address and password.';
    } else {
        try {
            $pdo = database();
            $statement = $pdo->prepare(
                'SELECT u.id, u.password_hash, r.name AS role_name
                 FROM users u
                 INNER JOIN roles r ON r.id = u.role_id
                 WHERE email = :email AND status = :status
                 LIMIT 1'
            );
            $statement->execute(['email' => $email, 'status' => 'active']);
            $user = $statement->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = (int) $user['id'];
                $_SESSION['user_role'] = $user['role_name'];

                $updateLogin = $pdo->prepare('UPDATE users SET last_login_at = NOW() WHERE id = :id');
                $updateLogin->execute(['id' => $user['id']]);

                $destination = $user['role_name'] === 'admin'
                    ? 'admin/dashboard.php'
                    : ($_SESSION['booking_return'] ?? 'account.php');
                unset($_SESSION['booking_return']);
                header('Location: ' . $destination);
                exit;
            }

            $error = 'The email address or password is incorrect.';
        } catch (Throwable $exception) {
            $error = 'Login is temporarily unavailable. Please check the database setup.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stay Easy Hotel: Log In</title>
        <link rel="stylesheet" href="StayEasyStyle.css">
    </head>
    <body>
        <?php include 'includes/header.php'; ?>

        <main id="main-content" class="auth-page container" tabindex="-1">
            <section class="auth-card auth-card-small">
                <h1 class="sectionTitle">Log In</h1>
                <p class="bodyText">Welcome back to Stay Easy Hotel.</p>

                <?php if ($error !== ''): ?>
                    <div id="login-errors" class="form-message error-message" role="alert" tabindex="-1"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                <?php endif; ?>

                <form class="auth-form" method="post" action="login.php" <?= $error !== '' ? 'aria-describedby="login-errors"' : '' ?>>
                    <div class="form-field">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" maxlength="254" autocomplete="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" <?= $error !== '' ? 'aria-invalid="true"' : '' ?> required>
                    </div>
                    <div class="form-field">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" autocomplete="current-password" <?= $error !== '' ? 'aria-invalid="true"' : '' ?> required>
                    </div>
                    <button type="submit">Log In</button>
                </form>

                <p class="auth-switch">Need an account? <a href="register.php">Sign up</a></p>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </body>
</html>
