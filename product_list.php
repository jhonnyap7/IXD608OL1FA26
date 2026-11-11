<?php

// =====================================
// SOPHEA — PRODUCT COLLECTION
// =====================================

require_once __DIR__ . '/php/functions.php';
require_once __DIR__ . '/parts/templates.php';

// =====================================
// RETRIEVE PRODUCTS FROM MYSQL
// =====================================

$products = [];
$databaseError = false;

try {

    $conn = makeConn();

    $result = makeQuery(
        $conn,
        "SELECT id, name, description, price,
                category, scent, size, image
         FROM products
         ORDER BY id ASC"
    );

    $products = $result->fetch_all(MYSQLI_ASSOC);

    $conn->close();

} catch (Throwable $e) {

    // Do not expose database information to visitors.
    error_log('SOPHEA product query failed: ' . $e->getMessage());

    $databaseError = true;
}

// =====================================
// SOPHEA PRODUCT IMAGE MAPPING
// =====================================

// These paths are relative to IXD608OL1FA26/.

$productImages = [
    'current'  => 'img/store/sophea-current.png',
    'obsidian' => 'img/store/sophea-obsidian.png',
    'dune'     => 'img/store/sophea-dune.jpg',
    'verdant'  => 'img/store/sophea-verdant.jpg',
    'still'    => 'img/store/sophea-still.jpg',
    'vapor'    => 'img/store/sophea-vapor.jpg'
];

// Image used when a product has no matching photograph.
$fallbackImage = 'img/store/sophea-candles.png';

// =====================================
// PREPARE PRODUCT DATA
// =====================================

foreach ($products as &$product) {

    // Normalize the product name for image matching.
    $productName = strtolower(
        trim($product['name'])
    );

    // Remove an optional SOPHEA prefix.
    $productName = preg_replace(
        '/^sophea\s+/',
        '',
        $productName
    );

    // Use the database image when available.
    if (empty($product['image'])) {

        // Otherwise, use the mapped image or fallback.
        $product['image'] =
            $productImages[$productName]
            ?? $fallbackImage;
    }
}

unset($product);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>SOPHEA | The Collection</title>

    <?php include __DIR__ . '/parts/meta.php'; ?>

</head>

<body>

    <?php include __DIR__ . '/parts/navbar.php'; ?>

    <main class="sophea-page">

        <h1 class="sophea-page-title">
            The Collection
        </h1>

        <p class="sophea-page-description">
            Thoughtfully crafted fragrances for moments of stillness.
        </p>

        <?php if ($databaseError): ?>

            <p class="sophea-page-description">
                Our collection is temporarily unavailable.
                Please try again later.
            </p>

        <?php elseif (empty($products)): ?>

            <p class="sophea-page-description">
                No products are currently available.
            </p>

        <?php else: ?>

            <div class="sophea-product-grid">

                <?php foreach ($products as $product): ?>

                    <?php
                    // The reusable template expects an object.
                    $item = (object) $product;

                    echo productListTemplate($item);
                    ?>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </main>

</body>

</html>