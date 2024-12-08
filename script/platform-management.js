document.addEventListener("DOMContentLoaded", () => {
  const closeEditFormButton = document.getElementById("closeEditFormButton");
  const closeEditFormContainer = document.getElementById("editProductForm");
  const addAccountsBtn = document.getElementById("addAccountsButton");
  const viewMode = document.getElementById("linkedAccountsView");
  const editMode = document.getElementById("linkedAccountsEdit");
  const platforms = document.getElementById("platforms-select");
  const platformsSelector = document.getElementById("platform-selector");
  const placeHolderPlatform = document.querySelector(".placeholder");
  const storePlatforms = JSON.parse(
    document.querySelector("[data-storelinks]").dataset.storelinks
  );
  const platformLinksContainer = document.getElementById(
    "platform-links-container"
  );
  const cancelButton = document.getElementById("cancelButton");
  const placeHolderLabel = placeHolderPlatform.querySelector(".platform-label");
  const placeHolderInput = placeHolderPlatform.querySelector(
    'input[name="links[$id]"]'
  );

  const renderPlatforms = () => {
    storePlatforms.forEach((platform) => {
      const platformLabel = platform.platformtype;
      const platformLink = platform.link;

      const platformInput = document.createElement("input");
      platformInput.type = "text";
      platformInput.name = `links[${platform.id}]`;
      platformInput.value = platformLink;
      platformInput.placeholder = `Enter ${platformLabel} account link`;

      const platformLabelElement = document.createElement("label");
      platformLabelElement.textContent = platformLabel;

      const platformContainer = document.createElement("div");
      platformContainer.className = "platformlink-input";

      const divider = document.createElement("div");
      divider.appendChild(platformLabelElement);
      divider.appendChild(platformInput);
      platformContainer.appendChild(divider);

      const removeButton = document.createElement("button");
      removeButton.type = "button";
      removeButton.className = "remove-button";
      removeButton.innerHTML = "&#10006;";

      removeButton.addEventListener("click", () => {
        platformInput.value = "";
        platformContainer.style.display = "none";
      });

      platformContainer.appendChild(removeButton);

      platformLinksContainer.appendChild(platformContainer);
    });
  };

  renderPlatforms();

  //   {
  //     "id": 1,
  //     "link": "https://www.lazada.com.ph/?spm=a2o4l.searchlistcategory.header.dhome.4d5f77b3NKqShx#?",
  //     "platformtype": "LAZADA"
  //    }

  //   foreach ($store_links as $link) {
  //     $id = $link['id'];
  //     $platform = $link['platformtype'];
  //     $mainlink = $link['link'];
  //     $platformLabel = strtoupper($platform[0]) . strtolower(substr($platform, 1));

  //     echo "
  //         <div class=\"platformlink-input\">
  //             <div>
  //                 <label for=\"$platform\">$platformLabel</label>
  //                 <input type=\"text\" name=\"links[$id]\"
  //                     value=\"$mainlink\"
  //                     placeholder=\"Enter $platformLabel account link\" />
  //             </div>

  //             <button type=\"button\" class=\"remove-button\">&#10006;</button>
  //         </div>
  //         ";
  // }

  // Toggle edit form visibility
  closeEditFormButton?.addEventListener("click", () => {
    closeEditFormContainer.classList.add("form-hidden");
  });

  // Edit store button logic
  const editStoreBtn = document.getElementById("editStoreBtn");
  const updateInfoForm = document.getElementById("updateInfo");
  const editContainer = document.querySelector(".edit-container");
  const accountInfo = document.getElementById("storeInfo");
  const divider = document.getElementById("divider");
  const mainProducts = document.getElementById("mainProducts");
  const closeButton = document.getElementById("cancelButton");
  const accountContainer = document.querySelector(".account-container");
  const editStoreContainer = document.querySelector(".edit-store");

  editStoreBtn?.addEventListener("click", () => {
    // updateInfoForm?.classList.remove("hidden");
    // editContainer?.classList.add("hidden");
    // accountInfo?.classList.add("hidden");
    // divider?.classList.add("hidden");
    // mainProducts?.classList.add("hidden");
    // editStoreBtn?.classList.toggle("hidden");

    accountContainer?.classList.toggle("hidden");
    editStoreContainer?.classList.toggle("hidden");
  });

  closeButton?.addEventListener("click", () => {
    console.log("Close button clicked");
    updateInfoForm?.classList.add("hidden");
    editContainer?.classList.remove("hidden");
    accountInfo?.classList.remove("hidden");
    divider?.classList.remove("hidden");
    mainProducts?.classList.remove("hidden");
  });

  // File rendering logic
  function renderFile(event) {
    const container = document.getElementById("file-container");
    const file = event.target.files[0];

    if (!container) return;

    // Remove existing label and preview
    container.querySelector("label")?.remove();
    container.querySelector("img")?.remove();

    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        const img = document.createElement("img");
        img.src = e.target.result;
        img.style.width = "200px";
        img.style.height = "200px";
        img.style.borderRadius = "100px";
        container.appendChild(img);
      };
      reader.readAsDataURL(file);
    }
  }

  function renderFiles(event) {
    const container = document.getElementById("image-container");
    const files = event.target.files;

    if (!container) return;

    container.querySelector("label")?.remove();

    if (files.length) {
      Array.from(files).forEach((file) => {
        const reader = new FileReader();
        reader.onload = function (e) {
          const img = document.createElement("img");
          img.src = e.target.result;
          img.style.maxWidth = "100px";
          img.style.maxHeight = "100px";
          img.style.margin = "5px";
          container.appendChild(img);
        };
        reader.readAsDataURL(file);
      });
    } else {
      container.textContent = "No files selected";
    }
  }

  function setupPlaceHolder(value, label) {
    if (!placeHolderPlatform) {
      console.error("Placeholder platform element is missing.");
      return;
    }

    if (!placeHolderLabel || !placeHolderInput) {
      console.error("Label or input element is missing inside placeholder.");
      return;
    }

    console.log(value);
    if (value == 0) {
      // Reset the placeholder
      placeHolderPlatform.style.display = "none";
      placeHolderLabel.textContent = ""; // Clear the label
      placeHolderInput.placeholder = ""; // Clear the placeholder
      placeHolderInput.value = ""; // Clear the input value
      placeHolderInput.name = ""; // Reset the name attribute
      return;
    }

    placeHolderPlatform.style.display = "block";
    // Update the placeholder
    placeHolderLabel.textContent = label;
    placeHolderInput.placeholder = `Enter ${label} account link`;
    placeHolderInput.name = `links[${-1 * value}]`;
  }

  platformsSelector?.addEventListener("change", (e) => {
    const value = e.target.value;
    const selectedOption = e.target.options[e.target.selectedIndex];
    const label = selectedOption ? selectedOption.textContent : "";
    if (value) {
      placeHolderPlatform?.classList.remove("hidden");
      setupPlaceHolder(value, label);
    } else {
      placeHolderPlatform?.classList.add("hidden");
    }
  });

  addAccountsBtn?.addEventListener("click", () => {
    platforms?.classList.toggle("hidden");
    addAccountsBtn.textContent =
      addAccountsBtn.textContent === "Add Account" ? "Close" : "Add Account";
  });

  document
    .getElementById("editAccountsButton")
    ?.addEventListener("click", function () {
      const isHidden = editMode?.classList.contains("hidden");
      addAccountsBtn?.classList.toggle("hidden");
      editMode?.classList.toggle("hidden");
      viewMode?.classList.toggle("hidden");
      this.textContent = isHidden ? "Cancel" : "Edit";
    });

  cancelButton?.addEventListener("click", () => {
    window.location.href = "http://localhost:3000/pages/store-info.php";
  });

  document
    .getElementById("file-upload")
    ?.addEventListener("change", renderSelectedFiles);

  function renderSelectedFiles(event) {
    const container = document.getElementById("image-container");
    const files = event.target.files;

    if (!container) return;

    // Clear any existing previews
    container.querySelectorAll("img").forEach((img) => img.remove());

    // Render file previews
    if (files.length) {
      Array.from(files).forEach((file) => {
        const reader = new FileReader();
        reader.onload = function (e) {
          const img = document.createElement("img");
          img.src = e.target.result;
          img.style.maxWidth = "150px";
          img.style.maxHeight = "150px";
          img.style.margin = "5px";
          container.appendChild(img);
        };
        reader.readAsDataURL(file);
      });
    } else {
      container.textContent = "No files selected";
    }
  }

  function initializeAccountToggle() {
    const addAccountsBtn = document.getElementById("addAccountsButton");
    const platforms = document.getElementById("platforms-select");

    addAccountsBtn?.addEventListener("click", () => {
      platforms?.classList.toggle("hidden");
      addAccountsBtn.textContent =
        addAccountsBtn.textContent === "Add Account" ? "Close" : "Add Account";
    });
  }
});
