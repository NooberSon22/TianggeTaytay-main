const progressbarFill = document.querySelector(".progress-bar-fill");

const heroContent = {
  default: {
    title: "Register",
    description: "Create your seller account to post and market your products!",
  },
  5: {
    title: "We’re Excited to Have You Onboard!",
    description: "",
  },
};

function updateHeroContent(step) {
  const heroTitle = document.getElementById("hero-title");
  const heroDescription = document.getElementById("hero-description");
  const content = heroContent[step] || heroContent.default;
  heroTitle.textContent = content.title;
  heroDescription.textContent = content.description;
}

function showError(message) {
  const errorContainer = document.getElementById("form-errors");

  const errorItem = document.createElement("div");
  errorItem.classList.add("error-message");
  errorItem.textContent = message;

  // Append the error message to the container
  errorContainer.appendChild(errorItem);
}


function nextStep(currentStep) {
  var currentForm = document.getElementById("step" + currentStep);
  var nextForm = document.getElementById("step" + (currentStep + 1));
  const progressDiv = document.querySelector(
    `.progress-sub-container div:nth-of-type(${currentStep}) > div`
  );

  progressDiv.classList.add("progress-active");
  progressbarFill.style.width = (currentStep / 4) * 100 + "%";

  var allValid = true;

  // Step-specific validation
  if (currentStep === 1) {
    const username = document.querySelector('[name="username"]');
    const email = document.querySelector('[name="email"]');
    const password = document.querySelector("#password");
    const confirmPassword = document.querySelector("#confirmPassword");

    if (!username.value.trim()) {
      showError(username, "Username is required.");
      allValid = false;
    }
    if (!email.value.match(/^\S+@\S+\.\S+$/)) {
      showError(email, "Please enter a valid email address.");
      allValid = false;
    }

    if (password.value !== confirmPassword.value) {
      showError(confirmPassword, "Passwords do not match.");
      allValid = false;
    }

    if (password.value.length < 8) {
      showError(password, "The password should have at least 8 characters.");
      allValid = false;
    }
  }

  // File upload validation (if needed in step 3)
  if (currentStep === 3) {
    const fileInput = document.querySelector('[name="permit"]');
    if (fileInput.files.length === 0) {
      alert("Please upload a business permit.");
      allValid = false;
    } else {
      const file = fileInput.files[0];
      const allowedTypes = ["image/jpeg", "image/png", "application/pdf"];
      if (!allowedTypes.includes(file.type)) {
        alert("Invalid file type. Only JPG, PNG, or PDF are allowed.");
        allValid = false;
      }
    }
  }

  if (allValid) {
    currentForm.classList.remove("active");
    nextForm.classList.add("active");
    console.log(`Showing step ${currentStep + 1}`);
    updateHeroContent(currentStep + 1);

    // Set focus to the first input in the next step
    const firstInput = nextForm.querySelector("input, select, textarea");
    if (firstInput) firstInput.focus();
  }
}

function previousStep(currentStep) {
  var currentForm = document.getElementById("step" + currentStep);
  var previousForm = document.getElementById("step" + (currentStep - 1));

  progressbarFill.style.width = ((currentStep - 2) / 4) * 100 + "%";
  const progressDiv = document.querySelector(
    `.progress-sub-container div:nth-of-type(${currentStep - 1}) > div`
  );
  progressDiv.classList.remove("progress-active");

  currentForm.classList.remove("active");
  previousForm.classList.add("active");

  // Update hero section visibility
  updateHeroContent(currentStep - 1);
}

function submitRegistrationForm(formData) {
  // Clear previous error messages
  const errorContainer = document.getElementById("form-errors");
  errorContainer.innerHTML = "";

  fetch("../server/save_registration.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.status === "success") {
        // Hide all steps
        for (let i = 1; i <= 4; i++) {
          document.getElementById("step" + i)?.classList.remove("active");
        }
        // Show Step 5
        document.getElementById("step5").classList.add("active");
        updateHeroContent(5);
      } else if (data.errors) {
        // Display specific errors returned by the server
        for (let field in data.errors) {
          showError(data.errors[field]);
        }
      } else {
        alert("Registration failed. Please try again.");
      }
    })
    .catch((error) => {
      console.error("Error:", error);
      alert("An error occurred. Please try again.");
    });
}





document
  .getElementById("registrationForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();
    var formData = new FormData(this);

    // Add Lazada and Shopee links explicitly to the FormData (if not already)
    const lazada = document.querySelector('[name="lazada"]');
    const shopee = document.querySelector('[name="shopee"]');
    formData.append("lazada", lazada ? lazada.value : "");
    formData.append("shopee", shopee ? shopee.value : "");

    submitRegistrationForm(formData);
  });
