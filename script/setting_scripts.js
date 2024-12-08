document.addEventListener('DOMContentLoaded', function () {
    // Function to update the URL without reloading the page
    function updateURL(sectionName) {
        const url = new URL(window.location);
        url.searchParams.set('section', sectionName); // Set the section query parameter
        history.pushState(null, null, url); // Update the URL
    }

    // Function to activate a section based on its data-section attribute
    function activateSection(section) {
        if (section) {
            // Hide all sections first
            document.querySelectorAll('.section-container').forEach(sectionContainer => {
                sectionContainer.classList.add('hidden'); // Hide all sections
                sectionContainer.classList.remove('active'); // Ensure no section stays active
            });

            // Show the relevant section
            const activeSection = document.querySelector(`.${section}-section`);
            if (activeSection) {
                activeSection.classList.remove('hidden');
                activeSection.classList.add('active');
                console.log(`Showing section: ${section}`); // Debug: Check if section is shown
                updateURL(section); // Update the URL with the section name
            } else {
                console.log(`No matching section found for: ${section}`); // Debug: Check if section is found
            }

            // Update the active class in the sidebar
            document.querySelectorAll('.sidebar-item').forEach(item => {
                if (item.getAttribute('data-section') === section) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });
        }
    }

    // Add event listeners to sidebar items
    document.querySelectorAll('.sidebar-item').forEach(item => {
        item.addEventListener('click', function () {
            const sectionName = this.getAttribute('data-section'); // Get the section name
            activateSection(sectionName); // Activate the section
        });
    });

    // Add an event listener to the select element to handle changes
    const selectElement = document.getElementById('archive-select'); // Get the select element
    if (selectElement) {
        selectElement.addEventListener('change', function (event) {
            const selectedOption = event.target.options[event.target.selectedIndex]; // Get the selected option
            const section = selectedOption.getAttribute('data-section'); // Get the data-section attribute value

            if (section) {
                activateSection(section); // Activate the section
            }
        });
    }

    // On page load, check the URL to determine the active section
    const urlParams = new URLSearchParams(window.location.search);
    const sectionFromURL = urlParams.get('section'); // Get the section from the URL query parameter

    if (sectionFromURL) {
        // Activate the section from the URL
        activateSection(sectionFromURL);

        // Synchronize the sidebar item based on the section
        document.querySelectorAll('.sidebar-item').forEach(item => {
            if (item.getAttribute('data-section') === sectionFromURL) {
                item.classList.add('active'); // Mark the sidebar item as active
            } else {
                item.classList.remove('active'); // Remove active class from other items
            }
        });

        // Synchronize the select dropdown based on the section
        const selectOption = document.querySelector(`#archive-select option[data-section="${sectionFromURL}"]`);
        if (selectOption) {
            selectOption.selected = true; // Set the selected option in the dropdown
        }
    } else {
        // Default to the first sidebar item if no section is provided in the URL
        const defaultSection = document.querySelector('.sidebar-item.active')?.getAttribute('data-section');
        if (defaultSection) {
            activateSection(defaultSection);
        }
    }

    // Listen for input events on the form fields to remove the error or success messages when the user starts typing
    document.querySelectorAll('input').forEach(input => {
        input.addEventListener('input', function () {
            ['error-message', 'success-message', 'cat-error-message', 'cat-success-message', 'type-error-message', 'type-success-message']
                .forEach(id => {
                    const element = document.getElementById(id);
                    if (element) element.style.display = 'none';
                });
        });
    });

    // Custom Cancel button functionality
    const clearBtn = document.getElementById('clearBtn');
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            ['adminForm', 'add-category-form', 'add-type-form'].forEach(formId => {
                const form = document.getElementById(formId);
                if (form) form.reset();
            });

            // Clear any error or success message if visible
            ['error-message', 'success-message', 'cat-error-message', 'cat-success-message', 'type-error-message', 'type-success-message']
                .forEach(id => {
                    const element = document.getElementById(id);
                    if (element) element.style.display = 'none';
                });
        });
    }

    // Hide messages after 3 seconds
    const messages = [
        'error-message',
        'success-message',
        'cat-error-message',
        'cat-success-message',
        'type-error-message',
        'type-success-message'
    ];

    setTimeout(() => {
        messages.forEach(id => {
            const messageElement = document.getElementById(id);
            if (messageElement) messageElement.style.display = 'none';
        });
    }, 3000); // Hide messages after 3 seconds
});
