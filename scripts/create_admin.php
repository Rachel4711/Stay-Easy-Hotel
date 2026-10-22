<?php

declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once dirname(__DIR__) . '/includes/database.php';

echo "Create Stay Easy Hotel administrator\n";
$firstName = trim((string) readline('First name: '));
$lastName = trim((string) readline('Last name: '));
$email = strtolower(trim((string) readline('Email: ')));
$password = (string) readline('Password (minimum 8 characters): ');

if ($firstName === '' || $lastName === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
    fwrite(STDERR, "Invalid details. No administrator was created.\n");
    exit(1);
}

try {
    $pdo = database();
    $roleStatement = $pdo->prepare("SELECT id FROM roles WHERE name = 'admin' LIMIT 1");
    $roleStatement->execute();
    $roleId = $roleStatement->fetchColumn();

    if (!$roleId) {
        throw new RuntimeException('The admin role is missing. Import database/seed.sql first.');
    }

    $insert = $pdo->prepare(
        'INSERT INTO users (role_id, first_name, last_name, email, password_hash)
         VALUES (:role_id, :first_name, :last_name, :email, :password_hash)'
    );
    $insert->execute([
        'role_id' => $roleId,
        'first_name' => $firstName,
        'last_name' => $lastName,
        'email' => $email,
        'password_hash' => password_hash($password, PASSWORD_DEFAULT),
    ]);

    echo "Administrator created successfully.\n";
} catch (PDOException $exception) {
    if ($exception->getCode() === '23000') {
        fwrite(STDERR, "That email address is already registered.\n");
    } else {
        fwrite(STDERR, "The administrator could not be created.\n");
    }
    exit(1);
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . "\n");
    exit(1);
}
