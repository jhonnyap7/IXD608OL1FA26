<?php
require_once __DIR__ . '/php/functions.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
$product = null;
$databaseError = false;

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

$productImages = [
    'current'  => 'img/store/sophea-current.png',
    'obsidian' => 'img/store/sophea-obsidian.png',
    'dune'     => 'img/store/sophea-dune.jpg',
    'verdant'  => 'img/store/sophea-verdant.jpg',
    'still'    => 'img/store/sophea-still.jpg',
    'vapor'    => 'img/store/sophea-vapor.jpg'
];

$image = 'img/store/sophea-candles.png';

if ($product) {
    $productName = strtolower(trim($product['name']));
    $productName = preg_replace('/^sophea\s+/', '', $productName);

    $image = !empty($product['image'])
        ? $product['image']
        : ($productImages[$productName] ?? $image);
}

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

    <style>
        /* SOPHEA product purchase section */

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

        @media (max-width: 768px) {
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

            <div class="sophea-product-gallery">

                <img
                    src="<?= escapeProduct($image) ?>"
                    alt="<?= escapeProduct($product['name']) ?> candle"
                    class="sophea-main-product-image"
                >

            </div>

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

                <!-- PRODUCT PURCHASE OPTIONS -->

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
                            Standard Packaging  Included
                        </option>

                        <option value="gift">
                            Gift Packaging  +$5.00
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

        <a href="product_list.php" class="sophea-button">
            View Collection
        </a>

    <?php endif; ?>

</main>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const packaging = document.getElementById('packaging');
    const price = document.getElementById('sophea-product-price');
    const description = document.getElementById(
        'sophea-packaging-description'
    );

    if (!packaging || !price || !description) {
        return;
    }

    const basePrice = Number(packaging.dataset.basePrice);
    const giftPackagingPrice = 5.00;

    if (!Number.isFinite(basePrice)) {
        return;
    }

    function updatePackagingPrice() {

        const isGift = packaging.value === 'gift';

        const totalPrice = basePrice +
            (isGift ? giftPackagingPrice : 0);

        price.textContent = '$' + totalPrice.toFixed(2);

        description.textContent = isGift
            ? 'Gift packaging adds $5.00 to your order.'
            : 'Signature SOPHEA packaging included.';
    }

    packaging.addEventListener(
        'change',
        updatePackagingPrice
    );

    updatePackagingPrice();

});
</script>

</body>
</html>