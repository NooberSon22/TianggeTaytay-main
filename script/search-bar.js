const searchBar = document.querySelector("#searchbar");
const searchResults = document.querySelector(".search-results");
const navbar = document.querySelector(".navbar-list");

const navigateProduct = (productId) => {
  searchResults.classList.remove("open");
  window.location.href = `http://localhost/ETianggeTaytay/pages/view-product.php?id=${productId}`;
};

const renderResults = (results) => {
  searchResults.innerHTML = ""; // Clear previous results

  if (results.length === 0) {
    // Show "No results found"
    const noResultsElement = document.createElement("p");
    noResultsElement.textContent = "No results found...";
    noResultsElement.classList.add("no-results");
    searchResults.appendChild(noResultsElement);
    return;
  }

  results.forEach((result, index) => {
    // Create result element
    const resultElement = document.createElement("p");
    resultElement.textContent = result.product_name;
    resultElement.setAttribute("data-product-id", result.productid);
    resultElement.setAttribute("tabindex", index); // Allow focus with keyboard

    // Add click event
    resultElement.addEventListener("click", () =>
      navigateProduct(result.productid)
    );

    // Add keyboard navigation support
    resultElement.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        navigateProduct(result.productid);
      }
    });

    searchResults.appendChild(resultElement);
  });
};

const getResults = async (searchTerm) => {
  try {
    const response = await fetch(
      "http://localhost/ETianggeTaytay/server/search_products.php",
      {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ search_term: searchTerm }),
      }
    );

    const data = await response.json();
    renderResults(data);
  } catch (error) {
    console.error("Error fetching results:", error);
  }
};

// Input event for live search
searchBar.addEventListener("input", (e) => {
  const searchTerm = e.target.value.trim().toLowerCase();

  if (searchTerm.length > 0) {
    searchResults.classList.add("open");
    getResults(searchTerm);
  } else {
    searchResults.classList.remove("open");
    searchResults.innerHTML = ""; // Clear results when input is empty
  }
});

// Keyboard events for navigation
searchBar.addEventListener("keydown", (e) => {
  const firstResult = searchResults.querySelector("p:first-of-type");

  if (e.key === "Enter" && firstResult) {
    firstResult.click();
  }

  if (e.key === "ArrowDown") {
    const firstFocusable = searchResults.querySelector("p");
    if (firstFocusable) {
      firstFocusable.focus(); // Move focus to the first result
    }
  }
});

searchResults.addEventListener("keydown", (e) => {
  const focused = document.activeElement;
  if (!focused || focused.parentNode !== searchResults) return;

  if (e.key === "ArrowDown") {
    const next = focused.nextElementSibling;
    if (next) next.focus();
  } else if (e.key === "ArrowUp") {
    const prev = focused.previousElementSibling;
    if (prev) prev.focus();
  }
});

navbar.addEventListener("click", (event) => {
  const clickedItem = event.target;

  if (clickedItem.classList.contains("navbar-item")) {
    document
      .querySelectorAll(".navbar-item")
      .forEach((item) => item.classList.remove("selected"));
    clickedItem.classList.add("selected");
    console.log(clickedItem);
  }
});

// Close results when clicking outside
document.addEventListener("click", (e) => {
  if (!searchResults.contains(e.target) && e.target !== searchBar) {
    searchResults.classList.remove("open");
  }
});

document.addEventListener("keydown", (e) => {
  if (e.key === "ArrowDown" || e.key === "ArrowUp") {
    const focused = document.activeElement;
    if (focused && focused.parentNode === searchResults) {
      e.preventDefault(); // Prevent default arrow key behavior
    }
  }
});
