<?php

// =============================================
// SOPHEA — INDIVIDUAL PRODUCT PAGE
// Product Gallery + Packaging + Add to Bag
// =============================================

require_once __DIR__ . '/php/functions.php';

// =============================================
// GET PRODUCT FROM DATABASE
// =============================================

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

$product = null;
$databaseError = false;

if ($id && $id > 0) {

    try {

        $conn = makeConn();

        $stmt = $conn->prepare(
            "SELECT
                id,
                name,
                description,
                price,
                category,
                scent,
                size,
                image
             FROM products
             WHERE id = ?
             LIMIT 1"
        );

        if (!$stmt) {
            throw new RuntimeException('Product query failed.');
        }

        $stmt->bind_param("i", $id);
        $stmt->execute();

        $product = $stmt->get_result()->fetch_assoc();

        $stmt->close();
        $conn->close();

    } catch (Throwable $e) {

        error_log('Product error: ' . $e->getMessage());

        $databaseError = true;
    }
}

// =============================================
// ESCAPE OUTPUT
// =============================================

function escapeProduct($value) {

    return htmlspecialchars(
        (string) $value,
        ENT_QUOTES,
        'UTF-8'
    );
}

// =============================================
// EXISTING SOPHEA MAIN PRODUCT IMAGES
// =============================================

$productImages = [

    'current'  => 'img/store/sophea-current.png',

    'obsidian' => 'img/store/sophea-obsidian.png',

    'dune'     => 'img/store/sophea-dune.jpg',

    'verdant'  => 'img/store/sophea-verdant.jpg',

    'still'    => 'img/store/sophea-still.jpg',

    'vapor'    => 'img/store/sophea-vapor.jpg'

];

// =============================================
// ADDITIONAL PRODUCT GALLERY IMAGES
// =============================================

// All additional images belong in:
// IXD608OL1FA26/img/store/

$productGalleryImages = [

    'obsidian' => [

        'img/store/product_obsidian .png',

        'img/store/sophea-obsidian_2.jpg',

        'img/store/sophea-obsidian_3.jpg'

    ],

    'current' => [

        'img/store/product_current.jpg'

    ],

    'verdant' => [

        'img/store/product_verdant.jpg',

        'img/store/sophea-verdant_2.jpg',

        'img/store/sophea-verdant_3.jpg'

    ],

    'vapor' => [

        'img/store/product_vapor.jpg',

        'img/store/sophea-vapor_2.jpg'

    ],

    'dune' => [

        'img/store/sophea-dune_2.png'

    ],

    'still' => [

        'img/store/Product_still .jpg'

    ]

];

// =============================================
// PREPARE PRODUCT GALLERY
// =============================================

$image = 'img/store/sophea-candles.png';

$galleryImages = [];

if ($product) {

    $productName = strtolower(
        trim((string) $product['name'])
    );

    $productName = preg_replace(
        '/^sophea\s+/',
        '',
        $productName
    );

    // Preserve SOPHEA's original main product image.

    $image = $productImages[$productName]
        ?? (
            !empty($product['image'])
                ? $product['image']
                : $image
        );

    // Main photograph is always first.

    $galleryImages[] = $image;

    // Add the new photographs for this candle.

    if (isset($productGalleryImages[$productName])) {

        foreach ($productGalleryImages[$productName] as $extraImage) {

            if (!in_array($extraImage, $galleryImages, true)) {

                $galleryImages[] = $extraImage;

            }
        }
    }
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

    <style>

        /* =====================================
           SOPHEA — PRODUCT GALLERY
        ===================================== */

        .sophea-product-gallery {
            min-width: 0;
        }

        .sophea-main-product-image {
            display: block;
            width: 100%;
            height: auto;
        }

        /* =====================================
           GALLERY THUMBNAILS
        ===================================== */

        .sophea-gallery-thumbnails {

            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 12px;

            margin-top: 16px;

            width: 100%;

        }

        .sophea-gallery-thumbnail {

            display: block;

            width: 100%;

            padding: 0;

            margin: 0;

            border: 1px solid transparent;

            border-radius: 0;

            background: transparent;

            cursor: pointer;

            overflow: hidden;

            transition:
                border-color 0.2s ease,
                opacity 0.2s ease;

        }

        .sophea-gallery-thumbnail img {

            display: block;

            width: 100%;

            aspect-ratio: 1 / 1;

            object-fit: cover;

            transition: opacity 0.2s ease;

        }

        .sophea-gallery-thumbnail:hover img {

            opacity: 0.75;

        }

        .sophea-gallery-thumbnail.is-active {

            border-color: #24211D;

        }

        .sophea-gallery-thumbnail:focus-visible {

            outline: 2px solid #78604C;

            outline-offset: 3px;

        }

        /* =====================================
           PRODUCT PURCHASE SECTION
        ===================================== */

        .sophea-product-information .sophea-product-size {

            margin-bottom: 0;

        }

        .sophea-purchase-section {

            margin-top: 26px;

        }

        .sophea-purchase-label {

            display: block;

            font-size: 14px;

            font-weight: 400;

            color: #24211D;

            margin-bottom: 12px;

            letter-spacing: 0;

            text-transform: none;

        }

        .sophea-packaging-select {

            display: block;

            width: 100%;

            max-width: 360px;

            min-height: 48px;

            padding: 12px 16px;

            border: 1px solid #C9C1B7;

            border-radius: 0;

            background-color: #F4EFE7;

            color: #24211D;

            font-family: inherit;

            font-size: 14px;

            cursor: pointer;

            box-sizing: border-box;

        }

        .sophea-packaging-select:focus {

            outline: 1px solid #78604C;

            outline-offset: 2px;

        }

        .sophea-packaging-description {

            font-size: 12px;

            color: #78604C;

            margin: 10px 0 0;

            line-height: 1.6;

        }

        .sophea-purchase-button {

            display: inline-block;

            margin-top: 24px;

            min-width: 190px;

            padding: 16px 28px;

            background: #24211D;

            color: #FFFFFF;

            border: none;

            border-radius: 0;

            font-family: inherit;

            font-size: 12px;

            letter-spacing: 1.5px;

            text-transform: uppercase;

            cursor: pointer;

        }

        .sophea-purchase-button:hover {

            background: #78604C;

        }

        .sophea-purchase-back {

            display: inline-block;

            margin-top: 28px;

        }

        .sophea-product-information .sophea-product-price {

            transition: color 0.2s ease;

        }

        /* =====================================
           RESPONSIVE GALLERY
        ===================================== */

        @media (max-width: 768px) {

            .sophea-gallery-thumbnails {

                gap: 8px;

                margin-top: 12px;

            }

            .sophea-packaging-select {

                max-width: 100%;

            }

        }

    </style>

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

            <!-- =====================================
                 PRODUCT IMAGE GALLERY
            ===================================== -->

            <div class="sophea-product-gallery">

                <!-- MAIN PRODUCT IMAGE -->

                <img
                    id="sophea-gallery-main"
                    src="<?= escapeProduct($galleryImages[0]) ?>"
                    alt="<?= escapeProduct($product['name']) ?> candle"
                    class="sophea-main-product-image"
                >

                <!-- CLICKABLE IMAGE THUMBNAILS -->

                <?php if (count($galleryImages) > 1): ?>

                    <div
                        class="sophea-gallery-thumbnails"
                        role="group"
                        aria-label="Product photographs"
                    >

                        <?php foreach ($galleryImages as $index => $galleryImage): ?>

                            <button
                                type="button"
                                class="sophea-gallery-thumbnail <?= $index === 0 ? 'is-active' : '' ?>"
                                data-gallery-image="<?= escapeProduct($galleryImage) ?>"
                                data-gallery-alt="<?= escapeProduct($product['name']) ?> candle — photograph <?= $index + 1 ?>"
                                aria-label="View photograph <?= $index + 1 ?>"
                                aria-pressed="<?= $index === 0 ? 'true' : 'false' ?>"
                            >

                                <img
                                    src="<?= escapeProduct($galleryImage) ?>"
                                    alt=""
                                    loading="lazy"
                                >

                            </button>

                        <?php endforeach; ?>

                    </div>

                <?php endif; ?>

            </div>

            <!-- =====================================
                 PRODUCT INFORMATION
            ===================================== -->

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

                <!-- DYNAMIC PRODUCT PRICE -->

                <h2
                    class="sophea-product-price"
                    id="sophea-product-price"
                >
                    $<?= number_format((float) $product['price'], 2) ?>
                </h2>

                <p>
                    <?= escapeProduct($product['description']) ?>
                </p>

                <p class="sophea-product-size">
                    Size:
                    <?= escapeProduct($product['size']) ?>
                </p>

                <!-- =====================================
                     PRODUCT PURCHASE OPTIONS
                ===================================== -->

                <form
                    action="cart.php"
                    method="POST"
                    class="sophea-purchase-section"
                >

                    <input
                        type="hidden"
                        name="action"
                        value="add"
                    >

                    <input
                        type="hidden"
                        name="id"
                        value="<?= (int) $product['id'] ?>"
                    >

                    <label
                        for="packaging"
                        class="sophea-purchase-label"
                    >
                        Packaging
                    </label>

                    <select
                        id="packaging"
                        name="packaging"
                        class="sophea-packaging-select"
                        data-base-price="<?= escapeProduct((float) $product['price']) ?>"
                        required
                    >

                        <option value="standard">
                            Standard Packaging — Included
                        </option>

                        <option value="gift">
                            Gift Packaging — +$5.00
                        </option>

                    </select>

                    <p
                        class="sophea-packaging-description"
                        id="sophea-packaging-description"
                        aria-live="polite"
                    >
                        Signature SOPHEA packaging included.
                    </p>

                    <button
                        type="submit"
                        class="sophea-purchase-button"
                    >
                        ADD TO BAG
                    </button>

                </form>

                <a
                    href="product_list.php"
                    class="sophea-back-link sophea-purchase-back"
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

<script>

document.addEventListener('DOMContentLoaded', function () {

    // =========================================
    // PRODUCT IMAGE GALLERY
    // =========================================

    const mainImage = document.getElementById(
        'sophea-gallery-main'
    );

    const thumbnails = document