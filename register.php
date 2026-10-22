<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/database.php';

if (isset($_SESSION['user_id'])) {
    header('Location: account.php');
    exit;
}

$errors = [];
$firstName = '';
$lastName = '';
$email = '';
$phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $email = strtolower(trim($_POST['email'] ?? ''));
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if ($firstName === '' || mb_strlen($firstName) > 80) {
        $errors[] = 'Enter a valid first name.';
    }
    if ($lastName === '' || mb_strlen($lastName) > 80) {
        $errors[] = 'Enter a valid last name.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 254) {
        $errors[] = 'Enter a valid email address.';
    }
    if (mb_strlen($phone) > 30) {
        $errors[] = 'The phone number is too long.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'The password must contain at least 8 characters.';
    }
    if ($password !== $confirmPassword) {
        $errors[] = 'The passwords do not match.';
    }

    if (!$errors) {
        try {
            $pdo = database();
            $roleStatement = $pdo->prepare('SELECT id FROM roles WHERE name = :name LIMIT 1');
            $roleStatement->execute(['name' => 'customer']);
            $roleId = $roleStatement->fetchColumn();

            if (!$roleId) {
                throw new RuntimeException('The customer role has not been seeded.');
            }

            $statement = $pdo->prepare(
                'INSERT INTO users (role_id, first_name, last_name, email, password_hash, phone)
                 VALUES (:role_id, :first_name, :last_name, :email, :password_hash, :phone)'
            );
            $statement->execute([
                'role_id' => $roleId,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'phone' => $phone !== '' ? $phone : null,
            ]);

            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $pdo->lastInsertId();
            $_SESSION['user_role'] = 'customer';
            header('Location: account.php?registered=1');
            exit;
        } catch (PDOException $exception) {
            if ($exception->getCode() === '23000') {
                $errors[] = 'An account with this email address already exists.';
            } else {
                $errors[] = 'Registration is temporarily unavailable. Please try again later.';
            }
        } catch (Throwable $exception) {
            $errors[] = 'Registration is temporarily unavailable. Please check the database setup.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stay Easy Hotel: Sign Up</title>
        <link rel="stylesheet" href="StayEasyStyle.css">
    </head>
    <body>
        <?php include 'includes/header.php'; ?>

        <main id="main-content" class="auth-page container" tabindex="-1">
            <section class="auth-card">
                <h1 class="sectionTitle">Create Your Account</h1>
                <p class="bodyText">Register to manage your future hotel bookings.</p>

                <?php if ($errors): ?>
                    <div id="registration-errors" class="form-message error-message" role="alert" tabindex="-1">
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form class="auth-form" method="post" action="register.php" <?= $errors ? 'aria-describedby="registration-errors"' : '' ?>>
                    <div class="form-grid">
                        <div class="form-field">
                            <label for="first_name">First name</label>
                            <input id="first_name" name="first_name" type="text" maxlength="80" autocomplete="given-name" value="<?= htmlspecialchars($firstName, ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                        <div class="form-field">
                            <label for="last_name">Last name</label>
                            <input id="last_name" name="last_name" type="text" maxlength="80" autocomplete="family-name" value="<?= htmlspecialchars($lastName, ENT_QUOTES, 'UTF-8') ?>" required>
                        </div>
                    </div>
                    <div class="form-field">
                        <label for="email">Email address</label>
                        <input id="email" name="email" type="email" maxlength="254" autocomplete="email" value="<?= htmlspecialchars($email, ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="form-field">
                        <label for="phone">Phone number <span>(optional)</span></label>
                        <input id="phone" name="phone" type="tel" maxlength="30" autocomplete="tel" value="<?= htmlspecialchars($phone, ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="form-field">
                        <label for="password">Password</label>
                        <input id="password" name="password" type="password" minlength="8" autocomplete="new-password" required>
                        <small>Use at least 8 characters.</small>
                    </div>
                    <div class="form-field">
                        <label for="confirm_password">Confirm password</label>
                        <input id="confirm_password" name="confirm_password" type="password" minlength="8" autocomplete="new-password" required>
                    </div>
                    <button type="submit">Create Account</button>
                </form>

                <p class="auth-switch">Already registered? <a href="login.php">Log in</a></p>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </body>
</html>
