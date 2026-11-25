<?php

// =============================================
// SOPHEA — PRODUCT API
// IXD 608 — Back-End Development
// =============================================

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

// =============================================
// DATABASE CONNECTION
// =============================================

require_once __DIR__ . '/../php/functions.php';

// =============================================
// HELPER: SEND JSON RESPONSE
// =============================================

function sendResponse($data, $status = 200) {

    http_response_code($status);

    echo json_encode(
        $data,
        JSON_UNESCAPED_UNICODE |
        JSON_INVALID_UTF8_SUBSTITUTE
    );

    exit;
}

// =============================================
// REQUEST PARAMETERS
// =============================================

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {

    header('Allow: GET');

    sendResponse([
        'success' => false,
        'message' => 'Only GET requests are supported.'
    ], 405);
}

$type = $_GET['type'] ?? 'products_all';

$allowedTypes = [
    'products_all',
    'product_search',
    'product_filter',
    'product_sort'
];

if (
    !is_string($type) ||
    !in_array($type, $allowedTypes, true)
) {

    sendResponse([
        'success' => false,
        'message' => 'Invalid product operation.'
    ], 400);
}

// =============================================
// SEARCH PARAMETERS
// =============================================

$search = $_GET['search'] ?? '';

if (!is_string($search)) {
    $search = '';
}

$search = trim($search);
$search = substr($search, 0, 100);

// =============================================
// FILTER PARAMETERS
// =============================================

$category = $_GET['category'] ?? 'all';

if (!is_string($category)) {
    $category = 'all';
}

$category = trim($category);

// =============================================
// SORT PARAMETERS
// =============================================

$sort = $_GET['sort'] ?? 'featured';

if (!is_string($sort)) {
    $sort = 'featured';
}

// Whitelist sorting expressions.
// Never insert unvalidated user input into ORDER BY.

$sortOptions = [

    'featured' => 'id ASC',

    'newest' => 'id DESC',

    'oldest' => 'id ASC',

    'name_asc' => 'name ASC, id ASC',

    'name_desc' => 'name DESC, id ASC',

    'price_asc' => 'price ASC, id ASC',

    'price_desc' => 'price DESC, id ASC'

];

if (!isset($sortOptions[$sort])) {
    $sort = 'featured';
}

// =============================================
// DATABASE QUERY
// =============================================

$conn = null;
$stmt = null;

try {

    $conn = makeConn();

    // =========================================
    // BUILD SEARCH AND FILTER CONDITIONS
    // =========================================

    $conditions = [];
    $parameters = [];
    $types = '';

    // =========================================
    // SEARCH
    // =========================================
    // Search product names, descriptions,
    // categories, and fragrance notes.
    //
    // Search must work with filtering and sorting,
    // regardless of the selected API operation.

    if ($search !== '') {

        $conditions[] = "
            (
                name LIKE ?
                OR description LIKE ?
                OR category LIKE ?
                OR scent LIKE ?
            )
        ";

        $searchTerm = '%' . $search . '%';

        $parameters[] = $searchTerm;
        $parameters[] = $searchTerm;
        $parameters[] = $searchTerm;
        $parameters[] = $searchTerm;

        $types .= 'ssss';
    }

    // =========================================
    // CATEGORY FILTER
    // =========================================

    if (
        $category !== '' &&
        $category !== 'all'
    ) {

        $conditions[] = 'category = ?';

        $parameters[] = $category;

        $types .= 's';
    }

    // =========================================
    // BUILD SQL QUERY
    // =========================================

    $sql = "
        SELECT
            id,
            name,
            description,
            price,
            category,
            scent,
            size,
            image
        FROM products
    ";

    // Add search and category conditions.

    if (!empty($conditions)) {

        $sql .= ' WHERE ' . implode(
            ' AND ',
            $conditions
        );
    }

    // =========================================
    // SORT PRODUCTS
    // =========================================

    $sql .= ' ORDER BY ' . $sortOptions[$sort];

    // =========================================
    // PREPARE AND EXECUTE QUERY
    // =========================================

    $stmt = $conn->prepare($sql);

    if (!$stmt) {

        throw new RuntimeException(
            'Unable to prepare product query.'
        );
    }

    // Bind parameters when searching or filtering.

    if (!empty($parameters)) {

        $stmt->bind_param(
            $types,
            ...$parameters
        );
    }

    if (!$stmt->execute()) {

        throw new RuntimeException(
            'Unable to execute product query.'
        );
    }

    // =========================================
    // RETRIEVE PRODUCTS
    // =========================================

    $result = $stmt->get_result();

    if (!$result) {

        throw new RuntimeException(
            'Unable to retrieve query results.'
        );
    }

    $products = $result->fetch_all(
        MYSQLI_ASSOC
    );

    // =========================================
    // RETURN JSON
    // =========================================

    sendResponse([
        'success' => true,
        'type' => $type,
        'count' => count($products),
        'products' => $products
    ]);

} catch (Throwable $e) {

    // Log errors without exposing database details.

    error_log(
        'SOPHEA API error: ' . $e->getMessage()
    );

    sendResponse([
        'success' => false,
        'message' => 'Unable to retrieve products.'
    ], 500);

} finally {

    if ($stmt instanceof mysqli_stmt) {
        $stmt->close();
    }

    if ($conn instanceof mysqli) {
        $conn->close();
    }
}