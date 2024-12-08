const categorySelect = document.querySelector(".categories-select");
const typeSelect = document.querySelector(".types-select");
const productContainer = document.querySelector(".products");
const pagesField = document.querySelector(".pages");
const results = document.querySelector(".results");
let currentPage = 1; // Default to page 1
let category = "";
let type = "";
const targetDiv = document.querySelector(".products-container");
const resetButton = document.querySelector(".reset-button");

const createOption = (value, text) => {
  const option = document.createElement("option");
  option.value = value;
  option.textContent = text;
  return option;
};

const scrollToTop = () => {
  setTimeout(() => {
    targetDiv.scrollIntoView({
      behavior: "smooth",
      block: "start",
    });
  }, 100);
};

const resetFilters = () => {
  currentPage = 1; // Reset to page 1
  category = "";
  type = "";

  categorySelect.selectedIndex = 0;
  typeSelect.selectedIndex = 0;

  fetchProducts(storename);
};

const showLoading = (container) => {
  container.innerHTML = '<p class="loading">Loading...</p>';
};

const showError = (container, message) => {
  container.innerHTML = `<p class="error-message">${message}</p>`;
};

const showEmptyState = (container, message) => {
  container.innerHTML = `<p class="empty-state">${message}</p>`;
};

const highlightCurrentPage = () => {
  const prevSelected = document.querySelector(".pages > .page p.selected");
  if (prevSelected) prevSelected.classList.remove("selected");

  const currentPageElement = document.querySelector(
    `.pages > .page:nth-of-type(${currentPage}) p`
  );
  if (currentPageElement) currentPageElement.classList.add("selected");
};

const ProductCard = (id, img, name, price) => {
  const div = document.createElement("div");
  div.className = "product-card";
  div.addEventListener("click", () => clickProduct(id));

  const productImage = document.createElement("img");
  productImage.className = "product-image";
  productImage.src = img ? `data:image/png;base64,${img}` : defaultImage;
  productImage.loading = "lazy";
  productImage.onerror = () => {
    productImage.src = defaultImage; // Fallback for broken images
  };

  const productName = document.createElement("p");
  productName.textContent = name;

  const productPrice = document.createElement("p");
  productPrice.innerHTML = `&#8369; <span>${price}</span>`;

  div.addEventListener("click", () => {
    window.location.href = `${URL}/pages/view-product.php?id=${id}`;
  });

  div.appendChild(productImage);
  div.appendChild(productName);
  div.appendChild(productPrice);

  return div;
};

/** Fetch Categories */
const fetchCategories = async () => {
  try {
    const response = await fetch(
      `${URL}/server/products-fetch-ui.php?component=category&store_id=${storeid}`
    );

    if (!response.ok) {
      throw new Error(`Failed to fetch categories: ${response.statusText}`);
    }

    const data = await response.json();

    if (!data.categories || !Array.isArray(data.categories)) {
      throw new Error("Invalid categories data format.");
    }

    categorySelect.innerHTML = ""; // Clear previous options
    categorySelect.appendChild(createOption("", "All Categories")); // Default option

    data.categories.forEach((category) => {
      categorySelect.appendChild(createOption(category.value, category.label));
    });
  } catch (error) {
    console.error("Error fetching categories:", error);
    showError(categorySelect, "Failed to load categories.");
  }
};

/** Fetch Types */
const fetchTypes = async () => {
  try {
    const response = await fetch(
      `${URL}/server/products-fetch-ui.php?component=type&store_id=${storeid}`
    );

    if (!response.ok) {
      throw new Error(`Failed to fetch types: ${response.statusText}`);
    }

    const data = await response.json();

    typeSelect.innerHTML = ""; // Clear previous options
    typeSelect.appendChild(createOption("", "All Types")); // Default option

    data.product_types.forEach((product_type) => {
      typeSelect.appendChild(
        createOption(product_type.value, product_type.label)
      );
    });
  } catch (error) {
    console.error("Error fetching types:", error);
    showError(typeSelect, "Failed to load types.");
  }
};

/** Fetch Products */
const fetchProducts = async (storename) => {
  showLoading(productContainer);

  try {
    const response = await fetch(
      `${URL}/server/fetch_product.php?store=${storename}&category=${category}&type=${type}&max=${10}&page=${currentPage}`
    );

    if (!response.ok) {
      throw new Error(`Failed to fetch products: ${response.statusText}`);
    }

    const data = await response.json();
    const { data: products, total_records, start, end, pages, page } = data;

    if (!products || products.length === 0) {
      productContainer.classList.add("empty");
      showEmptyState(
        productContainer,
        "No products found for the selected filters."
      );
      results.textContent = "No results found to display";
      pagesField.innerHTML = "";
      return;
    }

    productContainer.classList.remove("empty");
    productContainer.innerHTML = ""; // Clear previous products
    products.forEach((product) => {
      const productCard = ProductCard(
        product.product_id,
        product.img,
        product.product_name,
        product.price
      );
      productContainer.appendChild(productCard);
    });

    pagesField.innerHTML = "";

    // Render pagination
    Array.from({ length: pages }, (_, i) => i + 1).forEach((pageNumber) => {
      const pageDiv = document.createElement("div");
      pageDiv.className = "page";
      pageDiv.innerHTML = `<p>${pageNumber}</p>`;
      pageDiv.addEventListener("click", async () => {
        currentPage = pageNumber;
        await fetchProducts(storename);
        scrollToTop();
      });

      pagesField.appendChild(pageDiv);

      highlightCurrentPage();
    });

    results.textContent = `Showing ${start} to ${end} of ${total_records} results`;
  } catch (error) {
    console.error("Error fetching products:", error);
    showError(productContainer, "Failed to load products.");
  }
};

/** Render UI */
const renderUI = async (storename) => {
  await fetchCategories();
  await fetchTypes();
  await fetchProducts(storename);

  categorySelect.addEventListener("change", () => {
    category = categorySelect.value;
    fetchProducts(storename);
  });

  typeSelect.addEventListener("change", () => {
    type = typeSelect.value;
    fetchProducts(storename);
  });

  resetButton.addEventListener("click", () => {
    resetFilters();
    fetchProducts(storename);
  });
};
