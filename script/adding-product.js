const productCategorySelect = document.querySelector("#category");
const productTypesSelect = document.querySelector("#type");
const productCategories = document.querySelector(".product-categories");
const productTypes = document.querySelector(".product-types");

const addCategory = (value, label) => {
    const category = document.createElement("div");
    category.className = "category-item";

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "category[]";
    input.value = value;

    const categoryLabel = document.createElement("p");
    categoryLabel.textContent = label;

    const deleteButton = document.createElement("div");
    deleteButton.innerHTML = "&#10006;";
    deleteButton.addEventListener("click", () => {
        category.remove();
    });

    category.appendChild(input);
    category.appendChild(categoryLabel);
    category.appendChild(deleteButton);

    // Append the category to the container
    productCategories.appendChild(category);
};

const addType = (value, label) => {
    const type = document.createElement("div");
    type.className = "type-item";

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "type[]";
    input.value = value;

    const typeLabel = document.createElement("p");
    typeLabel.textContent = label;

    const deleteButton = document.createElement("div");
    deleteButton.innerHTML = "&#10006;";
    deleteButton.className = "delete-button";
    deleteButton.addEventListener("click", () => {
        type.remove();
    });

    type.appendChild(input);
    type.appendChild(typeLabel);
    type.appendChild(deleteButton);

    productTypes.appendChild(type);
};

document.addEventListener("DOMContentLoaded", () => {
    productCategorySelect.addEventListener("change", () => {
        const value = productCategorySelect.value;
        const label = productCategorySelect.options[productCategorySelect.selectedIndex].text;
        addCategory(value, label);
    });

    productTypesSelect.addEventListener("change", () => {
        const value = productTypesSelect.value;
        const label = productTypesSelect.options[productTypesSelect.selectedIndex].text;
        addType(value, label);
    });
});
