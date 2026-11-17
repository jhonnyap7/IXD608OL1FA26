<?php

// =====================================
// SOPHEA — CART HELPERS
// =====================================

// Gift packaging costs an additional $5.
const SOPHEA_GIFT_PACKAGING_PRICE = 5.00;

// Create a unique key for each product and packaging option.
function sopheaCartKey(int $id, string $packaging): string {
    return $id . ':' . $packaging;
}

// Calculate the correct unit price.
function sopheaProductPrice(float $basePrice, string $packaging): float {

    if ($packaging === 'gift') {
        return round($basePrice + SOPHEA_GIFT_PACKAGING_PRICE, 2);
    }

    return round($basePrice, 2);
}

// Convert older cart entries to the current structure.
function sopheaNormalizeCart(): void {

    if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $normalized = [];

    foreach ($_SESSION['cart'] as $key => $quantity) {

        if (!is_numeric($quantity) || (int)$quantity <= 0) {
            continue;
        }

        $parts = explode(':', (string)$key, 2);

        $id = filter_var($parts[0], FILTER_VALIDATE_INT);

        if (!$id || $id < 1) {
            continue;
        }

        $packaging = $parts[1]
            ?? ($_SESSION['cart_packaging'][$id] ?? 'standard');

        if (!in_array($packaging, ['standard', 'gift'], true)) {
            $packaging = 'standard';
        }

        $lineKey = sopheaCartKey($id, $packaging);

        $normalized[$lineKey] = min(
            99,
            ($normalized[$lineKey] ?? 0) + (int)$quantity
        );
    }

    $_SESSION['cart'] = $normalized;

    unset($_SESSION['cart_packaging']);
}

// Load cart products from the database.
function sopheaCartProducts(): array {

    $items = [];
    $subtotal = 0.0;

    if (empty($_SESSION['cart'])) {
        return [$items, $subtotal];
    }

    $ids = array_values(array_unique(array_map(
        static fn($key) => (int)explode(':', (string)$key, 2)[0],
        array_keys($_SESSION['cart'])
    )));

    $conn = makeConn();

    try {

        $placeholders = implode(
            ',',
            array_fill(0, count($ids), '?')
        );

        $stmt = $conn->prepare(
            "SELECT id, name, price, scent, image
             FROM products
             WHERE id IN ($placeholders)"
        );

        if (!$stmt) {
            throw new RuntimeException('Cannot load cart products');
        }

        try {

            $types = str_repeat('i', count($ids));

            $stmt->bind_param($types, ...$ids);
            $stmt->execute();

            $result = $stmt->get_result();

            $byId = [];

            while ($row = $result->fetch_assoc()) {
                $byId[(int)$row['id']] = $row;
            }

        } finally {
            $stmt->close();
        }

    } finally {
        $conn->close();
    }

    foreach ($_SESSION['cart'] as $key => $quantity) {

        [$id, $packaging] = explode(':', (string)$key, 2);

        if (!isset($byId[(int)$id])) {
            continue;
        }

        $item = $byId[(int)$id];

        $item['key'] = (string)$key;
        $item['packaging'] = $packaging;
        $item['quantity'] = (int)$quantity;

        // Preserve the original database price.
        $item['base_price'] = (float)$item['price'];

        // Apply the packaging price adjustment.
        $item['price'] = sopheaProductPrice(
            $item['base_price'],
            $packaging
        );

        // Calculate total for this cart item.
        $item['line_total'] = round(
            $item['price'] * $item['quantity'],
            2
        );

        $subtotal += $item['line_total'];

        $items[] = $item;
    }

    return [$items, round($subtotal, 2)];
}

// Escape values before displaying them in HTML.
function sopheaEscape($value): string {

    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}