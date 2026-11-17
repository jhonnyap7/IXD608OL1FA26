<?php
session_start();

require_once __DIR__ . '/php/functions.php';
require_once __DIR__ . '/php/cart_helpers.php';

// Convert older cart entries to the updated format.
sopheaNormalizeCart();

// Redirect if the shopping bag is empty.
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Load products and calculate the subtotal.
$cartProducts = [];
$subtotal = 0.0;
$databaseError = false;

try {
    [$cartProducts, $subtotal] = sopheaCartProducts();
} catch (Throwable $e) {
    error_log('Checkout error: ' . $e->getMessage());
    $databaseError = true;
}

// Generate a security token for checkout.
if (empty($_SESSION['checkout_csrf'])) {
    $_SESSION['checkout_csrf'] = bin2hex(random_bytes(32));
}

function checkoutEscape($value) {
    return sopheaEscape($value);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">

    <title>SOPHEA | Checkout</title>

    <?php include __DIR__ . '/parts/meta.php'; ?>

    <style>
        .sophea-checkout {
            max-width: 1000px;
            margin: 0 auto;
            padding: 80px 30px 120px;
        }

        .sophea-checkout h1 {
            font-family: Georgia, serif;
            font-size: 48px;
            font-weight: 400;
            text-align: center;
            color: #24211D;
        }

        .sophea-checkout-subtitle {
            text-align: center;
            color: #77736F;
            font-size: 13px;
            letter-spacing: 2px;
            margin-bottom: 65px;
        }

        .sophea-checkout-layout {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 60px;
        }

        .sophea-checkout h2 {
            font-family: Georgia, serif;
            font-size: 26px;
            font-weight: 400;
            margin-bottom: 30px;
        }

        .sophea-checkout label {
            display: block;
            font-size: 11px;
            letter-spacing: 1.5px;
            margin-bottom: 8px;
            color: #24211D;
        }

        .sophea-checkout input:not([type="hidden"]) {
            width: 100%;
            padding: 14px;
            margin-bottom: 25px;
            border: 1px solid #DEDAD4;
            background: #FFFFFF;
            box-sizing: border-box;
            font-size: 14px;
        }

        .sophea-checkout-summary {
            background: #E8E2D9;
            padding: 35px;
            align-self: start;
        }

        .sophea-checkout-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 18px 0;
            border-bottom: 1px solid #C9C1B7;
            font-size: 14px;
        }

        .sophea-checkout-product-name {
            font-weight: 500;
            color: #24211D;
        }

        .sophea-checkout-product-option {
            display: block;
            font-size: 12px;
            color: #77736F;
            margin-top: 6px;
        }

        .sophea-checkout-total {
            font-size: 18px;
            font-weight: 600;
            margin-top: 15px;
        }

        .sophea-checkout-button {
            display: block;
            width: 100%;
            padding: 18px;
            background: #24211D;
            color: #FFFFFF;
            border: none;
            cursor: pointer;
            letter-spacing: 2px;
            font-size: 11px;
            margin-top: 30px;
        }

        .sophea-checkout-button:hover {
            background: #78604C;
        }

        .sophea-checkout-note {
            color: #77736F;
            font-size: 12px;
            line-height: 1.7;
            margin-top: 25px;
        }

        .sophea-checkout-back {
            display: inline-block;
            margin-top: 30px;
            color: #24211D;
            text-decoration: none;
            font-size: 12px;
        }

        @media (max-width: 768px) {
            .sophea-checkout-layout {
                grid-template-columns: 1fr;
                gap: 35px;
            }

            .sophea-checkout h1 {
                font-size: 38px;
            }
        }
    </style>

</head>

<body>

<?php include __DIR__ . '/parts/navbar.php'; ?>

<main class="sophea-checkout">

    <h1>Checkout</h1>

    <p class="sophea-checkout-subtitle">
        COMPLETE YOUR SOPHEA EXPERIENCE
    </p>

    <?php if ($databaseError || empty($cartProducts)): ?>

        <p>
            We couldn't load your order.
            Please return to your shopping bag.
        </p>

        <a href="cart.php" class="sophea-checkout-back">
            ← RETURN TO SHOPPING BAG
        </a>

    <?php else: ?>

        <div class="sophea-checkout-layout">

            <!-- CUSTOMER INFORMATION -->

            <section>

                <form
                    action="checkout_process.php"
                    method="POST"
                >

                    <input
                        type="hidden"
                        name="csrf"
                        value="<?= checkoutEscape($_SESSION['checkout_csrf']) ?>"
                    >

                    <h2>Contact Information</h2>

                    <label for="customer_name">
                        FULL NAME
                    </label>

                    <input
                        type="text"
                        id="customer_name"
                        name="customer_name"
                        autocomplete="name"
                        required
                    >

                    <label for="customer_email">
                        EMAIL ADDRESS
                    </label>

                    <input
                        type="email"
                        id="customer_email"
                        name="customer_email"
                        autocomplete="email"
                        required
                    >

                    <h2>Shipping Information</h2>

                    <label for="address">
                        SHIPPING ADDRESS
                    </label>

                    <input
                        type="text"
                        id="address"
                        name="address"
                        autocomplete="street-address"
                        required
                    >

                    <label for="city">
                        CITY
                    </label>

                    <input
                        type="text"
                        id="city"
                        name="city"
                        autocomplete="address-level2"
                        required
                    >

                    <label for="state">
                        STATE
                    </label>

                    <input
                        type="text"
                        id="state"
                        name="state"
                        autocomplete="address-level1"
                        required
                    >

                    <label for="zip">
                        ZIP CODE
                    </label>

                    <input
                        type="text"
                        id="zip"
                        name="zip"
                        autocomplete="postal-code"
                        required
                    >

                    <button
                        type="submit"
                        class="sophea-checkout-button"
                    >
                        PLACE DEMO ORDER
                    </button>

                    <p class="sophea-checkout-note">
                        This is a demonstration checkout
                        created for an academic project.
                        No payment will be collected,
                        and no real order will be placed.
                    </p>

                </form>

                <a
                    href="cart.php"
                    class="sophea-checkout-back"
                >
                    ← RETURN TO SHOPPING BAG
                </a>

            </section>

            <!-- ORDER SUMMARY -->

            <aside class="sophea-checkout-summary">

                <h2>Order Summary</h2>

                <?php foreach ($cartProducts as $product): ?>

                    <div class="sophea-checkout-row">

                        <div>

                            <span class="sophea-checkout-product-name">
                                <?= checkoutEscape($product['name']) ?>
                            </span>

                            <span class="sophea-checkout-product-option">

                                <?= $product['packaging'] === 'gift'
                                    ? 'Gift Packaging'
                                    : 'Standard Packaging' ?>

                            </span>

                            <span class="sophea-checkout-product-option">
                                Quantity:
                                <?= (int) $product['quantity'] ?>
                            </span>

                        </div>

                        <strong>
                            $<?= number_format(
                                (float) $product['line_total'],
                                2
                            ) ?>
                        </strong>

                    </div>

                <?php endforeach; ?>

                <div class="sophea-checkout-row sophea-checkout-total">

                    <span>Subtotal</span>

                    <strong>
                        $<?= number_format($subtotal, 2) ?>
                    </strong>

                </div>

                <p class="sophea-checkout-note">
                    Shipping and taxes are not calculated
                    in this demonstration.
                </p>

            </aside>

        </div>

    <?php endif; ?>

</main>

</body>
</html>