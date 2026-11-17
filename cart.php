<?php
session_start();

require_once __DIR__ . '/php/functions.php';
require_once __DIR__ . '/php/cart_helpers.php';

sopheaNormalizeCart();

// Handle shopping cart actions.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    if ($action === 'add') {

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $packaging = $_POST['packaging'] ?? 'standard';

        if (!in_array($packaging, ['standard', 'gift'], true)) {
            $packaging = 'standard';
        }

        if ($id && $id > 0) {

            try {
                $conn = makeConn();

                try {
                    $stmt = $conn->prepare(
                        'SELECT id FROM products WHERE id = ? LIMIT 1'
                    );

                    if (!$stmt) {
                        throw new RuntimeException('Product lookup failed');
                    }

                    $stmt->bind_param('i', $id);
                    $stmt->execute();

                    $exists = $stmt->get_result()->num_rows > 0;

                    $stmt->close();

                    if ($exists) {
                        $key = sopheaCartKey($id, $packaging);

                        $_SESSION['cart'][$key] = min(
                            99,
                            ($_SESSION['cart'][$key] ?? 0) + 1
                        );
                    }

                } finally {
                    $conn->close();
                }

            } catch (Throwable $e) {
                error_log('Cart add error: ' . $e->getMessage());
            }
        }

    } elseif ($action === 'update' || $action === 'remove') {

        $key = $_POST['key'] ?? '';

        if (
            is_string($key) &&
            preg_match('/^[1-9][0-9]*:(standard|gift)$/', $key) &&
            isset($_SESSION['cart'][$key])
        ) {

            if ($action === 'remove') {

                unset($_SESSION['cart'][$key]);

            } else {

                $quantity = filter_input(
                    INPUT_POST,
                    'quantity',
                    FILTER_VALIDATE_INT
                );

                if ($quantity !== false && $quantity !== null) {

                    if ($quantity <= 0) {
                        unset($_SESSION['cart'][$key]);
                    } else {
                        $_SESSION['cart'][$key] = min(99, $quantity);
                    }
                }
            }
        }

    } elseif ($action === 'clear') {

        $_SESSION['cart'] = [];
    }

    header('Location: cart.php');
    exit;
}

// Load cart products.
$cartProducts = [];
$subtotal = 0.0;
$databaseError = false;

try {
    [$cartProducts, $subtotal] = sopheaCartProducts();
} catch (Throwable $e) {
    error_log('Cart load error: ' . $e->getMessage());
    $databaseError = true;
}

function cartEscape($value) {
    return sopheaEscape($value);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>SOPHEA | Shopping Bag</title>

    <?php include __DIR__ . '/parts/meta.php'; ?>

    <style>
        .sophea-cart {
            max-width: 1100px;
            margin: 0 auto;
            padding: 80px 30px 120px;
        }

        .sophea-cart-title {
            font-family: Georgia, serif;
            font-size: 48px;
            font-weight: 400;
            text-align: center;
            margin-bottom: 15px;
        }

        .sophea-cart-subtitle {
            text-align: center;
            color: #77736F;
            font-size: 13px;
            letter-spacing: 2px;
            margin-bottom: 70px;
        }

        .sophea-cart-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 60px;
        }

        .sophea-cart-item {
            display: flex;
            gap: 25px;
            padding: 30px 0;
            border-bottom: 1px solid #DEDAD4;
        }

        .sophea-cart-item:first-child {
            border-top: 1px solid #DEDAD4;
        }

        .sophea-cart-image {
            width: 120px;
            height: 150px;
            object-fit: cover;
            background: #E8E2D9;
        }

        .sophea-cart-info {
            flex: 1;
        }

        .sophea-cart-info h2 {
            font-family: Georgia, serif;
            font-size: 23px;
            font-weight: 400;
            margin: 0 0 8px;
        }

        .sophea-cart-scent {
            font-size: 13px;
            color: #77736F;
        }

        .sophea-cart-price {
            font-size: 15px;
            margin: 15px 0;
        }

        .sophea-cart-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 15px;
        }

        .sophea-cart-quantity input {
            width: 55px;
            padding: 8px;
            border: 1px solid #DEDAD4;
            background: transparent;
        }

        .sophea-cart-small-button {
            border: 1px solid #24211D;
            background: transparent;
            padding: 9px 13px;
            cursor: pointer;
            font-size: 11px;
        }

        .sophea-cart-remove {
            border: none;
            background: none;
            padding: 15px 0;
            color: #77736F;
            text-decoration: underline;
            cursor: pointer;
            font-size: 12px;
        }

        .sophea-cart-summary {
            background: #E8E2D9;
            padding: 35px;
            align-self: start;
        }

        .sophea-cart-summary h2 {
            font-family: Georgia, serif;
            font-weight: 400;
            font-size: 27px;
            margin-top: 0;
        }

        .sophea-cart-total {
            display: flex;
            justify-content: space-between;
            border-top: 1px solid #C9C1B7;
            padding-top: 25px;
            margin-top: 30px;
            font-size: 17px;
        }

        .sophea-cart-note {
            color: #77736F;
            font-size: 12px;
            margin: 25px 0;
        }

        .sophea-cart-checkout {
            display: block;
            width: 100%;
            box-sizing: border-box;
            padding: 17px;
            background: #24211D;
            color: white;
            text-align: center;
            text-decoration: none;
            letter-spacing: 2px;
            font-size: 11px;
        }

        .sophea-cart-empty {
            text-align: center;
            padding: 60px 20px;
        }

        .sophea-cart-empty p {
            margin-bottom: 35px;
            color: #77736F;
        }

        @media (max-width: 768px) {
            .sophea-cart-layout {
                grid-template-columns: 1fr;
                gap: 35px;
            }

            .sophea-cart-title {
                font-size: 38px;
            }

            .sophea-cart-item {
                gap: 15px;
            }

            .sophea-cart-image {
                width: 90px;
                height: 115px;
            }
        }
    </style>
</head>

<body>

<?php include __DIR__ . '/parts/navbar.php'; ?>

<main class="sophea-cart">

    <h1 class="sophea-cart-title">Your Shopping Bag</h1>

    <p class="sophea-cart-subtitle">
        SELECTED WITH INTENTION
    </p>

    <?php if ($databaseError): ?>

        <div class="sophea-cart-empty">
            <p>We couldn't load your bag. Please try again.</p>
        </div>

    <?php elseif (empty($cartProducts)): ?>

        <div class="sophea-cart-empty">

            <h2>Your bag is currently empty.</h2>

            <p>Discover the SOPHEA collection.</p>

            <a href="product_list.php" class="sophea-button">
                EXPLORE COLLECTION
            </a>

        </div>

    <?php else: ?>

        <div class="sophea-cart-layout">

            <section>

                <?php foreach ($cartProducts as $product): ?>

                    <?php
                        $name = strtolower(trim($product['name']));

                        $images = [
                            'obsidian' => 'img/store/sophea-obsidian.png',
                            'dune' => 'img/store/sophea-dune.jpg',
                            'current' => 'img/store/sophea-current.png',
                            'verdant' => 'img/store/sophea-verdant.jpg',
                            'still' => 'img/store/sophea-still.jpg',
                            'vapor' => 'img/store/sophea-vapor.jpg'
                        ];

                        $image = !empty($product['image'])
                            ? $product['image']
                            : ($images[$name] ?? 'img/store/sophea-candles.png');
                    ?>

                    <article class="sophea-cart-item">

                        <a href="product_item.php?id=<?= (int) $product['id'] ?>">
                            <img
                                class="sophea-cart-image"
                                src="<?= cartEscape($image) ?>"
                                alt="<?= cartEscape($product['name']) ?>"
                            >
                        </a>

                        <div class="sophea-cart-info">

                            <h2>
                                <?= cartEscape($product['name']) ?>
                            </h2>

                            <p class="sophea-cart-scent">
                                <?= cartEscape($product['scent']) ?>
                            </p>

                            <p class="sophea-cart-scent">
                                Packaging:
                                <?= $product['packaging'] === 'gift'
                                    ? 'Gift Packaging'
                                    : 'Standard Packaging' ?>
                            </p>

                            <p class="sophea-cart-price">
                                $<?= number_format((float) $product['price'], 2) ?>
                            </p>

                            <form method="POST" class="sophea-cart-quantity">

                                <input type="hidden" name="action" value="update">

                                <input
                                    type="hidden"
                                    name="key"
                                    value="<?= cartEscape($product['key']) ?>"
                                >

                                <label for="quantity-<?= cartEscape(str_replace(':', '-', $product['key'])) ?>">
                                    Qty
                                </label>

                                <input
                                    id="quantity-<?= cartEscape(str_replace(':', '-', $product['key'])) ?>"
                                    type="number"
                                    name="quantity"
                                    min="0"
                                    max="99"
                                    value="<?= (int) $product['quantity'] ?>"
                                >

                                <button type="submit" class="sophea-cart-small-button">
                                    UPDATE
                                </button>

                            </form>

                            <form method="POST">

                                <input type="hidden" name="action" value="remove">

                                <input
                                    type="hidden"
                                    name="key"
                                    value="<?= cartEscape($product['key']) ?>"
                                >

                                <button type="submit" class="sophea-cart-remove">
                                    Remove
                                </button>

                            </form>

                        </div>

                        <strong>
                            $<?= number_format($product['line_total'], 2) ?>
                        </strong>

                    </article>

                <?php endforeach; ?>

            </section>

            <aside class="sophea-cart-summary">

                <h2>Order Summary</h2>

                <div class="sophea-cart-total">

                    <span>Subtotal</span>

                    <strong>
                        $<?= number_format($subtotal, 2) ?>
                    </strong>

                </div>

                <p class="sophea-cart-note">
                    Shipping and taxes are not included.
                </p>

                <a href="checkout.php" class="sophea-cart-checkout">
                    PROCEED TO CHECKOUT
                </a>

                <p class="sophea-cart-note">
                    This is a demonstration shopping cart.
                    No payments are collected.
                </p>

                <a href="product_list.php" class="sophea-back-link">
                    ← CONTINUE SHOPPING
                </a>

            </aside>

        </div>

    <?php endif; ?>

</main>

</body>
</html>