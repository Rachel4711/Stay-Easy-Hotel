<?php require_once __DIR__ . '/includes/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stay Easy Hotel: Neighbourhood</title>
        <link rel="stylesheet" href="StayEasyStyle.css">
    </head>
    <body>
        <?php include 'includes/header.php'; ?>

        <div id="banner">
            <img src="assets/banner5.jpg" alt="The neighbourhood around Stay Easy Hotel">
        </div>

        <main id="main-content" class="container content-page neighbourhood-page" tabindex="-1">
            <header class="page-intro">
                <p class="page-eyebrow">Step outside and explore</p>
                <h1 class="sectionTitle">Your Guide to the Neighbourhood</h1>
                <p class="bodyText">Stay Easy Hotel puts transport, culture, food, and green spaces within easy reach. Use these suggestions to plan a relaxed afternoon, a photo walk, or a full day around Kuala Lumpur.</p>
            </header>

            <section class="neighbourhood-grid" aria-label="Nearby recommendations">
                <article class="place-card">
                    <span class="place-type">Getting around</span>
                    <h2>KL Sentral</h2>
                    <p>The city’s main transport hub connects rail, airport services, buses, and local transit. It is a convenient starting point for exploring beyond the neighbourhood.</p>
                    <p class="place-tip">Best for: quick city connections</p>
                </article>
                <article class="place-card">
                    <span class="place-type">Food &amp; walking</span>
                    <h2>Brickfields</h2>
                    <p>Walk through colourful Little India for banana-leaf rice, curries, sweets, flower stalls, and lively street scenes.</p>
                    <p class="place-tip">Best for: local flavours and an evening stroll</p>
                </article>
                <article class="place-card">
                    <span class="place-type">Shopping &amp; dining</span>
                    <h2>NU Sentral</h2>
                    <p>A convenient stop for cafés, restaurants, daily essentials, shopping, and a comfortable break between journeys.</p>
                    <p class="place-tip">Best for: rainy days and easy meals</p>
                </article>
                <article class="place-card">
                    <span class="place-type">Culture</span>
                    <h2>National Museum</h2>
                    <p>Discover Malaysia’s history, traditions, and cultural heritage in galleries inspired by traditional Malay architecture.</p>
                    <p class="place-tip">Best for: a meaningful half-day visit</p>
                </article>
                <article class="place-card">
                    <span class="place-type">Nature &amp; rest</span>
                    <h2>Perdana Botanical Gardens</h2>
                    <p>Slow down among shaded paths, lawns, gardens, and lake views—a peaceful contrast to the energy of the city.</p>
                    <p class="place-tip">Best for: morning walks and quiet breaks</p>
                </article>
                <article class="place-card">
                    <span class="place-type">Photo stop</span>
                    <h2>Thean Hou Temple</h2>
                    <p>Ornate architecture, lantern-lined courtyards, and elevated city views make this a memorable place for photographs.</p>
                    <p class="place-tip">Best for: architecture and sunset light</p>
                </article>
            </section>

            <section class="neighbourhood-plan">
                <div>
                    <p class="page-eyebrow">An easy afternoon</p>
                    <h2 class="sectionTitle">Walk, taste, and unwind</h2>
                    <p class="bodyText">Begin with lunch in Brickfields, continue to the National Museum, then finish among the greenery of the botanical gardens. Ask our team for directions and current transport suggestions before you leave.</p>
                </div>
                <a class="button" href="contact.php">View Our Location</a>
            </section>
        </main>

        <?php include 'includes/footer.php'; ?>
    </body>
</html>
