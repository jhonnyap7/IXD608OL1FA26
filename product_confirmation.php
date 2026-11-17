<?php
session_start();

// Only allow access after a completed demonstration checkout.
if (
    empty($_SESSION['demo_order_complete']) ||
    empty($_SESSION['demo_order'])
) {
    header('Location: product_list.php');
    exit;
}

// Retrieve the completed order.
$order = $_SESSION['demo_order'];

// Clear the confirmation session data so refreshing
// or revisiting this page cannot reuse the confirmation.
unset(
    $_SESSION['demo_order_complete'],
    $_SESSION['demo_order']
);

function confirmationEscape($value): string {
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

    <title>SOPHEA | Order Confirmation</title>

    <?php include __DIR__ . '/parts/meta.php'; ?>

    <style>
        .sophea-confirmation {
            max-width: 750px;
            margin: 0 auto;
            padding: 100px 30px 140px;
            text-align: center;
        }

        .sophea-confirmation-label {
            font-size: 11px;
            letter-spacing: 3px;
            color: #78604C;
            text-transform: uppercase;
            margin-bottom: 25px;
        }

        .sophea-confirmation h1 {
            font-family: Georgia, "Times New Roman", serif;
            font-size: clamp(40px, 6vw, 64px);
            font-weight: 400;
            color: #24211D;
            margin-bottom: 25px;
        }

        .sophea-confirmation-message {
            max-width: 500px;
            margin: 0 auto 40px;
            font-size: 15px;
            line-height: 1.9;
            color: #77736F;
        }

        .sophea-confirmation-summary {
            max-width: 550px;
            margin: 45px auto;
            padding: 35px;
            background: #E8E2D9;
            text-align: left;
        }

        .sophea-confirmation-summary h2 {
            font-family: Georgia, serif;
            font-size: 25px;
            font-weight: 400;
            margin-top: 0;
            margin-bottom: 25px;
        }

        .sophea-confirmation-row {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 15px 0;
            border-bottom: 1px solid #C9C1B7;
            font-size: 14px;
        }

        .sophea-confirmation-option {
            display: block;
            color: #77736F;
            font-size: 12px;
            margin-top: 6px;
        }

        .sophea-confirmation-total {
            font-weight: 600;
            font-size: 17px;
            margin-top: 15px;
        }

        .sophea-confirmation-button {
            display: inline-block;
            padding: 17px 38px;
            background-color: #24211D;
            color: #FFFFFF;
            text-decoration: none;
            font-size: 11px;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-top: 15px;
        }

        .sophea-confirmation-button:hover {
            background-color: #78604C;
            color: #FFFFFF;
            text-decoration: none;
        }

        .sophea-confirmation-note {
            margin-top: 35px;
            font-size: 12px;
            color: #77736F;
            line-height: 1.8;
        }

        @media (max-width: 768px) {
            .sophea-confirmation {
                padding: 70px 20px 100px;
            }

            .sophea-confirmation-summary {
                padding: 25px;
            }
        }
    </style>

</head>

<body>

<?php include __DIR__ . '/parts/navbar.php'; ?>

<main class="sophea-confirmation">

    <p class="sophea-confirmation-label">
        SOPHEA  SPATIAL FRAGRANCE
    </p>

    <h1>Thank You</h1>

    <p class="sophea-confirmation-message">
        Your demonstration order has been completed successfully.

        <br><br>

        Thank you for exploring Sophea.
        Each fragrance is designed to bring intention,
        warmth, and atmosphere into everyday spaces.
    </p>

    <!-- COMPLETED ORDER SUMMARY -->

    <section class="sophea-confirmation-summary">

        <h2>Your Order Summary</h2>

        <?php foreach ($order['items'] as $product): ?>

            <div class="sophea-confirmation-row">

                <div>

                    <strong>
                        <?= confirmationEscape($product['name']) ?>
                    </strong>

                    <span class="sophea-confirmation-option">
                        <?= $product['packaging'] === 'gift'
                            ? 'Gift Packaging'
                            : 'Standard Packaging' ?>
                    </span>

                    <span class="sophea-confirmation-option">
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

        <div class="sophea-confirmation-row sophea-confirmation-total">

            <span>Subtotal</span>

            <strong>
                $<?= number_format(
                    (float) $order['subtotal'],
                    2
                ) ?>
            </strong>

        </div>

    </section>

    <a
        href="product_list.php"
        class="sophea-confirmation-button"
    >
        CONTINUE SHOPPING
    </a>

    <p class="sophea-confirmation-note">
        This was a demonstration checkout for an academic project.
        No payment was collected, and no real order was placed.
    </p>

</main>

</body>
</html>