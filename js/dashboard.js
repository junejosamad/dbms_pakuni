document.addEventListener('DOMContentLoaded', () => {
    // Handle sidebar navigation
    const sidebarLinks = document.querySelectorAll('.sidebar-nav a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const target = link.getAttribute('href').substring(1);
            
            // Remove active class from all links
            sidebarLinks.forEach(l => l.classList.remove('active'));
            
            // Add active class to clicked link
            link.classList.add('active');
            
            // Navigate to the appropriate page based on the target
            switch(target) {
                case 'overview':
                    // Already on overview page
                    break;
                case 'applications':
                    window.location.href = 'my-applications.php';
                    break;
                case 'documents':
                    window.location.href = 'documents.php';
                    break;
                case 'profile':
                    window.location.href = 'profile.php';
                    break;
                case 'settings':
                    window.location.href = 'settings.php';
                    break;
            }
        });
    });

    // Handle search functionality
    const searchInput = document.querySelector('.search-bar input');
    searchInput.addEventListener('input', (e) => {
        const searchTerm = e.target.value.toLowerCase();
        // Here you would typically filter the dashboard content based on the search term
        console.log(`Searching for: ${searchTerm}`);
    });

    // Handle logout
    const logoutButton = document.querySelector('.btn-logout');
    if (logoutButton) {
        logoutButton.addEventListener('click', (e) => {
            e.preventDefault();
            // Here you would typically handle the logout process
            console.log('Logging out...');
            // Redirect to login page
            window.location.href = 'login.php';
        });
    }

    // Handle application card clicks
    const applicationCards = document.querySelectorAll('.application-card');
    applicationCards.forEach(card => {
        card.addEventListener('click', () => {
            const universityName = card.querySelector('h4').textContent;
            console.log(`Viewing application for: ${universityName}`);
            // Navigate to application details page
            window.location.href = 'application-details.php';
        });
    });

    // Handle deadline card clicks
    const deadlineCards = document.querySelectorAll('.deadline-card');
    deadlineCards.forEach(card => {
        card.addEventListener('click', () => {
            const universityName = card.querySelector('h4').textContent;
            console.log(`Viewing deadline for: ${universityName}`);
            // Navigate to university's application page
            window.location.href = 'apply.php';
        });
    });

    // Simulate loading data
    loadDashboardData();
});

function loadDashboardData() {
    // This would typically be an API call to fetch the user's dashboard data
    console.log('Loading dashboard data...');
    
    // Simulate loading data with a timeout
    setTimeout(() => {
        console.log('Dashboard data loaded');
        // Here you would typically update the UI with the fetched data
    }, 1000);
}

// Function to format dates
function formatDate(dateString) {
    const options = { year: 'numeric', month: 'long', day: 'numeric' };
    return new Date(dateString).toLocaleDateString('en-US', options);
}

// Function to calculate days until deadline
function daysUntilDeadline(deadlineDate) {
    const today = new Date();
    const deadline = new Date(deadlineDate);
    const diffTime = deadline - today;
    const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
    return diffDays;
}

// Function to update application status
function updateApplicationStatus(applicationId, newStatus) {
    // This would typically be an API call to update the application status
    console.log(`Updating application ${applicationId} status to: ${newStatus}`);
    
    // Simulate updating status with a timeout
    setTimeout(() => {
        console.log('Application status updated');
        // Here you would typically update the UI to reflect the new status
    }, 1000);
}

// Function to handle file uploads
function handleFileUpload(file, type) {
    // This would typically handle file uploads for documents
    console.log(`Uploading ${type} file:`, file.name);
    
    // Simulate file upload with a timeout
    setTimeout(() => {
        console.log('File uploaded successfully');
        // Here you would typically update the UI to show the uploaded file
    }, 1500);
} 