// =============================================
// SOPHEA — PRODUCT TEMPLATES
// IXD 608 — Back-End Development
// =============================================

"use strict";

const sopheaProductImages = {
    current: "img/store/sophea-current.png",
    obsidian: "img/store/sophea-obsidian.png",
    dune: "img/store/sophea-dune.jpg",
    verdant: "img/store/sophea-verdant.jpg",
    still: "img/store/sophea-still.jpg",
    vapor: "img/store/sophea-vapor.jpg"
};

// =============================================
// CREATE PRODUCT CARD
// =============================================

window.sopheaProductTemplate = function (product) {

    const id = String(product.id ?? "");
    const name = String(product.name ?? "");
    const scent = String(product.scent ?? "");
    const category = String(product.category ?? "");
    const price = Number(product.price ?? 0);

    const normalizedName = name
        .trim()
        .toLowerCase()
        .replace(/^sophea\s+/, "");

    const comingSoonNames = [
        "noir",
        "lumen",
        "serein"
    ];

    const comingSoon = comingSoonNames.includes(
        normalizedName
    );

    const imagePath =
    sopheaProductImages[normalizedName] ||
    product.image ||
    "img/store/sophea-candles.png";

    // =========================================
    // CARD CONTAINER
    // =========================================

    const card = document.createElement("article");

    card.className = "sophea-product-card";

    // =========================================
    // COMING SOON PRODUCTS
    // =========================================

    if (comingSoon) {

        const placeholder = document.createElement("div");

        placeholder.className =
            "sophea-coming-soon-image";

        const brand = document.createElement("span");
        brand.textContent = "SOPHEA";

        const heading = document.createElement("h3");
        heading.textContent = "COMING SOON";

        const message = document.createElement("p");

        message.textContent =
            "Something beautiful is in the making.";

        placeholder.append(
            brand,
            heading,
            message
        );

        card.appendChild(placeholder);
    }

    // =========================================
    // PRODUCT IMAGE
    // =========================================

    else {

        const imageLink = document.createElement("a");

        imageLink.href =
            "product_item.php?id=" +
            encodeURIComponent(id);

        const image = document.createElement("img");

        image.src = imagePath;
        image.alt = name + " candle";
        image.loading = "lazy";

        imageLink.appendChild(image);
        card.appendChild(imageLink);
    }

    // =========================================
    // PRODUCT INFORMATION
    // =========================================

    const content = document.createElement("div");

    content.className = "sophea-product-content";

    // Category

    const categoryElement = document.createElement("p");

    categoryElement.className =
        "sophea-product-category";

    categoryElement.textContent =
        category.toLowerCase();

    // Product name

    const title = document.createElement("h2");

    title.className = "sophea-product-name";

    title.textContent = name;

    // Fragrance notes

    const description = document.createElement("p");

    description.className =
        "sophea-product-description";

    description.textContent = scent;

    content.append(
        categoryElement,
        title,
        description
    );

    // =========================================
    // PRODUCT PRICE AND BUTTON
    // =========================================

    if (comingSoon) {

        const comingSoonButton =
            document.createElement("span");

        comingSoonButton.className =
            "sophea-coming-soon-button";

        comingSoonButton.textContent =
            "COMING SOON";

        content.appendChild(comingSoonButton);

    } else {

        const priceElement = document.createElement("p");

        priceElement.className =
            "sophea-product-price";

        priceElement.textContent =
            price.toLocaleString("en-US", {
                style: "currency",
                currency: "USD"
            });

        const discoverButton =
            document.createElement("a");

        discoverButton.className = "sophea-button";

        discoverButton.href =
            "product_item.php?id=" +
            encodeURIComponent(id);

        discoverButton.textContent = "DISCOVER";

        content.append(
            priceElement,
            discoverButton
        );
    }

    card.appendChild(content);

    return card;
};