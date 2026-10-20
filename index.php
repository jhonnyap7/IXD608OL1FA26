<?php

// ======================================
// SOPHEA — HOMEPAGE
// ======================================

// Featured candles
$featuredProducts = [
    [
        "id" => 1,
        "name" => "CURRENT",
        "description" => "White Tea",
        "image" => "img/sophea-current.png"
    ],
    [
        "id" => 2,
        "name" => "OBSIDIAN",
        "description" => "Black Sea",
        "image" => "img/sophea-obsidian.png"
    ],
    [
        "id" => 3,
        "name" => "DUNE",
        "description" => "White Orris & Sandalwood",
        "image" => "img/sophea-dune.jpg"
    ]
];

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>SOPHEA | Luxury Candles</title>

    <?php include "parts/meta.php"; ?>

</head>

<body>

    <!-- NAVIGATION -->

    <?php include "parts/navbar.php"; ?>


    <!-- HERO SECTION -->

    <section class="sophea-home-hero">

        <div class="sophea-home-hero-image">

            <div class="sophea-home-hero-content">

                <p class="sophea-home-label">
                    SOPHEA
                </p>

                <h1>
                    The Art of Stillness
                </h1>

                <p class="sophea-home-subtitle">
                    Thoughtfully crafted fragrances
                    for moments of clarity and calm.
                </p>

                <a href="product_list.php"
                   class="sophea-home-button">

                    EXPLORE THE COLLECTION

                </a>

            </div>

        </div>

    </section>


    <!-- INTRODUCTION -->

    <section class="sophea-home-intro" id="story">

        <p class="sophea-home-label">
            OUR PHILOSOPHY
        </p>

        <h2>
            A quieter kind of luxury.
        </h2>

        <p>
            SOPHEA is rooted in the belief that
            everyday moments deserve intention.

            Through thoughtfully crafted fragrances,
            we create space for stillness,
            reflection, and connection.
        </p>

    </section>


    <!-- FEATURED COLLECTION -->

    <section class="sophea-home-collection">

        <div class="sophea-home-section-heading">

            <p class="sophea-home-label">
                DISCOVER SOPHEA
            </p>

            <h2>
                The Collection
            </h2>

            <p>
                Fragrances designed to elevate
                the everyday.
            </p>

        </div>


        <div class="sophea-product-grid">

            <?php foreach ($featuredProducts as $product): ?>

                <article class="sophea-product-card">

                    <a href="product_item.php?id=<?= (int) $product['id'] ?>">

                        <img
                            src="<?= htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?> candle"
                        >

                    </a>

                    <h3 class="sophea-product-name">

                        <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>

                    </h3>

                    <p class="sophea-product-description">

                        <?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?>

                    </p>

                    <a
                        href="product_item.php?id=<?= (int) $product['id'] ?>"
                        class="sophea-button"
                    >

                        DISCOVER

                    </a>

                </article>

            <?php endforeach; ?>

        </div>


        <div class="sophea-home-view-all">

            <a href="product_list.php"
               class="sophea-home-text-link">

                VIEW ALL FRAGRANCES →

            </a>

        </div>

    </section>


    <!-- BRAND MESSAGE -->

    <section class="sophea-home-message">

        <div class="sophea-home-message-content">

            <p class="sophea-home-label">
                THE SOPHEA EXPERIENCE
            </p>

            <h2>
                Designed for moments
                that matter.
            </h2>

            <p>
                An invitation to slow down,
                reconnect, and find beauty
                in simplicity.
            </p>

        </div>

    </section>


    <!-- FOOTER -->

    <footer class="sophea-home-footer" id="contact">

        <h2>
            SOPHEA
        </h2>

        <p>
            Clarity. Stillness. Intention.
        </p>

        <a href="mailto:hello@sophea.co">
            hello@sophea.co
        </a>

        <p class="sophea-home-copyright">
            &copy; <?= date("Y") ?> SOPHEA.
            All rights reserved.
        </p>

    </footer>

</body>
</html>