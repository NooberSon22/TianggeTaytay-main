
    // Get all the sidebar list items (excluding logout)
    const sidebarItems = document.querySelectorAll('.sidebar ul li:not(.logout)');

    // Loop through all sidebar items and add a click event listener
    sidebarItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove the 'active' class from all items and revert their icons to blue
            sidebarItems.forEach(i => {
                i.classList.remove('active'); // Remove 'active' class from all items
                const icon = i.querySelector('.sidebar-icon');
                const defaultIconSrc = icon.getAttribute('src').replace('-white',
                    ''); // Get the default blue icon (remove any '-white' part)
                icon.src = defaultIconSrc; // Set the icon back to the default blue
            });

            // Add the 'active' class to the clicked item and change its icon to white
            this.classList.add('active');
            const icon = this.querySelector('.sidebar-icon');
            const activeIconSrc = icon.getAttribute('data-active-src'); // Get the white icon path
            icon.src = activeIconSrc; // Set the icon to the white version
        });
    });

    // Select all items with the class 'sidebar-item'
    const settingSideBarItems = document.querySelectorAll('.sidebar-item');

    // Loop through all sidebar items and add a click event listener
    settingSideBarItems.forEach(item => {
        item.addEventListener('click', function() {
            // Remove the 'active' class from all items
            settingSideBarItems.forEach(i => {
                i.classList.remove('active');
            });

            // Add the 'active' class to the clicked item
            this.classList.add('active');
        });
    });

    document.querySelectorAll('.sidebar-item').forEach(item => {
        item.addEventListener('click', function() {
            // Remove active class from all sidebar items
            document.querySelectorAll('.sidebar-item').forEach(sidebar => sidebar.classList.remove('active'));
    
            // Add active class to the clicked sidebar item
            this.classList.add('active');
    
            // Hide all sections
            document.querySelectorAll('.section-container').forEach(section => section.classList.add('hidden'));
    
            // Show the relevant section based on the clicked sidebar item
            const sectionName = this.textContent.trim().toLowerCase().replace(' ', '-'); // e.g. 'admin' or 'categories'
            const activeSection = document.querySelector(`.${sectionName}-section`);
            if (activeSection) {
                activeSection.classList.remove('hidden');
                activeSection.classList.add('active');
            }
        });
    });
    