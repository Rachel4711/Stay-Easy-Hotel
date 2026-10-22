<?php require_once __DIR__ . '/includes/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stay Easy Hotel: Food Page</title>
        <link rel="stylesheet" href="StayEasyStyle.css">
    </head>

    <body>
        <?php include 'includes/header.php'; ?>

        <div id="banner">
            <img src="assets/banner4.jpg" alt="Stay Easy Hotel">
        </div>

        <div class="container">
            <h1 class="sectionTitle">Eat & Drink Easy.</h1>
            <p class="bodyText"> 
                At Stay Easy Hotel, we believe that good food adds comfort to every stay. 
                Our dining options are thoughtfully arranged to suit your daily routine—from a refreshing breakfast in the morning to relaxing drinks in the evening.
                 Whether complimentary or available at an additional charge, each option is designed for convenience and enjoyment.
            </p>
        </div>

        <div id="foodContainer">
            <div id="foodMenuContainer">
                <hr id="menu-line">
                    <nav class="menu-nav bodyText">
                        <a href="#morning">Easy Morning Café</a>
                        <a href="#bites">Easy Bites Lounge</a>
                        <a href="#skyCorner">Easy Sky View Corner</a>
                        <a href="#inRoom">In-Room Dining</a>
                    </nav>
                <hr id="menu-line">
            </div>

            <div class="container">
                <h2 class="sectionTitle" id="morning">Easy Morning Café</h2>
                <p class="bodyText"><b>Operating Hours:</b> 7:00 AM - 10:30 AM </p>
                <p class="bodyText">Start your day with a satisfying breakfast at the Easy Morning Café, served in a bright and welcoming environment.</p>
                <p class="bodyText">Complimentary for in-house guests:</p>
                <ul class="bodyText">
                    <li>Daily breakfast set</li>
                    <li>Freshly brewed coffee and tea</li>
                </ul>
                <p class="bodyText">Available upon requests:</p>
                <ul class="bodyText">
                    <li>Breakfast add-ons</li>
                    <li>Pastries and baked goods</li>
                    <li>Specialty coffee beverages</li>
                </ul>
                <p class="bodyText">Breakfast selection includes:</p>
                <ul class="bodyText">
                    <li>Local breakfast favourites</li>
                    <li>Light international dishes</li>
                    <li>Fresh bread, spreads and fruits</li>
                </ul>

                <h2 class="sectionTitle" id="bites">Easy Bites Lounge</h2>
                <p class="bodyText"><b>Operating Hours:</b> 12:00 AM - 9:30 AM </p>
                <p class="bodyText">The Easy Bites Lounge offers a casual dining experience throughout the day, perfect for lunch or dinner without leaving the hotel.</p>
                <p class="bodyText">Complimentary for in-house guests:</p>
                <ul class="bodyText">
                    <li>Local comfort dishes</li>
                    <li>International main courses</li>
                    <li>Vegetarian-friendly options</li>
                    <li>Soft drinks and refreshing beverages</li>
                </ul>

                <h2 class="sectionTitle" id="skyCorner">Easy Sky View Corner</h2>
                <p class="bodyText"><b>Operating Hours:</b> 6:00 AM -11:00 AM </p>
                <p class="bodyText">Relax in the evening at the Easy Sky View Corner while enjoying a calm night-sky or city view.</p>
                <p class="bodyText">Available at additional charge:</p>
                <ul class="bodyText">
                    <li>Soft drinks and mocktails</li>
                    <li>Light snacks and finger food</li>
                </ul>

                <h2 class="sectionTitle" id="inRoom">In-Room Dining</h2>
                <p class="bodyText"><b>Operating Hours:</b> 8:00 AM -10:00 AM </p>
                <p class="bodyText">For guests who prefer privacy, in-room dining is available upon request.</p>
                <p class="bodyText">Complimentary for in-house guests:</p>
                <ul class="bodyText">
                    <li>Selected meals from the Easy Bites menu</li>
                    <li>Soft drinks and mocktails</li>
                    <li>Light snacks and finger food</li>
                </ul>
            </div>
        </div>

        <?php include 'includes/footer.php'; ?>
    </body>
</html>
