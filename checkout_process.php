<?php
session_start();

require_once __DIR__ . '/php/functions.php';
require_once __DIR__ . '/php/cart_helpers.php';

// Only accept checkout form submissions.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: checkout.php');
    exit;
}

// Validate the checkout security token.
$submittedToken = $_POST['csrf'] ?? '';
$sessionToken = $_SESSION['checkout_csrf'] ?? '';

if (
    !is_string($submittedToken) ||
    !is_string($sessionToken) ||
    $sessionToken === '' ||
    !hash_equals($sessionToken, $submittedToken)
) {
    http_response_code(403);
    exit('Invalid checkout request. Please return to checkout.');
}

// Normalize the cart before processing.
sopheaNormalizeCart();

// Make sure the cart is not empty.
if (empty($_SESSION['cart'])) {
    header('Location: cart.php');
    exit;
}

// Validate required customer information.
$requiredFields = [
    'customer_name',
    'customer_email',
    'address',
    'city',
    'state',
    'zip'
];

foreach ($requiredFields as $field) {

    $value = $_POST[$field] ?? '';

    if (!is_string($value) || trim($value) === '') {
        header('Location: checkout.php');
        exit;
    }
}

// Validate email address.
$email = filter_var(
    $_POST['customer_email'],
    FILTER_VALIDATE_EMAIL
);

if (!$email) {
    header('Location: checkout.php');
    exit;
}

// Verify cart products against the database.
try {

    [$cartProducts, $subtotal] = sopheaCartProducts();

    if (empty($cartProducts)) {
        header('Location: cart.php');
        exit;
    }

    // Do not complete an order if a cart item
    // no longer exists in the database.
    if (count($cartProducts) !== count($_SESSION['cart'])) {
        header('Location: cart.php');
        exit;
    }

} catch (Throwable $e) {

    error_log(
        'SOPHEA checkout processing error: ' .
        $e->getMessage()
    );

    http_response_code(500);

    exit('Unable to process the demonstration order.');
}

// Store a minimal confirmation summary.
// Do not store shipping details or payment information.
$_SESSION['demo_order'] = [
    'items' => $cartProducts,
    'subtotal' => $subtotal,
    'completed_at' => date('Y-m-d H:i:s')
];

// Mark the demonstration checkout as complete.
$_SESSION['demo_order_complete'] = true;

// Clear the cart only after successful validation.
$_SESSION['cart'] = [];

// Remove the used checkout security token.
unset($_SESSION['checkout_csrf']);

// Redirect to the confirmation page.
header('Location: product_confirmation.php');
exit;