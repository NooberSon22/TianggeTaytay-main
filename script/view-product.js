const productName = document.querySelector(".product-name");
const productPrice = document.querySelector(".product-price");
const productDescription = document.querySelector(".product-desc");
const categoryList = document.querySelector(".categories");
const typeList = document.querySelector(".types");
const productId = new URLSearchParams(window.location.search).get("id");
const imageList = document.querySelector(".image-list");
const previewImage = document.querySelector(".prev-image img");
const links = document.querySelector(".links");
const storename = document.querySelector(".store-name");
const viewShop = document.querySelector(".view-shop");
let storeid = "";

const defaultImage = "https://via.placeholder.com/150?text=No+Image";
const API_URL = "http://localhost:3000/server";

const displayLinks = (productlinks) => {
  links.innerHTML = "";

  if (!productlinks || productlinks.length === 0) {
    const noLinksMessage = document.createElement("p");
    noLinksMessage.textContent = "No external links available.";
    links.appendChild(noLinksMessage);
  }

  productlinks.forEach((link) => {
    const div = document.createElement("div");
    const img = document.createElement("img");
    img.src = `data:image/png;base64,${link.linkimg}`;
    img.loading = "lazy";

    div.appendChild(img);

    div.className = "link";

    div.addEventListener("click", () => {
      window.open(link.link, "_blank");
    });

    links.appendChild(div);
  });
};

const displayImages = (images) => {
  // Clear existing images
  imageList.innerHTML = "";

  images.forEach((image, index) => {
    const div = document.createElement("div");
    const productImage = document.createElement("img");

    productImage.src = image ? `data:image/png;base64,${image}` : defaultImage;
    productImage.alt = `Product Image ${index + 1}`;
    productImage.loading = "lazy";

    div.addEventListener("click", () => {
      const selectedImage = document.querySelector(".selected-image");
      if (selectedImage) selectedImage.classList.remove("selected-image");
      div.classList.add("selected-image");
      previewImage.src = productImage.src;
    });

    if (index === 0) {
      div.classList.add("selected-image");
      previewImage.src = productImage.src;
    }

    div.appendChild(productImage);
    imageList.appendChild(div);
  });

  if (images.length === 0) {
    previewImage.src = defaultImage;
    const noImageMessage = document.createElement("p");
    noImageMessage.textContent = "No images available for this product.";
    imageList.appendChild(noImageMessage);
  }
};

const displayProductDetails = (name, price, description) => {
  productName.textContent = name || "Unknown Product";
  productPrice.innerHTML = price ? `&#8369; ${price}` : "Price not available";
  productDescription.textContent = description || "No description provided.";
};

const fetchProductInfo = async () => {
  try {
    if (!productId) {
      throw new Error("Invalid product ID in URL.");
    }

    const response = await fetch(`${API_URL}/view-product.php?id=${productId}`);
    if (!response.ok) {
      throw new Error(`Failed to fetch product data: ${response.statusText}`);
    }

    const data = await response.json();
    return data;
  } catch (error) {
    console.error(error.message);
    alert(
      "An error occurred while fetching product information. Please try again."
    );
  }
};

const displayCategories = (categories) => {
  categories.forEach((category) => {
    const div = document.createElement("div");
    div.textContent = category;
    categoryList.appendChild(div);
  });
};

const displayTypes = (types) => {
  types.forEach((type) => {
    const div = document.createElement("div");
    div.textContent = type;
    typeList.appendChild(div);
  });
};

const renderPage = async () => {
  try {
    const data = await fetchProductInfo();
    if (!data || !data.product) {
      throw new Error("Product data is missing or invalid.");
    }

    const {
      product,
      images,
      categories,
      types,
      storeid: store_id,
      links,
    } = data;

    storeid = store_id;

    displayProductDetails(
      product.product_name,
      product.price,
      product.description,
      product
    );
    displayImages(images || []);
    displayCategories(categories || []);
    displayTypes(types || []);
    displayLinks(links || []);
    renderUI(product.storename);

    storename.textContent = product.storename;

    viewShop.addEventListener("click", () => {
      window.location.href = `http://localhost:3000/pages/view-store.php?storename=${product.storename}`;
    });
  } catch (error) {
    console.error(error.message);
    alert("An error occurred while rendering the page. Please try again.");
  }
};

window.addEventListener("DOMContentLoaded", renderPage);
