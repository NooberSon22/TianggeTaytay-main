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

const displayLinks = (shopee_link, lazada_link) => {
  links.innerHTML = "";

  if (shopee_link) {
    const shopeeIcon = document.createElement("img");
    shopeeIcon.src = "../assets/shopee.png";
    shopeeIcon.alt = "Shopee Link";
    shopeeIcon.classList.add("link-icon");
    links.appendChild(shopeeIcon);

    shopeeIcon.addEventListener("click", () => {
      window.open(shopee_link, "_blank");
    });
  }

  if (lazada_link) {
    const lazadaIcon = document.createElement("img");
    lazadaIcon.src = "../assets/lazada.png";
    lazadaIcon.alt = "Lazada Link";
    lazadaIcon.classList.add("link-icon");
    links.appendChild(lazadaIcon);

    lazadaIcon.addEventListener("click", () => {
      window.open(lazada_link, "_blank");
    });
  }

  if (!shopee_link && !lazada_link) {
    const noLinksMessage = document.createElement("p");
    noLinksMessage.textContent = "No external links available.";
    links.appendChild(noLinksMessage);
  }
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

    const { product, images, categories, types, storeid: store_id } = data;

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
    displayLinks(product.shopee_link, product.lazada_link);
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
