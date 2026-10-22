<?php require_once __DIR__ . '/includes/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stay Easy Hotel: About Page</title>
        <link rel="stylesheet" href="StayEasyStyle.css">
    </head>

    <body>
        <?php include 'includes/header.php'; ?>

         <div id="banner">
            <img src="assets/banner2.jpg" alt="Bright and welcoming Stay Easy Hotel interior">
        </div>

       <main id="main-content" class="container content-page" tabindex="-1">
        <header class="page-intro">
            <p class="page-eyebrow">Relax. Reset. Recharge.</p>
            <h1 class="sectionTitle">About Stay Easy Hotel</h1>
            <p class="bodyText">
                We believe a comfortable stay should be simple, stress-free, and welcoming. Designed for travellers who value convenience and comfort, our hotel offers a relaxing place to feel at home—whether you are staying for one night or an extended visit.
            </p>
        </header>
        <div class="imageContainer">
            <img src="assets/pic2.jpg" alt="Stay Easy Hotel guest lounge">
            <img src="assets/pic3.jpg" alt="Stay Easy Hotel reception and seating area">
        </div>
        <div class="about-grid">
            <article class="content-card">
                <h2>Our Story</h2>
                <p>Stay Easy Hotel was created with one goal: to make every stay effortless. We provide cosy rooms, thoughtful amenities, and friendly service without unnecessary complications.</p>
            </article>
            <article class="content-card">
                <h2>Our Commitment</h2>
                <p>Our team delivers reliable service with a personal touch. We pay attention to the small details that help every guest feel comfortable, supported, and welcome.</p>
            </article>
            <article class="content-card">
                <h2>Why Stay With Us</h2>
                <ul>
                    <li>Comfortable, thoughtfully designed rooms</li>
                    <li>Friendly and attentive staff</li>
                    <li>Convenient location and modern amenities</li>
                    <li>A calm atmosphere for every guest</li>
                </ul>
            </article>
            <article class="content-card">
                <h2>Our Promise</h2>
                <p>Comfort meets convenience here. We give you room to relax, recharge, and focus on enjoying your journey and creating meaningful memories.</p>
            </article>
        </div>
       </main>
       


        <?php include 'includes/footer.php'; ?>
    </body>
</html>
