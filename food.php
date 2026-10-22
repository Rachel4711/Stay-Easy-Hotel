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
            <img src="assets/banner4.jpg" alt="Dining area at Stay Easy Hotel">
        </div>

        <main id="main-content" class="content-page" tabindex="-1">
        <div class="container page-intro">
            <p class="page-eyebrow">Fresh flavours, easy moments</p>
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
                    <nav class="menu-nav bodyText" aria-label="Dining sections">
                        <a href="#morning">Easy Morning Café</a>
                        <a href="#bites">Easy Bites Lounge</a>
                        <a href="#skyCorner">Easy Sky View Corner</a>
                        <a href="#inRoom">In-Room Dining</a>
                    </nav>
                <hr id="menu-line">
            </div>

            <div class="container dining-content">
                <section class="dining-section" id="morning">
                <h2 class="sectionTitle">Easy Morning Café</h2>
                <p class="bodyText"><b>Operating Hours:</b> 7:00 AM - 10:30 AM </p>
                <p class="bodyText">Start your day with a satisfying breakfast at the Easy Morning Café, served in a bright and welcoming environment.</p>
                <img class="dining-image" src="assets/easy-morning-cafe.png" alt="Warm café interior with counter seating and a pastry display">
                <div class="dining-details dining-details-grid">
                <div class="dining-detail-group">
                <p class="bodyText">Complimentary for in-house guests:</p>
                <ul class="bodyText">
                    <li>Daily breakfast set</li>
                    <li>Freshly brewed coffee and tea</li>
                </ul>
                </div>
                <div class="dining-detail-group">
                <p class="bodyText">Available upon requests:</p>
                <ul class="bodyText">
                    <li>Breakfast add-ons</li>
                    <li>Pastries and baked goods</li>
                    <li>Specialty coffee beverages</li>
                </ul>
                </div>
                <div class="dining-detail-group">
                <p class="bodyText">Breakfast selection includes:</p>
                <ul class="bodyText">
                    <li>Local breakfast favourites</li>
                    <li>Light international dishes</li>
                    <li>Fresh bread, spreads and fruits</li>
                </ul>
                </div>
                </div>
                </section>

                <section class="dining-section" id="bites">
                <h2 class="sectionTitle">Easy Bites Lounge</h2>
                <p class="bodyText"><b>Operating Hours:</b> 12:00 PM - 9:30 PM</p>
                <p class="bodyText">The Easy Bites Lounge offers a casual dining experience throughout the day, perfect for lunch or dinner without leaving the hotel.</p>
                <img class="dining-image" src="assets/easy-bites-lounge.png" alt="A sharing platter of skewers, pastries, meatballs, and snacks">
                <div class="dining-details">
                <div class="dining-detail-group">
                <p class="bodyText">Menu highlights:</p>
                <ul class="bodyText">
                    <li>Local comfort dishes</li>
                    <li>International main courses</li>
                    <li>Vegetarian-friendly options</li>
                    <li>Soft drinks and refreshing beverages</li>
                </ul>
                </div>
                </div>
                </section>

                <section class="dining-section" id="skyCorner">
                <h2 class="sectionTitle">Easy Sky View Corner</h2>
                <p class="bodyText"><b>Operating Hours:</b> 6:00 PM - 11:00 PM</p>
                <p class="bodyText">Relax in the evening at the Easy Sky View Corner while enjoying a calm night-sky or city view.</p>
                <img class="dining-image" src="assets/easy-sky-view-corner.png" alt="Sky-view restaurant with panoramic windows and dining tables">
                <div class="dining-details">
                <div class="dining-detail-group">
                <p class="bodyText">Available at additional charge:</p>
                <ul class="bodyText">
                    <li>Soft drinks and mocktails</li>
                    <li>Light snacks and finger food</li>
                </ul>
                </div>
                </div>
                </section>

                <section class="dining-section" id="inRoom">
                <h2 class="sectionTitle">In-Room Dining</h2>
                <p class="bodyText"><b>Operating Hours:</b> 8:00 AM - 10:00 PM</p>
                <p class="bodyText">For guests who prefer privacy, in-room dining is available upon request.</p>
                <img class="dining-image" src="assets/in-room-dining.png" alt="A selection of fresh breakfast dishes and juices served in-room">
                <div class="dining-details">
                <div class="dining-detail-group">
                <p class="bodyText">Available upon request:</p>
                <ul class="bodyText">
                    <li>Selected meals from the Easy Bites menu</li>
                    <li>Soft drinks and mocktails</li>
                    <li>Light snacks and finger food</li>
                </ul>
                </div>
                </div>
                </section>
            </div>
        </div>
        </main>

        <?php include 'includes/footer.php'; ?>
        <script>
            const diningLinks = Array.from(document.querySelectorAll('.menu-nav a'));
            const diningSections = diningLinks
                .map(link => document.querySelector(link.getAttribute('href')))
                .filter(Boolean);

            const diningObserver = new IntersectionObserver(entries => {
                entries.forEach(entry => {
                    if (!entry.isIntersecting) return;
                    diningLinks.forEach(link => {
                        const isCurrent = link.getAttribute('href') === `#${entry.target.id}`;
                        link.classList.toggle('is-current', isCurrent);
                        if (isCurrent) link.setAttribute('aria-current', 'true');
                        else link.removeAttribute('aria-current');
                    });
                });
            }, { rootMargin: '-20% 0px -65% 0px' });

            diningSections.forEach(section => diningObserver.observe(section));
        </script>
    </body>
</html>
