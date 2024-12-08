const storeCategoriesContainer = document.getElementById("store-categories");
const storeCategories =
  JSON.parse(storeCategoriesContainer.getAttribute("data-storecategories")) ||
  [];
const addCategoryButton = document.getElementById("addCategoryButton");
const addCategoryContainer = document.getElementById("new-category");
const newCategoryInput = document.getElementById("newcategory");
const saveCategoryButton = document.getElementById("saveCategoryButton");

const renderSelect = () => {
  storeCategoriesContainer.innerHTML = "";
  storeCategories.forEach((category) => {
    const id = category.value.split("-")[1];
    console.log(id);
    const div = document.createElement("div");

    const input = document.createElement("input");
    input.value = category.category_name;
    input.name = `storecategory[${id}]`;
    console.log(`storecategory[${id}]`);

    // Delete button
    const deleteButton = document.createElement("button");
    deleteButton.innerHTML = "&#10006;";
    deleteButton.type = "button";

    deleteButton.addEventListener("click", () => {
      input.value = "";
      div.style.display = "none";
    });

    // Append elements to the container
    div.appendChild(input);
    div.appendChild(deleteButton);
    storeCategoriesContainer.appendChild(div);
  });
};

// Handle adding a new category
addCategoryButton.addEventListener("click", () => {
  if (addCategoryButton.textContent === "Cancel") {
    // Filter out new categories (negative IDs)
    storeCategories.splice(
      storeCategories.findIndex((category) => category.categoryid < 0),
      storeCategories.length
    );
    renderSelect();
  }

  newCategoryInput.value = ""; // Clear input
  addCategoryContainer.classList.toggle("hidden");
  addCategoryButton.textContent =
    addCategoryButton.textContent === "Add New Category"
      ? "Cancel"
      : "Add New Category";
});

// Handle saving a new category
saveCategoryButton.addEventListener("click", () => {
  const category_name = newCategoryInput.value.trim();

  if (category_name) {
    const newCategoryId = -1 * (storeCategories.length + 1);

    storeCategories.push({ category_name, categoryid: newCategoryId });
    newCategoryInput.value = "";
    renderSelect();
  }
});

// Initialize the rendering on page load
document.addEventListener("DOMContentLoaded", () => {
  renderSelect();
});
