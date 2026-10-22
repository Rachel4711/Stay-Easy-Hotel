<?php require_once __DIR__ . '/includes/session.php'; ?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Stay Easy Hotel: Gallery</title>
        <link rel="stylesheet" href="StayEasyStyle.css">
    </head>

    <body>
        <?php include 'includes/header.php'; ?>

        <div id="banner">
            <img src="assets/banner1.jpg" alt="Stay Easy Hotel exterior and welcoming entrance">
        </div>

        <main id="main-content" class="container content-page gallery-page" tabindex="-1">
            <header class="page-intro">
                <p class="page-eyebrow">A glimpse of your stay</p>
                <h1 class="sectionTitle">Hotel Gallery</h1>
                <p class="bodyText">Step inside Stay Easy Hotel and explore the comfortable rooms, relaxing spaces, and welcoming atmosphere waiting for you.</p>
            </header>

            <section class="carousel" aria-label="Hotel gallery carousel" tabindex="0">
                <div class="carousel-viewport">
                    <div class="carousel-track">
                        <button class="carousel-slide" type="button" onclick="openModal(1)" aria-label="Enlarge guest room image">
                            <img src="assets/img_wide1.jpg" alt="Spacious Stay Easy Hotel guest room">
                            <span class="carousel-caption">Comfortable guest rooms</span>
                        </button>
                        <button class="carousel-slide" type="button" onclick="openModal(2)" aria-label="Enlarge hotel pool image">
                            <img src="assets/img_wide2.jpg" alt="Stay Easy Hotel swimming pool and exterior">
                            <span class="carousel-caption">Relax by the pool</span>
                        </button>
                        <button class="carousel-slide" type="button" onclick="openModal(3)" aria-label="Enlarge night pool image">
                            <img src="assets/img_wide3.jpg" alt="Stay Easy Hotel pool area at night">
                            <span class="carousel-caption">Evenings at Stay Easy</span>
                        </button>
                        <button class="carousel-slide" type="button" onclick="openModal(4)" aria-label="Enlarge hotel exterior image">
                            <img src="assets/img_wide4.jpg" alt="Stay Easy Hotel building exterior">
                            <span class="carousel-caption">Welcome to Stay Easy Hotel</span>
                        </button>
                    </div>
                </div>

                <button class="carousel-control carousel-previous" type="button" aria-label="Previous carousel image">&#10094;</button>
                <button class="carousel-control carousel-next" type="button" aria-label="Next carousel image">&#10095;</button>

                <div class="carousel-dots" role="group" aria-label="Choose carousel image">
                    <button type="button" aria-label="Show image 1" aria-pressed="true"></button>
                    <button type="button" aria-label="Show image 2" aria-pressed="false"></button>
                    <button type="button" aria-label="Show image 3" aria-pressed="false"></button>
                    <button type="button" aria-label="Show image 4" aria-pressed="false"></button>
                </div>
                <p class="carousel-status" aria-live="polite">Image 1 of 4</p>
            </section>

            <section class="gallery-highlights" aria-labelledby="gallery-highlights-title">
                <div class="gallery-highlight-intro">
                    <p class="page-eyebrow">Made for easy stays</p>
                    <h2 class="sectionTitle" id="gallery-highlights-title">Comfort in Every Corner</h2>
                    <p class="bodyText">From a quiet room after a busy day to an evening beside the pool, every space is designed to help you settle in and slow down.</p>
                </div>
                <div class="gallery-feature-grid">
                    <article class="content-card">
                        <span class="gallery-number">01</span>
                        <h3>Rest Well</h3>
                        <p>Thoughtfully arranged rooms, comfortable beds, and useful amenities make it easy to recharge.</p>
                    </article>
                    <article class="content-card">
                        <span class="gallery-number">02</span>
                        <h3>Take It Easy</h3>
                        <p>Relax by the pool or find a comfortable corner to pause between plans.</p>
                    </article>
                    <article class="content-card">
                        <span class="gallery-number">03</span>
                        <h3>Explore More</h3>
                        <p>A convenient city location keeps transport, dining, shopping, and local culture within reach.</p>
                    </article>
                </div>
            </section>

            <section class="gallery-cta">
                <div>
                    <p class="page-eyebrow">Ready when you are</p>
                    <h2 class="sectionTitle">Find Your Place to Unwind</h2>
                    <p class="bodyText">Compare our room options and choose the space that suits your next trip.</p>
                </div>
                <a class="button" href="search.php">Check Room Availability</a>
            </section>

            <div id="myModal" class="modal" role="dialog" aria-modal="true" aria-label="Hotel image gallery" aria-hidden="true">
                <button class="close" type="button" onclick="closeModal()" aria-label="Close gallery">&times;</button>
                <div class="modal-content">

                <div class="mySlides">
                    <div class="numbertext">1 / 4</div>
                    <img src="assets/img_wide1.jpg" alt="Spacious Stay Easy Hotel guest room enlarged">
                </div>

                <div class="mySlides">
                <div class="numbertext">2 / 4</div>
                <img src="assets/img_wide2.jpg" alt="Stay Easy Hotel gallery image 2 enlarged">
                </div>

                <div class="mySlides">
                <div class="numbertext">3 / 4</div>
                <img src="assets/img_wide3.jpg" alt="Stay Easy Hotel gallery image 3 enlarged">
                </div>

                <div class="mySlides">
                <div class="numbertext">4 / 4</div>
                <img src="assets/img_wide4.jpg" alt="Stay Easy Hotel gallery image 4 enlarged">
                </div>

                <button class="prev" type="button" onclick="plusSlides(-1)" aria-label="Previous image">&#10094;</button>
                <button class="next" type="button" onclick="plusSlides(1)" aria-label="Next image">&#10095;</button>

                <!-- Caption text -->
                <div class="caption-container">
                <p id="caption"></p>
                </div>

                <div class="lightbox-thumbnails" aria-label="Choose gallery image">
                    <button type="button" onclick="currentSlide(1)" aria-label="Show image 1"><img class="demo" src="assets/img1.jpg" alt="" data-caption="Guest room"></button>
                    <button type="button" onclick="currentSlide(2)" aria-label="Show image 2"><img class="demo" src="assets/img2.jpg" alt="" data-caption="Hotel pool and exterior"></button>
                    <button type="button" onclick="currentSlide(3)" aria-label="Show image 3"><img class="demo" src="assets/img3.jpg" alt="" data-caption="Pool area at night"></button>
                    <button type="button" onclick="currentSlide(4)" aria-label="Show image 4"><img class="demo" src="assets/img4.jpg" alt="" data-caption="Hotel building exterior"></button>
                </div>
                </div>
            </div>
        </main>

        <?php include 'includes/footer.php'; ?>

<script>
    var lastFocusedElement = null;

    // Open the Modal
    function openModal(imageNumber) {
        var modal = document.getElementById("myModal");
        lastFocusedElement = document.activeElement;
        currentSlide(imageNumber || carouselIndex + 1);
        modal.style.display = "flex";
        modal.setAttribute("aria-hidden", "false");
        document.body.classList.add("modal-open");
        modal.querySelector(".close").focus();
    }

    // Close the Modal
    function closeModal() {
        var modal = document.getElementById("myModal");
        modal.style.display = "none";
        modal.setAttribute("aria-hidden", "true");
        document.body.classList.remove("modal-open");
        if (lastFocusedElement) lastFocusedElement.focus();
    }

    var modalSlideIndex = 1;
    showSlides(modalSlideIndex);

    // Next/previous controls
    function plusSlides(n) {
        showSlides(modalSlideIndex += n);
    }

    // Thumbnail image controls
    function currentSlide(n) {
        showSlides(modalSlideIndex = n);
    }

    function showSlides(n) {
    var i;
    var slides = document.getElementsByClassName("mySlides");
    var dots = document.getElementsByClassName("demo");
    var captionText = document.getElementById("caption");
    if (n > slides.length) {modalSlideIndex = 1}
    if (n < 1) {modalSlideIndex = slides.length}
    for (i = 0; i < slides.length; i++) {
        slides[i].style.display = "none";
    }
    for (i = 0; i < dots.length; i++) {
        dots[i].className = dots[i].className.replace(" active", "");
    }
    slides[modalSlideIndex-1].style.display = "block";
    dots[modalSlideIndex-1].className += " active";
    captionText.textContent = dots[modalSlideIndex-1].dataset.caption;
    }

    var carousel = document.querySelector(".carousel");
    var carouselTrack = carousel.querySelector(".carousel-track");
    var carouselSlides = Array.from(carousel.querySelectorAll(".carousel-slide"));
    var carouselDots = Array.from(carousel.querySelectorAll(".carousel-dots button"));
    var carouselStatus = carousel.querySelector(".carousel-status");
    var carouselIndex = 0;
    var autoplayTimer;
    var touchStartX = 0;

    function showCarouselSlide(index) {
        carouselIndex = (index + carouselSlides.length) % carouselSlides.length;
        carouselTrack.style.transform = "translateX(-" + (carouselIndex * 100) + "%)";
        carouselDots.forEach(function (dot, dotIndex) {
            dot.setAttribute("aria-pressed", dotIndex === carouselIndex ? "true" : "false");
        });
        carouselStatus.textContent = "Image " + (carouselIndex + 1) + " of " + carouselSlides.length;
    }

    function startAutoplay() {
        if (window.matchMedia("(prefers-reduced-motion: reduce)").matches) return;
        clearInterval(autoplayTimer);
        autoplayTimer = setInterval(function () {
            showCarouselSlide(carouselIndex + 1);
        }, 5000);
    }

    function stopAutoplay() {
        clearInterval(autoplayTimer);
    }

    carousel.querySelector(".carousel-previous").addEventListener("click", function () {
        showCarouselSlide(carouselIndex - 1);
        startAutoplay();
    });

    carousel.querySelector(".carousel-next").addEventListener("click", function () {
        showCarouselSlide(carouselIndex + 1);
        startAutoplay();
    });

    carouselDots.forEach(function (dot, index) {
        dot.addEventListener("click", function () {
            showCarouselSlide(index);
            startAutoplay();
        });
    });

    carousel.addEventListener("keydown", function (event) {
        if (event.key === "ArrowLeft") showCarouselSlide(carouselIndex - 1);
        if (event.key === "ArrowRight") showCarouselSlide(carouselIndex + 1);
    });

    carousel.addEventListener("mouseenter", stopAutoplay);
    carousel.addEventListener("mouseleave", startAutoplay);
    carousel.addEventListener("focusin", stopAutoplay);
    carousel.addEventListener("focusout", startAutoplay);
    carousel.addEventListener("touchstart", function (event) {
        touchStartX = event.changedTouches[0].clientX;
        stopAutoplay();
    }, {passive: true});
    carousel.addEventListener("touchend", function (event) {
        var distance = event.changedTouches[0].clientX - touchStartX;
        if (Math.abs(distance) > 50) showCarouselSlide(carouselIndex + (distance < 0 ? 1 : -1));
        startAutoplay();
    }, {passive: true});

    document.addEventListener("visibilitychange", function () {
        if (document.hidden) stopAutoplay(); else startAutoplay();
    });

    showCarouselSlide(0);
    startAutoplay();

    document.addEventListener("keydown", function (event) {
        var modal = document.getElementById("myModal");
        if (modal.style.display !== "flex") return;
        if (event.key === "Escape") closeModal();
        if (event.key === "ArrowLeft") plusSlides(-1);
        if (event.key === "ArrowRight") plusSlides(1);
        if (event.key === "Tab") {
            var focusable = Array.from(modal.querySelectorAll("button:not([disabled]), [href], [tabindex]:not([tabindex='-1'])"));
            var first = focusable[0];
            var last = focusable[focusable.length - 1];
            if (event.shiftKey && document.activeElement === first) {
                event.preventDefault();
                last.focus();
            } else if (!event.shiftKey && document.activeElement === last) {
                event.preventDefault();
                first.focus();
            }
        }
    });

    document.getElementById("myModal").addEventListener("click", function (event) {
        if (event.target === this) closeModal();
    });
</script>
    </body>
</html>
