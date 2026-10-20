<?php

// =====================================
// SOPHEA PRODUCT COLLECTION
// =====================================

$products = [
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
    <title>SOPHEA | The Collection</title>

    <?php include "parts/meta.php"; ?>
</head>

<body>

    <?php include "parts/navbar.php"; ?>

    <main class="sophea-page">

        <h1 class="sophea-page-title">
            The Collection
        </h1>

        <p class="sophea-page-description">
            Thoughtfully crafted fragrances for moments of stillness.
        </p>

        <div class="sophea-product-grid">

            <?php foreach ($products as $product): ?>

                <article class="sophea-product-card">

                    <img
                        src="<?= htmlspecialchars($product['image'], ENT_QUOTES, 'UTF-8') ?>"
                        alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?> candle"
                    >

                    <h2 class="sophea-product-name">
                        <?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>
                    </h2>

                    <p class="sophea-product-description">
                        <?= htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8') ?>
                    </p>

                    <a
                        href="product_item.php?id=<?= (int) $product['id'] ?>"
                        class="sophea-button"
                    >
                        Discover
                    </a>

                </article>

            <?php endforeach; ?>

        </div>

    </main>

</body>
</html>