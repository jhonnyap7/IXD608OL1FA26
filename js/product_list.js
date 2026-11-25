// =============================================
// SOPHEA — PRODUCT COLLECTION
// IXD 608 — Back-End Development
// =============================================

"use strict";

document.addEventListener("DOMContentLoaded", function () {

    // =========================================
    // ELEMENTS
    // =========================================

    const productList = document.querySelector(
        ".sophea-product-grid"
    );

    const searchForm = document.querySelector(
        ".sophea-collection-tools"
    );

    const searchInput = document.querySelector(
        "#collection-search"
    );

    const categoryFilter = document.querySelector(
        "#collection-category"
    );

    const sortSelect = document.querySelector(
        "#collection-sort"
    );

    const resultsCount = document.querySelector(
        ".sophea-results-count"
    );

    // Only initialize on the collection page.

    if (!productList || !searchForm) {
        return;
    }

    // =========================================
    // API CONFIGURATION
    // =========================================

    const API_URL = "data/api.php";

    let currentRequest = null;

    // =========================================
    // READ CURRENT CONTROLS
    // =========================================

    function getParameters() {

        return {
            search: searchInput
                ? searchInput.value.trim()
                : "",

            category: categoryFilter
                ? categoryFilter.value
                : "all",

            sort: sortSelect
                ? sortSelect.value
                : "featured"
        };
    }

    // =========================================
    // DETERMINE API OPERATION
    // =========================================

    function getRequestType(parameters) {

        if (parameters.search !== "") {
            return "product_search";
        }

        if (
            parameters.category !== "" &&
            parameters.category !== "all"
        ) {
            return "product_filter";
        }

        if (parameters.sort !== "featured") {
            return "product_sort";
        }

        return "products_all";
    }

    // =========================================
    // UPDATE RESULTS COUNTER
    // =========================================

    function updateResultsCount(count) {

        if (!resultsCount) {
            return;
        }

        const label = count === 1
            ? "fragrance"
            : "fragrances";

        resultsCount.textContent =
            `${count} ${label} found`;
    }

    // =========================================
    // DISPLAY STATUS MESSAGE
    // =========================================

    function showMessage(message) {

        const paragraph = document.createElement("p");

        paragraph.className =
            "sophea-page-description";

        paragraph.textContent = message;

        productList.replaceChildren(paragraph);
    }

    // =========================================
    // RENDER PRODUCTS
    // =========================================

    function renderProducts(products) {

        productList.replaceChildren();

        if (!Array.isArray(products)) {
            showMessage("Unable to display fragrances.");
            return;
        }

        updateResultsCount(products.length);

        if (products.length === 0) {

            showMessage(
                "No fragrances match your selection."
            );

            return;
        }

        // Product cards are created by templates.js.
        // This preserves separation between
        // application logic and HTML templates.

        if (
            typeof window.sopheaProductTemplate !==
            "function"
        ) {

            showMessage(
                "Product template is unavailable."
            );

            console.error(
                "SOPHEA: sopheaProductTemplate is missing."
            );

            return;
        }

        const fragment = document.createDocumentFragment();

        products.forEach(function (product) {

            const card = window.sopheaProductTemplate(
                product
            );

            if (card instanceof Node) {
                fragment.appendChild(card);
            }
        });

        productList.appendChild(fragment);
    }

    // =========================================
    // QUERY API
    // =========================================

    async function query(parameters) {

        // Cancel an unfinished request before
        // starting another request.

        if (currentRequest) {
            currentRequest.abort();
        }

        const controller = new AbortController();

        currentRequest = controller;

        const requestType = getRequestType(parameters);

        const queryParameters = new URLSearchParams({
            type: requestType,
            search: parameters.search,
            category: parameters.category,
            sort: parameters.sort
        });

        const requestURL =
            `${API_URL}?${queryParameters.toString()}`;

        try {

            const response = await fetch(requestURL, {
                method: "GET",
                signal: controller.signal,
                headers: {
                    "Accept": "application/json"
                }
            });

            if (!response.ok) {

                throw new Error(
                    `API request failed: ${response.status}`
                );
            }

            const data = await response.json();

            if (
                data.success !== true ||
                !Array.isArray(data.products)
            ) {

                throw new Error(
                    data.message ||
                    "Invalid product response."
                );
            }

            // Ignore a response if a newer request
            // has already started.

            if (currentRequest !== controller) {
                return;
            }

            renderProducts(data.products);

        } catch (error) {

        if (
        error.name === "AbortError" ||
        currentRequest !== controller
        ) {
        return;
         }

            console.error(
                "SOPHEA product request failed:",
                error
            );

            showMessage(
                "Unable to load fragrances. Please try again."
            );

        } finally {

            if (currentRequest === controller) {
                currentRequest = null;
            }
        }
    }

    // =========================================
    // SEARCH
    // =========================================

    searchForm.addEventListener(
        "submit",
        function (event) {

            event.preventDefault();

            query(getParameters());
        }
    );

    // =========================================
    // CATEGORY FILTER
    // =========================================

    if (categoryFilter) {

        categoryFilter.addEventListener(
            "change",
            function () {

                query(getParameters());
            }
        );
    }

    // =========================================
    // SORT
    // =========================================

    if (sortSelect) {

        sortSelect.addEventListener(
            "change",
            function () {

                query(getParameters());
            }
        );
    }

    // =========================================
    // INITIAL PRODUCT LOAD
    // =========================================

    // The PHP page already renders its initial
    // products. Once the JavaScript template and
    // script integration are complete, AJAX will
    // handle subsequent search/filter/sort requests.

    console.log(
        "SOPHEA product collection initialized."
    );

});