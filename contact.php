<!DOCTYPE html>
<html>
    <head>
        <title>Stay Easy Hotel: Contact Page</title>
        <link rel="stylesheet" href="StayEasyStyle.css">
    </head>

    <body>
        <?php include 'includes/header.php'; ?>

        <div id = "navBar">
            <ul>
                <li><a href="about.php">About</a></li>
                <li><a href="contact.php">Contact Us</a></li>
                <li><a href="room.php">Room Tour</a></li>
                <li><a href="food.php">Eat & Drink</a></li>
                <li><a href="neighbourhood.php">Neighbourhood</a></li>
                <li><a href="gallery.php">Gallery</a></li>
            </ul>
        </div>

        <div class="container">
            <iframe style="margin-top:50px" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3983.8559477038607!2d101.6877037!3d3.1327426999999997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31cc49c1c620dcfd%3A0x8be53a3940f72fea!2sEasy%20Hotel%20KL%20Sentral!5e0!3m2!1sen!2smy!4v1769528711018!5m2!1sen!2smy" 
                width="1200" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
            </iframe>

            <section id="contact">
                <h1 class="sectionTitle">Contact Us</h1>
                <p class="bodyText">Have questions or need help with your booking? Fill out the form below and our team will get back to you!</p>

                <form class="contactForm" action="submit.php" method="post">
                    <div class="textPlacement">
                        <label for="name">Name</label>
                        <input type="text" id="name" name="name" placeholder="Your Name" required>
                    </div>

                    <div class="textPlacement">
                        <label for="email">Email</label>
                        <input type="text" id="email" name="email" placeholder="youremail@email.com" required>
                    </div>

                    <div class="textPlacement">
                        <label for="message">Message</label>
                        <textarea id="message" name="message" placeholder="Write your message here..." required></textarea>
                    </div>

                    <button type="submit">Send Message</button>
                </form>
            </section>
            
        </div>
       


        <?php include 'includes/footer.php'; ?>