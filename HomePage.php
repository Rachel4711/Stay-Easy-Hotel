<?php require_once __DIR__ . '/includes/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stay Easy Hotel: Home Page</title>
        <link rel="stylesheet" href="StayEasyStyle.css">
    </head>

    <body>
        <?php include 'includes/header.php'; ?>

        <div id="banner">
            <img src="assets/banner1.jpg" alt="Stay Easy Hotel exterior and welcoming entrance">
        </div>

        <main id="main-content" class="container" tabindex="-1">
            <div class="textPlacement home-intro">
                <h1 class="sectionTitle">
                    Stay Easy Hotel |<br> Relax. Reset. Recharge
                </h1>

                <p class="bodyText">
                    At Stay Easy Hotel, we believe that a good stay should feel effortless. 
                    Our welcoming atmosphere, comfortable rooms, and friendly service are here to make you feel right at home. 
                    Whether it’s a short getaway or a longer stay, we’re here to make every moment easy and enjoyable. From the moment you arrive, our attentive staff is ready to cater to your needs, ensuring a seamless experience. 
                    Enjoy modern amenities, relaxing spaces, and thoughtful touches designed to make your visit memorable. 
                    At Stay Easy Hotel, comfort meets convenience so that you can truly unwind and focus on what matters most—creating unforgettable memories.
                </p>
            </div>

            <div class="textPlacement">
                <div id ="subTextPlacement">
                    <h2 class="sectionTitle">Welcome to Stay Easy Hotel</h2>
                    <p class="bodyText">
                        Where your comfort comes first. Enjoy a simple, relaxing stay with everything you need for a good night's rest and a pleasant journey ahead. 
                        Our cozy rooms are thoughtfully designed to combine comfort and convenience, ensuring you feel at home from the moment you arrive. 
                        Whether you’re traveling for business or leisure, our friendly staff is here to make your experience effortless and memorable. 
                        Relax, unwind, and let us take care of the details, so your stay is as enjoyable as it is easy.
                    </p>
                </div>

                <div class="pic">
                    <img src="assets/pic1.jpg" alt="Comfortable lounge seating inside Stay Easy Hotel">
                </div>
            </div>


            <p class = "bodyText">Terms and conditions:</p>
            <ul class = "bodyText">
                <li>Payment upon arrival.</li>
                <li>If your booking is to be paid upon arrival, your booked room(s) will be held for check-in until 6 p.m. of the scheduled check-in date, after which the booking will be subject to availability upon check-in.</li>
                <li>Other terms and conditions may apply.</li>
            </ul>
            

            <a class="button" href="room.php">Explore Our Rooms</a>
        </main>

        <?php include 'includes/footer.php'; ?>
    </body>
</html>
