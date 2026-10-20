<?php

// =====================================
// SOPHEA PRODUCT INFORMATION
// =====================================

$products = [

    1 => [
        "name" => "CURRENT",
        "description" => "White Tea",
        "image" => "img/sophea-current.png",
        "story" => "A gentle expression of clarity and calm."
    ],

    2 => [
        "name" => "OBSIDIAN",
        "description" => "Black Sea",
        "image" => "img/sophea-obsidian.png",
        "story" => "A deeper, atmospheric fragrance inspired by quiet moments."
    ],

    3 => [
        "name" => "DUNE",
        "description" => "White Orris & Sandalwood",
        "image" => "img/sophea-dune.jpg",
        "story" => "A warm, refined fragrance inspired by stillness and simplicity."
    ]
];

// =====================================
// GET PRODUCT ID FROM URL
// =====================================

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

// Find selected product

$product = $products[$id] ?? null;

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>SOPHEA | Product Details</title>

    <?php include "parts/meta.php"; ?>

</head>

<body>

    <?php include "parts/navbar.php"; ?>

    <main class="sophea-page">

        <?php if ($product): ?>

            <div class="sophea-product-detail">

                <div>

                    <img
                        src="<?= htmlspecialchars($product['image']) ?>"
                        alt="<?= htmlspecialchars($product['name']) ?> candle"
                    >

                </div>

                <div>

                    <p class="sophea-product-description">
                        THE COLLECTION
                    </p>

                    <h1>
                        <?= htmlspecialchars($product['name']) ?>
                    </h1>

                    <p>
                        <?= htmlspecialchars($product['description']) ?>
                    </p>

                    <p>
                        <?= htmlspecialchars($product['story']) ?>
                    </p>

                    <a
                        href="product_list.php"
                        class="sophea-back-link"
                    >
                        ← BACK TO COLLECTION
                    </a>

                </div>

            </div>

        <?php else: ?>

            <h1 class="sophea-page-title">
                Product Not Found
            </h1>

            <p class="sophea-page-description">
                Please select a valid product.
            </p>

            <div style="text-align: center;">

                <a
                    href="product_list.php"
                    class="sophea-button"
                >
                    View Collection
                </a>

            </div>

        <?php endif; ?>

    </main>

</body>

</html>