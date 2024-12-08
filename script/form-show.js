
//   window.addEventListener("DOMContentLoaded", function () {
//     const formContainer = document.getElementById("productFormContainer");
//     const showFormButton = document.getElementById("linkshowFormButton");
//     const closeFormButton = document.getElementById("linkcloseFormButton");
//     const closeEditFormButton = document.getElementById("closeEditFormButton");
//     const closeEditFormContainer = document.getElementById("editModal");

//     // Show the form
//     showFormButton.addEventListener("click", function () {
//       formContainer.classList.remove("form-hidden");
//     });

//     // Hide the form
//     closeFormButton.addEventListener("click", function () {
//       formContainer.classList.add("form-hidden");
//     });


//     // Optional: Close the form when clicking outside it
//     document.addEventListener("click", function (e) {
//       // if (!formContainer.contains(e.target) && e.target !== showFormButton) {
//       //   formContainer.classList.add("form-hidden");
//       // }
//     });
//   });


  document.addEventListener("DOMContentLoaded", () => {
    const linkFormContainer = document.getElementById("linkFormContainer");
    const linkCloseButton = document.getElementById("linkcloseFormButton");
    const linkShowFormButton = document.getElementById("linkshowFormButton");
    const addButton = document.querySelector("#linkFormContainer form .add-button button");
    const linkInput = document.querySelector("#linkFormContainer input[type='text']");
    const platformSelect = document.querySelector("#linkFormContainer select");

    // Show the form
    linkShowFormButton.addEventListener("click", () => {
        linkFormContainer.classList.remove("hidden");
    });

    // Close the form
    linkCloseButton.addEventListener("click", () => {
        linkFormContainer.classList.add("hidden");
    });

    // Get the link container
    const linkContainer = document.querySelector(".link-container");

    // Handle the "Add Account" button click
    addButton.addEventListener("click", (e) => {
        e.preventDefault(); // Prevent form submission

        const selectedPlatform = platformSelect.options[platformSelect.selectedIndex].text;
        let link = linkInput.value.trim();

        if (link === "") {
            alert("Please enter a valid link.");
            return;
        }

        // Ensure the URL has a protocol
        if (!link.startsWith("http://") && !link.startsWith("https://")) {
            link = "https://" + link; // Prepend https:// if missing
        }

        // Create a new container for the image and close button
        const linkContainerDiv = document.createElement("div");
        linkContainerDiv.style.display = "flex";
        linkContainerDiv.style.alignItems = "center";
        linkContainerDiv.style.justifyContent = "space-between";
        linkContainerDiv.style.marginRight = "10px";
        linkContainerDiv.style.marginBottom = "15px";

        // Create the image link element
        const linkElement = document.createElement("a");
        linkElement.href = link;
        linkElement.target = "_blank";
        linkElement.innerHTML = `<img src="../assets/${selectedPlatform.toLowerCase()}.png" alt="${selectedPlatform}" style=" margin-right: 5px;">`;

        // Create the close (X) icon
        const closeIcon = document.createElement("span");
        closeIcon.textContent = "✖";
        closeIcon.style.cursor = "pointer";
        closeIcon.style.color = "gray";
        closeIcon.style.marginLeft = "5px";
        closeIcon.title = "Remove Link";

        // Add click event to remove the link container
        closeIcon.addEventListener("click", () => {
            linkContainerDiv.remove();
        });

        // Append the image link and close icon to the container
        linkContainerDiv.appendChild(linkElement);
        linkContainerDiv.appendChild(closeIcon);

        // Append the container to the main link area
        linkContainer.appendChild(linkContainerDiv);

        // Create a hidden input for the platform
        const hiddenInput = document.createElement("input");
        hiddenInput.type = "hidden";
        hiddenInput.name = selectedPlatform.toLowerCase(); // Set the name based on the platform (e.g., "lazada", "shopee")
        hiddenInput.value = link; // Set the value to the entered URL

        // Temporarily make the input visible for testing (you can remove this after verifying)
        hiddenInput.style.display = "block"; // Change this to "block" for visibility, or use a background color to see it.

        // Append the hidden input to the form (or another appropriate container)
        document.querySelector("form").appendChild(hiddenInput);

        // Clear the input field and hide the form
        linkInput.value = "";
        linkFormContainer.classList.add("hidden");
    });
});

const editButtons = document.querySelectorAll('.edit-btn'); // All edit buttons
    const cancelButton = document.getElementById('cancelButton');
    const accountInfo = document.getElementById('accountInfo');
    const personalInfo = document.getElementById('personalInfo');
    const updateForm = document.getElementById('updateInfo');

    // Function to hide account/personal info and show the form
    const showEditForm = () => {
        accountInfo.classList.add('hidden');
        personalInfo.classList.add('hidden');
        updateForm.classList.remove('hidden');
    };

    // Function to show account/personal info and hide the form
    const hideEditForm = () => {
        accountInfo.classList.remove('hidden');
        personalInfo.classList.remove('hidden');
        updateForm.classList.add('hidden');
    };

    // Show the update form and hide account/personal info on Edit button click
    editButtons.forEach(button => {
        button.addEventListener('click', showEditForm);
    });

    // Hide the update form and show account/personal info on Cancel button click
    // Get the cancel button and error container
// Get the cancel button, error container, and password inputs
const errorContainer = document.querySelector('#error-container');
const passwordInput = document.getElementById('password');
const confirmPasswordInput = document.getElementById('confirmpassword');

// Add event listener to the cancel button
// cancelButton.addEventListener('click', function () {
//     // Clear any error messages when cancel button is clicked
//     errorContainer.innerHTML = '';

//     // Clear the password and confirm password input fields
//     passwordInput.value = '';
//     confirmPasswordInput.value = '';

//     // Assuming you have a function to hide the form
//     hideEditForm();
// });




