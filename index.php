<?php

$pageTitle = "KisanSaathi - Smart Farming, Better Harvest";

require_once "config/database.php";

?>

<?php include "includes/header.php"; ?>

<?php include "includes/navbar.php"; ?>




<!-- HERO SECTION -->

<!-- <section class="hero-section">

    <div class="container">

        <div class="row align-items-center min-vh-75">

            <div class="col-lg-6">

                <span class="hero-badge">
                    🌱 Trusted Agriculture Marketplace
                </span>

                <h1>
                    Smart Farming.
                    <br>
                    <span>Better Harvest.</span>
                </h1>

                <p class="hero-text">
                    Quality fertilizers, seeds, pesticides and
                    farming products — all in one place.
                </p>

                <div class="hero-buttons">

                    <a href="products.php"
                       class="btn btn-success btn-lg px-4">

                        Shop Products
                        <i class="bi bi-arrow-right"></i>

                    </a>

                    <a href="categories.php"
                       class="btn btn-outline-success btn-lg px-4">

                        Explore Categories

                    </a>

                </div>

            </div>

            <div class="col-lg-6">

                <div class="hero-visual">

                    <div class="hero-circle">

                        <i class="bi bi-flower1"></i>

                    </div>

                    <div class="floating-card card-one">
                        🌱 Quality Seeds
                    </div>

                    <div class="floating-card card-two">
                        🧪 Trusted Fertilizers
                    </div>

                    <div class="floating-card card-three">
                        🚜 Better Farming
                    </div>

                </div>

            </div>

        </div>

    </div>

</section> -->

<!-- =========================================================
     KISANSAATHI HERO SECTION
========================================================= -->

<section class="ks-hero">

    <div class="container">

        <div class="row align-items-center g-5">

            <!-- =================================================
                 LEFT CONTENT
            ================================================== -->

            <div class="col-lg-5">

                <div class="ks-hero-content">

                    <!-- Badge -->

                    <div class="ks-hero-badge">

                        <span>🌱</span>

                        Trusted Agriculture Marketplace

                    </div>


                    <!-- Heading -->

                    <h1>

                        Smart Farming.

                        <span>
                            Better Harvest.
                        </span>

                    </h1>


                    <!-- Description -->

                    <p class="ks-hero-description">

                        Quality fertilizers, seeds, pesticides
                        and farming products — all in one place.

                    </p>


                    <!-- Buttons -->

                    <div class="ks-hero-buttons">

                        <a
                            href="products.php"
                            class="ks-btn-primary"
                        >

                            Shop Products

                            <span>→</span>

                        </a>


                        <a
                            href="categories.php"
                            class="ks-btn-outline"
                        >

                            Explore Categories

                        </a>

                    </div>


                    <!-- Trust Statistics -->

                    <div class="ks-hero-stats">

                        <div class="ks-stat">

                            <strong>
                                10,000+
                            </strong>

                            <span>
                                Happy Farmers
                            </span>

                        </div>


                        <div class="ks-stat">

                            <strong>
                                100%
                            </strong>

                            <span>
                                Quality Assured
                            </span>

                        </div>


                        <div class="ks-stat">

                            <strong>
                                Fast
                            </strong>

                            <span>
                                Delivery
                            </span>

                        </div>


                        <div class="ks-stat">

                            <strong>
                                24/7
                            </strong>

                            <span>
                                Support
                            </span>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 RIGHT IMAGE SLIDER
            ================================================== -->

            <div class="col-lg-7">

                <div
                    id="heroCarousel"
                    class="carousel slide ks-hero-slider"
                    data-bs-ride="carousel"
                    data-bs-interval="3500"
                    data-bs-pause="hover"
                >


                    <!-- =================================================
                         SLIDES
                    ================================================== -->

                    <div class="carousel-inner">


                        <!-- ===============================
                             SLIDE 1
                        ================================ -->

                        <div
                            class="carousel-item active"
                        >

                            <div class="ks-slide">

                                <img
                                    src="assets/images/hero/hero-1.jpg"
                                    alt="Smart Farming"
                                    class="ks-slide-image"
                                >


                                <div class="ks-slide-overlay"></div>


                                <div class="ks-slide-caption">

                                    <span>
                                        🌱 KisanSaathi
                                    </span>

                                    <h2>
                                        Quality Farming Products
                                    </h2>

                                    <p>
                                        Everything farmers need
                                        in one place.
                                    </p>

                                </div>

                            </div>

                        </div>



                        <!-- ===============================
                             SLIDE 2
                        ================================ -->

                        <div
                            class="carousel-item"
                        >

                            <div class="ks-slide">

                                <img
                                    src="assets/images/hero/hero-2.jpg"
                                    alt="Quality Seeds"
                                    class="ks-slide-image"
                                >


                                <div class="ks-slide-overlay"></div>


                                <div class="ks-slide-caption">

                                    <span>
                                        🌾 Better Seeds
                                    </span>

                                    <h2>
                                        Grow Healthier Crops
                                    </h2>

                                    <p>
                                        Choose quality seeds
                                        for better production.
                                    </p>

                                </div>

                            </div>

                        </div>



                        <!-- ===============================
                             SLIDE 3
                        ================================ -->

                        <div
                            class="carousel-item"
                        >

                            <div class="ks-slide">

                                <img
                                    src="assets/images/hero/hero-3.jpg"
                                    alt="Agricultural Fertilizers"
                                    class="ks-slide-image"
                                >


                                <div class="ks-slide-overlay"></div>


                                <div class="ks-slide-caption">

                                    <span>
                                        🌿 Trusted Fertilizers
                                    </span>

                                    <h2>
                                        Nourish Your Crops
                                    </h2>

                                    <p>
                                        Reliable fertilizers
                                        for healthy crop growth.
                                    </p>

                                </div>

                            </div>

                        </div>



                        <!-- ===============================
                             SLIDE 4
                        ================================ -->

                        <div
                            class="carousel-item"
                        >

                            <div class="ks-slide">

                                <img
                                    src="assets/images/hero/hero-4.jpg"
                                    alt="Modern Farming"
                                    class="ks-slide-image"
                                >


                                <div class="ks-slide-overlay"></div>


                                <div class="ks-slide-caption">

                                    <span>
                                        🚜 Modern Farming
                                    </span>

                                    <h2>
                                        Farm Smarter
                                    </h2>

                                    <p>
                                        Discover tools and products
                                        for modern agriculture.
                                    </p>

                                </div>

                            </div>

                        </div>



                        <!-- ===============================
                             SLIDE 5
                        ================================ -->

                        <div
                            class="carousel-item"
                        >

                            <div class="ks-slide">

                                <img
                                    src="assets/images/hero/hero-5.jpg"
                                    alt="Better Harvest"
                                    class="ks-slide-image"
                                >


                                <div class="ks-slide-overlay"></div>


                                <div class="ks-slide-caption">

                                    <span>
                                        🌱 Better Farming
                                    </span>

                                    <h2>
                                        Better Harvest
                                    </h2>

                                    <p>
                                        Quality products for
                                        stronger and healthier crops.
                                    </p>

                                </div>

                            </div>

                        </div>

                    </div>



                    <!-- =================================================
                         PREVIOUS BUTTON
                    ================================================== -->

                    <button
                        class="carousel-control-prev ks-slider-prev"
                        type="button"
                        data-bs-target="#heroCarousel"
                        data-bs-slide="prev"
                        aria-label="Previous slide"
                    >

                        <span class="ks-arrow">
                            ←
                        </span>

                    </button>



                    <!-- =================================================
                         NEXT BUTTON
                    ================================================== -->

                    <button
                        class="carousel-control-next ks-slider-next"
                        type="button"
                        data-bs-target="#heroCarousel"
                        data-bs-slide="next"
                        aria-label="Next slide"
                    >

                        <span class="ks-arrow">
                            →
                        </span>

                    </button>



                    <!-- =================================================
                         SLIDE INDICATORS
                    ================================================== -->

                    <div class="carousel-indicators ks-slider-indicators">

                        <button
                            type="button"
                            data-bs-target="#heroCarousel"
                            data-bs-slide-to="0"
                            class="active"
                            aria-current="true"
                            aria-label="Slide 1"
                        ></button>


                        <button
                            type="button"
                            data-bs-target="#heroCarousel"
                            data-bs-slide-to="1"
                            aria-label="Slide 2"
                        ></button>


                        <button
                            type="button"
                            data-bs-target="#heroCarousel"
                            data-bs-slide-to="2"
                            aria-label="Slide 3"
                        ></button>


                        <button
                            type="button"
                            data-bs-target="#heroCarousel"
                            data-bs-slide-to="3"
                            aria-label="Slide 4"
                        ></button>


                        <button
                            type="button"
                            data-bs-target="#heroCarousel"
                            data-bs-slide-to="4"
                            aria-label="Slide 5"
                        ></button>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


        <!-- PREVIOUS -->

        <button
            class="carousel-control-prev"
            type="button"
            data-bs-target="#heroCarousel"
            data-bs-slide="prev"
        >

            <span class="carousel-control-prev-icon"></span>

        </button>


        <!-- NEXT -->

        <button
            class="carousel-control-next"
            type="button"
            data-bs-target="#heroCarousel"
            data-bs-slide="next"
        >

            <span class="carousel-control-next-icon"></span>

        </button>


        <!-- DOTS -->

        <div class="carousel-indicators">

            <button
                type="button"
                data-bs-target="#heroCarousel"
                data-bs-slide-to="0"
                class="active"
            ></button>

            <button
                type="button"
                data-bs-target="#heroCarousel"
                data-bs-slide-to="1"
            ></button>

            <button
                type="button"
                data-bs-target="#heroCarousel"
                data-bs-slide-to="2"
            ></button>

        </div>

    </div>

</div>


<!-- =========================================================
     SHOP BY CATEGORY
========================================================= -->

<section class="categories-section py-5">

    <div class="container">

        <!-- HEADING -->

        <div class="text-center mb-5">

            <span class="section-label">
                EXPLORE
            </span>

            <h2 class="section-title">
                Shop by Category
            </h2>

            <p class="section-subtitle">
                Everything you need for healthier crops.
            </p>

        </div>


        <!-- CATEGORY GRID -->

        <div class="row g-4">


            <!-- FERTILIZERS -->

            <div class="col-lg-4 col-md-6">

                <a
                    href="categories.php?id=1"
                    class="category-card"
                >

                    <div class="category-image">

                        <img
                            src="assets/images/categories/fertilizers.jpg"
                            alt="Fertilizers"
                        >

                    </div>

                    <div class="category-content">

                        <span class="category-icon">
                            🧪
                        </span>

                        <h3>
                            Fertilizers
                        </h3>

                        <p>
                            25+ Products
                        </p>

                    </div>

                </a>

            </div>


            <!-- SEEDS -->

            <div class="col-lg-4 col-md-6">

                <a
                    href="categories.php?id=2"
                    class="category-card"
                >

                    <div class="category-image">

                        <img
                            src="assets/images/categories/seeds.jpg"
                            alt="Seeds"
                        >

                    </div>

                    <div class="category-content">

                        <span class="category-icon">
                            🌾
                        </span>

                        <h3>
                            Seeds
                        </h3>

                        <p>
                            30+ Products
                        </p>

                    </div>

                </a>

            </div>


            <!-- PESTICIDES -->

            <div class="col-lg-4 col-md-6">

                <a
                    href="categories.php?id=3"
                    class="category-card"
                >

                    <div class="category-image">

                        <img
                            src="assets/images/categories/pesticides.jpg"
                            alt="Pesticides"
                        >

                    </div>

                    <div class="category-content">

                        <span class="category-icon">
                            🐛
                        </span>

                        <h3>
                            Pesticides
                        </h3>

                        <p>
                            20+ Products
                        </p>

                    </div>

                </a>

            </div>


            <!-- BIO FERTILIZERS -->

            <div class="col-lg-4 col-md-6">

                <a
                    href="categories.php?id=4"
                    class="category-card"
                >

                    <div class="category-image">

                        <img
                            src="assets/images/categories/bio-fertilizers.jpg"
                            alt="Bio-Fertilizers"
                        >

                    </div>

                    <div class="category-content">

                        <span class="category-icon">
                            🌿
                        </span>

                        <h3>
                            Bio-Fertilizers
                        </h3>

                        <p>
                            15+ Products
                        </p>

                    </div>

                </a>

            </div>


            <!-- MICRONUTRIENTS -->

            <div class="col-lg-4 col-md-6">

                <a
                    href="categories.php?id=5"
                    class="category-card"
                >

                    <div class="category-image">

                        <img
                            src="assets/images/categories/image2.jpg"
                            alt="Micronutrients"
                        >

                    </div>

                    <div class="category-content">

                        <span class="category-icon">
                            🧬
                        </span>

                        <h3>
                            Micronutrients
                        </h3>

                        <p>
                            12+ Products
                        </p>

                    </div>

                </a>

            </div>


            <!-- FARM TOOLS -->

            <div class="col-lg-4 col-md-6">

                <a
                    href="categories.php?id=6"
                    class="category-card"
                >

                    <div class="category-image">

                        <img
                            src="assets/images/categories/image1.jpg"
                            alt="Farm Tools"
                        >

                    </div>

                    <div class="category-content">

                        <span class="category-icon">
                            🚜
                        </span>

                        <h3>
                            Farm Tools
                        </h3>

                        <p>
                            20+ Products
                        </p>

                    </div>

                </a>

            </div>


        </div>

    </div>

</section>

<!-- WHY  KisanSaathi-->

<section class="why-section py-5">

    <div class="container">

        <div class="section-heading text-center mb-5">

            <!-- <span>WHY US</span> -->

            <h2>
                Why Choose KisanSaathi?
            </h2>

        </div>

        <div class="row g-4">

            <div class="col-md-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        <i class="bi bi-patch-check"></i>
                    </div>

                    <h5>Quality Products</h5>

                    <p>
                        Carefully selected agricultural products
                        for better crop performance.
                    </p>

                </div>

            </div>

            <div class="col-md-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        <i class="bi bi-truck"></i>
                    </div>

                    <h5>Easy Delivery</h5>

                    <p>
                        Get your farming essentials delivered
                        conveniently to your doorstep.
                    </p>

                </div>

            </div>

            <div class="col-md-4">

                <div class="feature-card">

                    <div class="feature-icon">
                        <i class="bi bi-headset"></i>
                    </div>

                    <h5>Farmer Support</h5>

                    <p>
                        Helpful information and support to make
                        better farming decisions.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>


<?php include "includes/footer.php"; ?>