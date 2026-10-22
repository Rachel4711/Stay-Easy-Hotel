<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/database.php';

$roomTypeId = filter_var($_REQUEST['room_type_id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$checkInValue = trim($_REQUEST['check_in'] ?? '');
$checkOutValue = trim($_REQUEST['check_out'] ?? '');
$adults = filter_var($_REQUEST['adults'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 10]]);
$children = filter_var($_REQUEST['children'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 10]]);

if (!isset($_SESSION['user_id'])) {
    if ($roomTypeId && $checkInValue && $checkOutValue && $adults !== false && $children !== false) {
        $_SESSION['booking_return'] = 'booking.php?' . http_build_query([
            'room_type_id' => $roomTypeId,
            'check_in' => $checkInValue,
            'check_out' => $checkOutValue,
            'adults' => $adults,
            'children' => $children,
        ]);
    }
    header('Location: login.php');
    exit;
}

$today = new DateTimeImmutable('today', new DateTimeZone('Asia/Kuala_Lumpur'));
$checkIn = DateTimeImmutable::createFromFormat('!Y-m-d', $checkInValue);
$checkOut = DateTimeImmutable::createFromFormat('!Y-m-d', $checkOutValue);
$errors = [];
$roomType = null;
$user = null;

if (!$roomTypeId) {
    $errors[] = 'Choose a room type from the availability search.';
}
if (!$checkIn || $checkIn->format('Y-m-d') !== $checkInValue || $checkIn < $today) {
    $errors[] = 'Choose a valid check-in date.';
}
if (!$checkOut || $checkOut->format('Y-m-d') !== $checkOutValue || ($checkIn && $checkOut <= $checkIn)) {
    $errors[] = 'Check-out must be later than check-in.';
}
if ($adults === false || $children === false) {
    $errors[] = 'Choose a valid number of guests.';
}

try {
    $pdo = database();
    if (!$errors) {
        $roomStatement = $pdo->prepare(
            'SELECT id, name, description, max_adults, max_children, bed_summary, base_price, image_path
             FROM room_types WHERE id = :id AND is_active = 1 LIMIT 1'
        );
        $roomStatement->execute(['id' => $roomTypeId]);
        $roomType = $roomStatement->fetch();

        if (!$roomType || $adults > (int) $roomType['max_adults'] || $children > (int) $roomType['max_children']) {
            $errors[] = 'That room type cannot accommodate the selected guests.';
        }
    }

    $userStatement = $pdo->prepare(
        "SELECT first_name, last_name, email, phone FROM users
         WHERE id = :id AND status = 'active' LIMIT 1"
    );
    $userStatement->execute(['id' => $_SESSION['user_id']]);
    $user = $userStatement->fetch();
    if (!$user) {
        throw new RuntimeException('User account not found.');
    }
} catch (Throwable $exception) {
    $errors[] = 'Booking details cannot be loaded right now.';
}

$nights = (!$errors && $checkIn && $checkOut) ? (int) $checkIn->diff($checkOut)->days : 0;
$subtotal = $roomType ? $nights * (float) $roomType['base_price'] : 0;

if (empty($_SESSION['booking_csrf'])) {
    $_SESSION['booking_csrf'] = bin2hex(random_bytes(32));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$errors) {
    $submittedToken = $_POST['csrf_token'] ?? '';
    if (!is_string($submittedToken) || !hash_equals($_SESSION['booking_csrf'], $submittedToken)) {
        $errors[] = 'The booking form expired. Refresh the page and try again.';
    } else {
        $specialRequests = trim($_POST['special_requests'] ?? '');
        if (mb_strlen($specialRequests) > 2000) {
            $errors[] = 'Special requests must be 2,000 characters or fewer.';
        }

        if (!$errors) {
            try {
                $pdo->beginTransaction();
                $availableRoom = $pdo->prepare(
                    "SELECT r.id
                     FROM rooms r
                     WHERE r.room_type_id = :room_type_id
                       AND r.status = 'available'
                       AND NOT EXISTS (
                           SELECT 1
                           FROM active_booking_rooms abr
                           WHERE abr.room_id = r.id
                             AND abr.check_in_date < :check_out
                             AND abr.check_out_date > :check_in
                       )
                     ORDER BY r.id
                     LIMIT 1
                     FOR UPDATE"
                );
                $availableRoom->execute([
                    'room_type_id' => $roomTypeId,
                    'check_out' => $checkOutValue,
                    'check_in' => $checkInValue,
                ]);
                $roomId = $availableRoom->fetchColumn();

                if (!$roomId) {
                    throw new DomainException('The selected room is no longer available. Search again for another room.');
                }

                $bookingReference = 'SEH-' . strtoupper(bin2hex(random_bytes(4)));
                $bookingStatement = $pdo->prepare(
                    "INSERT INTO bookings
                        (booking_reference, user_id, guest_first_name, guest_last_name, guest_email,
                         guest_phone, check_in_date, check_out_date, adults, children, status,
                         subtotal, tax_amount, total_amount, currency, special_requests)
                     VALUES
                        (:reference, :user_id, :first_name, :last_name, :email, :phone,
                         :check_in, :check_out, :adults, :children, 'confirmed',
                         :subtotal, 0.00, :total, 'MYR', :special_requests)"
                );
                $bookingStatement->execute([
                    'reference' => $bookingReference,
                    'user_id' => $_SESSION['user_id'],
                    'first_name' => $user['first_name'],
                    'last_name' => $user['last_name'],
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'check_in' => $checkInValue,
                    'check_out' => $checkOutValue,
                    'adults' => $adults,
                    'children' => $children,
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                    'special_requests' => $specialRequests !== '' ? $specialRequests : null,
                ]);

                $bookingId = (int) $pdo->lastInsertId();
                $bookingRoomStatement = $pdo->prepare(
                    'INSERT INTO booking_rooms (booking_id, room_id, nightly_rate, adults, children)
                     VALUES (:booking_id, :room_id, :nightly_rate, :adults, :children)'
                );
                $bookingRoomStatement->execute([
                    'booking_id' => $bookingId,
                    'room_id' => $roomId,
                    'nightly_rate' => $roomType['base_price'],
                    'adults' => $adults,
                    'children' => $children,
                ]);

                $pdo->commit();
                unset($_SESSION['booking_csrf']);
                header('Location: booking_confirmation.php?reference=' . urlencode($bookingReference));
                exit;
            } catch (DomainException $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $errors[] = $exception->getMessage();
            } catch (Throwable $exception) {
                if ($pdo->inTransaction()) {
                    $pdo->rollBack();
                }
                $errors[] = 'The booking could not be completed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stay Easy Hotel: Confirm Booking</title>
        <link rel="stylesheet" href="StayEasyStyle.css">
    </head>
    <body>
        <?php include 'includes/header.php'; ?>
        <main class="container booking-page">
            <h1 class="sectionTitle">Confirm Your Booking</h1>

            <?php if ($errors): ?>
                <div class="form-message error-message" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul></div>
                <a class="button" href="search.php">Return to Room Search</a>
            <?php elseif ($roomType && $user): ?>
                <div class="booking-review">
                    <img src="<?= htmlspecialchars($roomType['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($roomType['name'], ENT_QUOTES, 'UTF-8') ?>">
                    <section class="booking-summary">
                        <h2><?= htmlspecialchars($roomType['name'], ENT_QUOTES, 'UTF-8') ?></h2>
                        <dl>
                            <div><dt>Check-in</dt><dd><?= $checkIn->format('j F Y') ?></dd></div>
                            <div><dt>Check-out</dt><dd><?= $checkOut->format('j F Y') ?></dd></div>
                            <div><dt>Length</dt><dd><?= $nights ?> night<?= $nights === 1 ? '' : 's' ?></dd></div>
                            <div><dt>Guests</dt><dd><?= (int) $adults ?> adult<?= (int) $adults === 1 ? '' : 's' ?>, <?= (int) $children ?> child<?= (int) $children === 1 ? '' : 'ren' ?></dd></div>
                            <div><dt>Guest</dt><dd><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name'], ENT_QUOTES, 'UTF-8') ?></dd></div>
                            <div><dt>Total</dt><dd><strong>MYR <?= number_format($subtotal, 2) ?></strong></dd></div>
                        </dl>

                        <form class="booking-confirm-form" method="post" action="booking.php">
                            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['booking_csrf'], ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="room_type_id" value="<?= (int) $roomTypeId ?>">
                            <input type="hidden" name="check_in" value="<?= htmlspecialchars($checkInValue, ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="check_out" value="<?= htmlspecialchars($checkOutValue, ENT_QUOTES, 'UTF-8') ?>">
                            <input type="hidden" name="adults" value="<?= (int) $adults ?>">
                            <input type="hidden" name="children" value="<?= (int) $children ?>">
                            <div class="form-field">
                                <label for="special_requests">Special requests <span>(optional)</span></label>
                                <textarea id="special_requests" name="special_requests" maxlength="2000" rows="4"></textarea>
                            </div>
                            <p class="bodyText booking-note">Payment is due upon arrival. Your booking will be confirmed immediately.</p>
                            <button type="submit">Confirm Booking</button>
                        </form>
                    </section>
                </div>
            <?php endif; ?>
        </main>
        <?php include 'includes/footer.php'; ?>
    </body>
</html>
