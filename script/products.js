const productsField = document.querySelector(".products");
const pagesField = document.querySelector(".pages");
// const pageBack = document.querySelector(".back-page");
// const pageNext = document.querySelector(".next-page");
const results = document.querySelector(".results");
const resetButton = document.querySelector(".reset-button");
const categoryField = document.querySelector(".categories");
const categoryLabel = document.querySelector(".category");

// Get the current page from the data attribute
let currentPage = parseInt(productsField.getAttribute("data-page"));
let type = productsField.getAttribute("data-type");
let category = productsField.getAttribute("data-category");
let labelCategory = "";

// Default placeholder for products without images
const defaultImage = "https://via.placeholder.com/150?text=No+Image";

const clickProduct = (productId) => {
  window.location.href = `../pages/view-product.php?id=${productId}`;
};

const highlightCurrentPage = () => {
  const prevSelected = document.querySelector(".pages > .page p.selected");
  if (prevSelected) prevSelected.classList.remove("selected");

  const currentPageElement = document.querySelector(
    `.pages > .page:nth-of-type(${currentPage}) p`
  );
  if (currentPageElement) currentPageElement.classList.add("selected");
};

// Create a product card
const ProductCard = (id, img, name, price) => {
  const div = document.createElement("div");
  div.className = "product-card";
  div.addEventListener("click", () => clickProduct(id));

  const productImage = document.createElement("img");
  productImage.className = "product-image";
  productImage.src = img ? `data:image/png;base64,${img}` : defaultImage;
  productImage.loading = "lazy";

  const productName = document.createElement("p");
  productName.textContent = name;

  const productPrice = document.createElement("p");
  productPrice.innerHTML = `&#8369; <span>${price}</span>`;

  div.appendChild(productImage);
  div.appendChild(productName);
  div.appendChild(productPrice);

  return div;
};

// Fetch products and pages from the server
const fetchProducts = async () => {
  try {
    const response = await fetch(
      `http://localhost:3000/server/fetch_product.php?page=${currentPage}&type=${type}&category=${category}`
    );
    if (!response.ok) {
      throw new Error("Failed to fetch products");
    }

    const data = await response.json();
    if (!data.success) {
      throw new Error("Invalid data received");
    }

    return data;
  } catch (error) {
    console.error("Error fetching products:", error);
    return { data: [], pages: 0 };
  }
};

// Render products and pagination
const renderPage = async () => {
  // Clear existing products and pages
  //   productsField.innerHTML = "<p>Loading...</p>";
  //   pagesField.innerHTML = "";
  const {
    data: productsData,
    pages,
    start,
    end,
    total_records,
  } = await fetchProducts();

  categoryLabel.textContent = labelCategory ? labelCategory : "All Products";

  // Render products
  productsField.innerHTML = ""; // Clear loading message
  if (productsData.length > 0) {
    productsData.forEach((product) => {
      const productCard = ProductCard(
        product.product_id,
        product.img,
        product.product_name,
        product.price
      );
      productsField.appendChild(productCard);
    });
  } else {
    productsField.innerHTML = "<p>No products available.</p>";
  }

  // Clear existing pages
  pagesField.innerHTML = "";

  // Render pagination
  Array.from({ length: pages }, (_, i) => i + 1).forEach((pageNumber) => {
    const pageDiv = document.createElement("div");
    pageDiv.className = "page";
    pageDiv.innerHTML = `<p>${pageNumber}</p>`;
    pageDiv.addEventListener("click", () => {
      currentPage = pageNumber;
      renderPage();
    });

    pagesField.appendChild(pageDiv);
  });

  results.textContent = `Showing ${start} to ${end} of ${total_records} results`;

  window.scrollTo({
    top: 0,
    behavior: "smooth", // Smooth scrolling effect
  });

  highlightCurrentPage();
};

resetButton.addEventListener("click", () => {
  currentPage = 1;
  type = "";
  category = "";
  labelCategory = "All Products";
  renderPage();
});

// pageBack.addEventListener("click", () => {
//   if (currentPage > 1) {
//     currentPage -= 1;
//     renderPage();
//   }
// });

// pageNext.addEventListener("click", () => {
//   const totalPages = pagesField.childElementCount;
//   if (currentPage < totalPages) {
//     currentPage += 1;
//     renderPage();
//   }
// });

window.addEventListener("DOMContentLoaded", () => {
  renderPage();
});
