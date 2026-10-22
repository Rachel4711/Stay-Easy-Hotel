<?php
require_once __DIR__ . '/includes/session.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: contact.php');
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');
$isValid = $name !== '' && filter_var($email, FILTER_VALIDATE_EMAIL) && $message !== '';
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stay Easy Hotel: Contact Form</title>
        <link rel="stylesheet" href="StayEasyStyle.css">
    </head>
    <body>
        <?php include 'includes/header.php'; ?>

        <main id="main-content" class="container form-result" tabindex="-1">
            <?php if ($isValid): ?>
                <h1 class="sectionTitle">Thank You, <?= htmlspecialchars($name, ENT_QUOTES, 'UTF-8') ?></h1>
                <p class="bodyText">Your form is valid. Message delivery and database storage will be connected during the backend phase.</p>
            <?php else: ?>
                <h1 class="sectionTitle">Please Check Your Details</h1>
                <p class="bodyText">Enter your name, a valid email address, and a message before submitting the form.</p>
            <?php endif; ?>
            <a class="button" href="contact.php">Return to Contact Page</a>
        </main>

        <?php include 'includes/footer.php'; ?>
    </body>
</html>
