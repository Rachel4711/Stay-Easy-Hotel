<?php

declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/database.php';

$today = new DateTimeImmutable('today', new DateTimeZone('Asia/Kuala_Lumpur'));
$checkInValue = trim($_GET['check_in'] ?? '');
$checkOutValue = trim($_GET['check_out'] ?? '');
$adults = filter_var($_GET['adults'] ?? 1, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 10]]);
$children = filter_var($_GET['children'] ?? 0, FILTER_VALIDATE_INT, ['options' => ['min_range' => 0, 'max_range' => 10]]);
$errors = [];
$results = [];
$searched = isset($_GET['check_in'], $_GET['check_out']);

if ($searched) {
    $checkIn = DateTimeImmutable::createFromFormat('!Y-m-d', $checkInValue);
    $checkOut = DateTimeImmutable::createFromFormat('!Y-m-d', $checkOutValue);

    if (!$checkIn || $checkIn->format('Y-m-d') !== $checkInValue || $checkIn < $today) {
        $errors[] = 'Choose a valid check-in date that is not in the past.';
    }
    if (!$checkOut || $checkOut->format('Y-m-d') !== $checkOutValue || ($checkIn && $checkOut <= $checkIn)) {
        $errors[] = 'Check-out must be later than check-in.';
    }
    if ($adults === false || $children === false) {
        $errors[] = 'Choose a valid number of guests.';
    }

    if (!$errors) {
        try {
            $statement = database()->prepare(
                "SELECT rt.id, rt.name, rt.description, rt.max_adults, rt.max_children,
                        rt.bed_summary, rt.size_sqm, rt.base_price, rt.image_path,
                        COUNT(r.id) AS available_rooms
                 FROM room_types rt
                 INNER JOIN rooms r ON r.room_type_id = rt.id AND r.status = 'available'
                 WHERE rt.is_active = 1
                   AND rt.max_adults >= :adults
                   AND rt.max_children >= :children
                   AND NOT EXISTS (
                       SELECT 1
                       FROM active_booking_rooms abr
                       WHERE abr.room_id = r.id
                         AND abr.check_in_date < :check_out
                         AND abr.check_out_date > :check_in
                   )
                 GROUP BY rt.id, rt.name, rt.description, rt.max_adults, rt.max_children,
                          rt.bed_summary, rt.size_sqm, rt.base_price, rt.image_path
                 ORDER BY rt.base_price ASC"
            );
            $statement->execute([
                'adults' => $adults,
                'children' => $children,
                'check_in' => $checkInValue,
                'check_out' => $checkOutValue,
            ]);
            $results = $statement->fetchAll();
        } catch (Throwable $exception) {
            $errors[] = 'Room availability cannot be loaded right now. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stay Easy Hotel: Search Rooms</title>
        <link rel="stylesheet" href="StayEasyStyle.css">
    </head>
    <body>
        <?php include 'includes/header.php'; ?>

        <main class="container booking-page">
            <section class="booking-search-panel" aria-labelledby="room-search-title">
                <h1 class="sectionTitle" id="room-search-title">Search Available Rooms</h1>
                <form class="booking-search-form" method="get" action="search.php">
                    <div class="form-field"><label for="check_in">Check-in</label><input type="date" id="check_in" name="check_in" min="<?= $today->format('Y-m-d') ?>" value="<?= htmlspecialchars($checkInValue, ENT_QUOTES, 'UTF-8') ?>" required></div>
                    <div class="form-field"><label for="check_out">Check-out</label><input type="date" id="check_out" name="check_out" min="<?= $today->format('Y-m-d') ?>" value="<?= htmlspecialchars($checkOutValue, ENT_QUOTES, 'UTF-8') ?>" required></div>
                    <div class="form-field"><label for="adults">Adults</label><input type="number" id="adults" name="adults" min="1" max="10" value="<?= (int) ($adults ?: 1) ?>" required></div>
                    <div class="form-field"><label for="children">Children</label><input type="number" id="children" name="children" min="0" max="10" value="<?= $children === false ? 0 : (int) $children ?>" required></div>
                    <button type="submit">Search Rooms</button>
                </form>
            </section>

            <?php if ($errors): ?>
                <div class="form-message error-message" role="alert"><ul><?php foreach ($errors as $error): ?><li><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></li><?php endforeach; ?></ul></div>
            <?php elseif ($searched): ?>
                <section aria-labelledby="results-title">
                    <h2 class="sectionTitle" id="results-title"><?= count($results) ?> Available Room Type<?= count($results) === 1 ? '' : 's' ?></h2>
                    <?php if (!$results): ?>
                        <p class="bodyText">No rooms match those dates and guest numbers. Try different dates or fewer guests.</p>
                    <?php else: ?>
                        <div class="room-results">
                            <?php foreach ($results as $room): ?>
                                <article class="room-result-card">
                                    <img src="<?= htmlspecialchars($room['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($room['name'], ENT_QUOTES, 'UTF-8') ?>">
                                    <div class="room-result-content">
                                        <h3><?= htmlspecialchars($room['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                                        <p><?= htmlspecialchars($room['description'], ENT_QUOTES, 'UTF-8') ?></p>
                                        <ul>
                                            <li><?= htmlspecialchars($room['bed_summary'], ENT_QUOTES, 'UTF-8') ?></li>
                                            <li>Up to <?= (int) $room['max_adults'] ?> adult<?= (int) $room['max_adults'] === 1 ? '' : 's' ?> and <?= (int) $room['max_children'] ?> child<?= (int) $room['max_children'] === 1 ? '' : 'ren' ?></li>
                                            <li><?= (int) $room['available_rooms'] ?> room<?= (int) $room['available_rooms'] === 1 ? '' : 's' ?> available</li>
                                        </ul>
                                        <div class="room-result-action">
                                            <strong>MYR <?= number_format((float) $room['base_price'], 2) ?> <small>per night</small></strong>
                                            <a class="button" href="booking.php?<?= http_build_query(['room_type_id' => $room['id'], 'check_in' => $checkInValue, 'check_out' => $checkOutValue, 'adults' => $adults, 'children' => $children]) ?>">Reserve</a>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </section>
            <?php endif; ?>
        </main>

        <?php include 'includes/footer.php'; ?>
    </body>
</html>
