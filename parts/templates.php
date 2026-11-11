<?php

function productListTemplate($product) {

    $id = (int) $product->id;

    $name = htmlspecialchars(
        (string) $product->name,
        ENT_QUOTES,
        'UTF-8'
    );

    $price = number_format((float) $product->price, 2);

    $scent = htmlspecialchars(
        (string) ($product->scent ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );

    $categoryRaw = strtolower(trim(
        (string) ($product->category ?? '')
    ));

    $category = htmlspecialchars(
        $categoryRaw,
        ENT_QUOTES,
        'UTF-8'
    );

    $image = htmlspecialchars(
        (string) ($product->image ?? ''),
        ENT_QUOTES,
        'UTF-8'
    );

    $comingSoonNames = [
        'holiday',
        'solace',
        'terra',
        'noir',
        'lumen',
        'serein'
    ];

    $normalizedName = strtolower(trim(
        (string) $product->name
    ));

    $normalizedName = preg_replace(
        '/^sophea\s+/',
        '',
        $normalizedName
    );

    $comingSoon = in_array(
        $normalizedName,
        $comingSoonNames,
        true
    );

    if ($comingSoon) {

        return '
        <article class="sophea-product-card">

            <div class="sophea-coming-soon-image">
                <span>SOPHEA</span>
                <h3>COMING SOON</h3>
                <p>Something beautiful is in the making.</p>
            </div>

            <div class="sophea-product-content">

                <p class="sophea-product-category">'.$category.'</p>

                <h2 class="sophea-product-name">'.$name.'</h2>

                <p class="sophea-product-description">'.$scent.'</p>

                <span class="sophea-coming-soon-button">
                    COMING SOON
                </span>

            </div>

        </article>';
    }

    return '
        <article class="sophea-product-card">

            <a href="product_item.php?id='.$id.'">
                <img src="'.$image.'" alt="'.$name.' candle" loading="lazy">
            </a>

            <div class="sophea-product-content">

                <p class="sophea-product-category">'.$category.'</p>

                <h2 class="sophea-product-name">'.$name.'</h2>

                <p class="sophea-product-description">'.$scent.'</p>

                <p class="sophea-product-price">$'.$price.'</p>

                <a href="product_item.php?id='.$id.'" class="sophea-button">
                    DISCOVER
                </a>

            </div>

        </article>';
}