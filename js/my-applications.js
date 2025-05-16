document.addEventListener('DOMContentLoaded', function() {
    const statusFilter = document.getElementById('statusFilter');
    const dateFilter = document.getElementById('dateFilter');
    const applicationCards = document.querySelectorAll('.application-card');
    const editPictureBtn = document.querySelector('.btn-edit-picture');
    const profilePicture = document.querySelector('.profile-picture img');

    // Handle profile picture change
    editPictureBtn.addEventListener('click', function() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    profilePicture.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
        
        input.click();
    });

    // Handle application filtering
    function filterApplications() {
        const selectedStatus = statusFilter.value;
        const selectedDate = dateFilter.value;

        applicationCards.forEach(card => {
            const status = card.querySelector('.status').textContent.toLowerCase();
            const applicationDate = new Date(card.querySelector('.application-details p:nth-child(2)').textContent.split(': ')[1]);

            const matchesStatus = selectedStatus === 'all' || status === selectedStatus;
            
            card.style.display = matchesStatus ? 'block' : 'none';
        });

        // Sort applications based on date
        const applicationsContainer = document.querySelector('.applications-list');
        const sortedCards = Array.from(applicationCards)
            .filter(card => card.style.display !== 'none')
            .sort((a, b) => {
                const dateA = new Date(a.querySelector('.application-details p:nth-child(2)').textContent.split(': ')[1]);
                const dateB = new Date(b.querySelector('.application-details p:nth-child(2)').textContent.split(': ')[1]);
                return selectedDate === 'newest' ? dateB - dateA : dateA - dateB;
            });

        // Reorder applications in the DOM
        sortedCards.forEach(card => {
            applicationsContainer.appendChild(card);
        });
    }

    // Add event listeners for filters
    statusFilter.addEventListener('change', filterApplications);
    dateFilter.addEventListener('change', filterApplications);

    // Handle application actions
    applicationCards.forEach(card => {
        const continueBtn = card.querySelector('.btn-continue');
        const viewBtn = card.querySelector('.btn-view');
        const downloadBtn = card.querySelector('.btn-download');
        const reapplyBtn = card.querySelector('.btn-reapply');

        if (continueBtn) {
            continueBtn.addEventListener('click', function() {
                const universityName = card.querySelector('h3').textContent;
                // Here you would typically redirect to the application form
                console.log('Continuing application for:', universityName);
                window.location.href = 'apply.php';
            });
        }

        if (viewBtn) {
            viewBtn.addEventListener('click', function() {
                const universityName = card.querySelector('h3').textContent;
                // Here you would typically show the application details in a modal
                // or redirect to a detailed view page
                console.log('Viewing application for:', universityName);
                window.location.href = 'application-details.php';
            });
        }

        if (downloadBtn) {
            downloadBtn.addEventListener('click', function() {
                const universityName = card.querySelector('h3').textContent;
                // Here you would typically trigger the download of the offer letter
                console.log('Downloading offer letter for:', universityName);
                showNotification('Offer letter download started', 'success');
            });
        }

        if (reapplyBtn) {
            reapplyBtn.addEventListener('click', function() {
                const universityName = card.querySelector('h3').textContent;
                if (confirm(`Are you sure you want to reapply to ${universityName}?`)) {
                    // Here you would typically redirect to the application form
                    console.log('Reapplying to:', universityName);
                    window.location.href = 'apply.php';
                }
            });
        }
    });

    // Update application statistics
    function updateStats() {
        const totalApplications = applicationCards.length;
        const pendingApplications = document.querySelectorAll('.status.pending').length;
        const acceptedApplications = document.querySelectorAll('.status.accepted').length;
        const rejectedApplications = document.querySelectorAll('.status.rejected').length;

        document.querySelector('.stat-card:nth-child(1) .stat-number').textContent = totalApplications;
        document.querySelector('.stat-card:nth-child(2) .stat-number').textContent = pendingApplications;
        document.querySelector('.stat-card:nth-child(3) .stat-number').textContent = acceptedApplications;
        document.querySelector('.stat-card:nth-child(4) .stat-number').textContent = rejectedApplications;
    }

    // Initialize stats
    updateStats();

    // Function to show notifications
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        // Remove notification after 3 seconds
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}); 