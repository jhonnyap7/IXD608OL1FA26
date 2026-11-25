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

// Search, category filter and sorting are applied on the server.
$search = trim((string) ($_GET['search'] ?? ''));
$search = substr($search, 0, 100);

$category = trim((string) ($_GET['category'] ?? 'all'));
$sort = (string) ($_GET['sort'] ?? 'featured');

$allowedSorts = [
    'featured' => 'id ASC',
    'newest' => 'id DESC',
    'oldest' => 'id ASC',
    'name_asc' => 'name ASC, id ASC',
    'name_desc' => 'name DESC, id ASC',
    'price_asc' => 'price ASC, id ASC',
    'price_desc' => 'price DESC, id ASC'
];

if (!isset($allowedSorts[$sort])) {
    $sort = 'featured';
}

$categories = [];

try {

    $conn = makeConn();

    $categoryResult = makeQuery(
        $conn,
        'SELECT DISTINCT category FROM products ORDER BY category'
    );

    while ($row = $categoryResult->fetch_assoc()) {
        $categories[] = $row['category'];
    }

    if (
        $category !== 'all' &&
        !in_array($category, $categories, true)
    ) {
        $category = 'all';
    }

    $conditions = [];
    $params = [];
    $types = '';

    // =====================================
    // SEARCH
    // =====================================

    if ($search !== '') {

        $conditions[] = '
            (
                name LIKE ?
                OR description LIKE ?
                OR category LIKE ?
                OR scent LIKE ?
            )
        ';

        $term = '%' . $search . '%';

        array_push(
            $params,
            $term,
            $term,
            $term,
            $term
        );

        $types .= 'ssss';
    }

    // =====================================
    // CATEGORY FILTER
    // =====================================

    if ($category !== 'all') {

        $conditions[] = 'category = ?';

        $params[] = $category;

        $types .= 's';
    }

    // =====================================
    // BUILD PRODUCT QUERY
    // =====================================

    $sql = '
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
    ';

    if ($conditions) {

        $sql .= ' WHERE ' . implode(
            ' AND ',
            $conditions
        );
    }

    // Sorting uses only predefined SQL expressions.
    $sql .= ' ORDER BY ' . $allowedSorts[$sort];

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        throw new RuntimeException(
            'Unable to prepare collection query'
        );
    }

    if ($params) {
        $stmt->bind_param(
            $types,
            ...$params
        );
    }

    $stmt->execute();

    $products = $stmt
        ->get_result()
        ->fetch_all(MYSQLI_ASSOC);

    $stmt->close();

    $conn->close();

} catch (Throwable $e) {

    error_log(
        'SOPHEA product query failed: ' .
        $e->getMessage()
    );

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
        trim((string) $product['name'])
    );

    // Remove an optional SOPHEA prefix.
    $productName = preg_replace(
        '/^sophea\s+/',
        '',
        $productName
    );

    // Use SOPHEA's assigned product image first.
    // Fall back to the database image if no mapping exists.

    $product['image'] =
        $productImages[$productName]
        ?? (
            !empty($product['image'])
                ? $product['image']
                : $fallbackImage
        );
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

        <!-- =====================================
             SEARCH, FILTER AND SORT
        ===================================== -->

        <form
            class="sophea-collection-tools"
            method="get"
            action="product_list.php"
            role="search"
        >

            <!-- SEARCH -->

            <div class="sophea-tool-field sophea-tool-search">

                <label for="collection-search">
                    Search fragrances
                </label>

                <input
                    id="collection-search"
                    type="search"
                    name="search"
                    placeholder="Search by name or scent"
                    value="<?= htmlspecialchars(
                        $search,
                        ENT_QUOTES,
                        'UTF-8'
                    ) ?>"
                >

            </div>

            <!-- CATEGORY FILTER -->

            <div class="sophea-tool-field">

                <label for="collection-category">
                    Filter by category
                </label>

                <select
                    id="collection-category"
                    name="category"
                >

                    <option value="all">
                        All categories
                    </option>

                    <?php foreach ($categories as $option): ?>

                        <option
                            value="<?= htmlspecialchars(
                                $option,
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>"
                            <?= $category === $option
                                ? 'selected'
                                : '' ?>
                        >
                            <?= htmlspecialchars(
                                ucfirst($option),
                                ENT_QUOTES,
                                'UTF-8'
                            ) ?>
                        </option>

                    <?php endforeach; ?>

                </select>

            </div>

            <!-- SORT -->

            <div class="sophea-tool-field">

                <label for="collection-sort">
                    Sort by
                </label>

                <select
                    id="collection-sort"
                    name="sort"
                >

                    <option
                        value="featured"
                        <?= $sort === 'featured'
                            ? 'selected'
                            : '' ?>
                    >
                        Featured
                    </option>

                    <option
                        value="newest"
                        <?= $sort === 'newest'
                            ? 'selected'
                            : '' ?>
                    >
                        Newest
                    </option>

                    <option
                        value="oldest"
                        <?= $sort === 'oldest'
                            ? 'selected'
                            : '' ?>
                    >
                        Oldest
                    </option>

                    <option
                        value="name_asc"
                        <?= $sort === 'name_asc'
                            ? 'selected'
                            : '' ?>
                    >
                        Name: A–Z
                    </option>

                    <option
                        value="name_desc"
                        <?= $sort === 'name_desc'
                            ? 'selected'
                            : '' ?>
                    >
                        Name: Z–A
                    </option>

                    <option
                        value="price_asc"
                        <?= $sort === 'price_asc'
                            ? 'selected'
                            : '' ?>
                    >
                        Price: low to high
                    </option>

                    <option
                        value="price_desc"
                        <?= $sort === 'price_desc'
                            ? 'selected'
                            : '' ?>
                    >
                        Price: high to low
                    </option>

                </select>

            </div>

            <!-- APPLY -->

            <button
                class="sophea-button"
                type="submit"
            >
                Apply
            </button>

            <!-- RESET -->

            <a
                class="sophea-reset-link"
                href="product_list.php"
            >
                Clear
            </a>

        </form>

        <!-- =====================================
             RESULTS COUNTER
        ===================================== -->

        <?php if (!$databaseError): ?>

            <p
                class="sophea-results-count"
                aria-live="polite"
            >
                <?= count($products) ?>

                <?= count($products) === 1
                    ? 'fragrance'
                    : 'fragrances' ?>

                found
            </p>

        <?php endif; ?>

        <!-- =====================================
             PRODUCT RESULTS
        ===================================== -->

        <div class="sophea-product-grid">

            <?php if ($databaseError): ?>

                <p class="sophea-page-description">
                    Our collection is temporarily unavailable.
                    Please try again later.
                </p>

            <?php elseif (empty($products)): ?>

                <p class="sophea-page-description">
                    No fragrances match your selection.
                </p>

            <?php else: ?>

                <?php foreach ($products as $product): ?>

                    <?php
                    $item = (object) $product;
                    echo productListTemplate($item);
                    ?>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>

    </main>

    <!-- JavaScript templates must load before the collection controller. -->

    <script src="js/templates.js" defer></script>
    <script src="js/product_list.js" defer></script>

</body>

</html>