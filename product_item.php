<?php

// =====================================
// SOPHEA — PRODUCT DETAIL PAGE
// =====================================

require_once __DIR__ . '/php/functions.php';

// =====================================
// GET PRODUCT ID
// =====================================

$id = filter_input(
    INPUT_GET,
    'id',
    FILTER_VALIDATE_INT
);

$product = null;
$databaseError = false;

// =====================================
// GET PRODUCT FROM MYSQL
// =====================================

if ($id && $id > 0) {

    try {

        $conn = makeConn();

        $stmt = $conn->prepare(
            "SELECT id, name, description, price,
                    category, scent, size, image
             FROM products
             WHERE id = ?
             LIMIT 1"
        );

        if (!$stmt) {
            throw new RuntimeException(
                'Unable to prepare product query.'
            );
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $result = $stmt->get_result();
        $product = $result->fetch_assoc();

        $stmt->close();
        $conn->close();

    } catch (Throwable $e) {

        error_log(
            'SOPHEA product detail error: ' .
            $e->getMessage()
        );

        $databaseError = true;
    }
}

// =====================================
// PRODUCT IMAGE MAPPING
// =====================================

$productImages = [
    'current'  => 'img/store/sophea-current.png',
    'obsidian' => 'img/store/sophea-obsidian.png',
    'dune'     => 'img/store/sophea-dune.jpg',
    'verdant'  => 'img/store/sophea-verdant.jpg',
    'still'    => 'img/store/sophea-still.jpg',
    'vapor'    => 'img/store/sophea-vapor.jpg'
];

$fallbackImage = 'img/store/sophea-candles.png';

$image = $fallbackImage;

// =====================================
// PREPARE PRODUCT IMAGE
// =====================================

if ($product) {

    $productName = strtolower(
        trim($product['name'])
    );

    $productName = preg_replace(
        '/^sophea\s+/',
        '',
        $productName
    );

    if (!empty($product['image'])) {

        $image = $product['image'];

    } else {

        $image = $productImages[$productName]
            ?? $fallbackImage;
    }
}

// =====================================
// ESCAPE HTML
// =====================================

function escapeProduct($value) {

    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>
        <?= $product
            ? 'SOPHEA | ' . escapeProduct($product['name'])
            : 'SOPHEA | Product'
        ?>
    </title>

    <?php include __DIR__ . '/parts/meta.php'; ?>

</head>

<body>

    <?php include __DIR__ . '/parts/navbar.php'; ?>

    <main class="sophea-page">

        <?php if ($databaseError): ?>

            <h1 class="sophea-page-title">
                Collection Unavailable
            </h1>

            <p class="sophea-page-description">
                We could not load this product.
                Please try again later.
            </p>

        <?php elseif ($product): ?>

            <div class="sophea-product-detail">

                <!-- PRODUCT IMAGE -->

                <div class="sophea-product-gallery">

                    <img
                        src="<?= escapeProduct($image) ?>"
                        alt="<?= escapeProduct($product['name']) ?> candle"
                        class="sophea-main-product-image"
                    >

                </div>

                <!-- PRODUCT INFORMATION -->

                <div class="sophea-product-information">

                    <p class="sophea-product-description">
                        THE SOPHEA COLLECTION
                    </p>

                    <h1>
                        <?= escapeProduct($product['name']) ?>
                    </h1>

                    <p class="sophea-product-description">
                        <?= escapeProduct($product['scent']) ?>
                    </p>

                    <h2 class="sophea-product-price">
                        $<?= number_format(
                            (float) $product['price'],
                            2
                        ) ?>
                    </h2>

                    <p>
                        <?= escapeProduct($product['description']) ?>
                    </p>

                    <p class="sophea-product-size">
                        Size:
                        <?= escapeProduct($product['size']) ?>
                    </p>

                    <p class="sophea-product-description">
                        <?= escapeProduct(
                            ucfirst($product['category'])
                        ) ?>
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
                This product could not be found.
            </p>

            <a
                href="product_list.php"
                class="sophea-button"
            >
                View Collection
            </a>

        <?php endif; ?>

    </main>

</body>

</html>