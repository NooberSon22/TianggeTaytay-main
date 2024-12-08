window.addEventListener("DOMContentLoaded", function () {
  const formContainer = document.getElementById("productFormContainer");
  const showFormButton = document.getElementById("showFormButton");
  const closeFormButton = document.getElementById("closeFormButton");
  const closeEditFormButton = document.getElementById("closeEditFormButton");
  const closeEditFormContainer = document.getElementById("editModal");

  // Show the form
  showFormButton.addEventListener("click", function () {
    formContainer.classList.remove("form-hidden");
  });

  // Hide the form
  closeFormButton.addEventListener("click", function () {
    formContainer.classList.add("form-hidden");
  });

  // Optional: Close the form when clicking outside it
  document.addEventListener("click", function (e) {
    // if (!formContainer.contains(e.target) && e.target !== showFormButton) {
    //   formContainer.classList.add("form-hidden");
    // }
  });
});

// Get elements
const editButtons = document.querySelectorAll(".edit-btn"); // All edit buttons
const cancelButton = document.getElementById("cancelButton");
const accountInfo = document.getElementById("accountInfo");
const personalInfo = document.getElementById("personalInfo");
const updateForm = document.getElementById("updateInfo");

// Function to hide account/personal info and show the form
const showEditForm = () => {
  accountInfo.classList.add("hidden");
  personalInfo.classList.add("hidden");
  updateForm.classList.remove("hidden");
};

// Function to show account/personal info and hide the form
const hideEditForm = () => {
  accountInfo.classList.remove("hidden");
  personalInfo.classList.remove("hidden");
  updateForm.classList.add("hidden");
};

// Show the update form and hide account/personal info on Edit button click
editButtons.forEach((button) => {
  button.addEventListener("click", showEditForm);
});

// Hide the update form and show account/personal info on Cancel button click
cancelButton.addEventListener("click", hideEditForm);
