document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    const tooltips = document.querySelectorAll('[data-tooltip]');
    tooltips.forEach(tooltip => {
        tooltip.addEventListener('mouseover', function(e) {
            const tooltipText = this.getAttribute('data-tooltip');
            const tooltipEl = document.createElement('div');
            tooltipEl.className = 'tooltip';
            tooltipEl.textContent = tooltipText;
            document.body.appendChild(tooltipEl);
            
            const rect = this.getBoundingClientRect();
            tooltipEl.style.top = rect.bottom + 5 + 'px';
            tooltipEl.style.left = rect.left + (rect.width - tooltipEl.offsetWidth) / 2 + 'px';
        });
        
        tooltip.addEventListener('mouseout', function() {
            const tooltipEl = document.querySelector('.tooltip');
            if (tooltipEl) {
                tooltipEl.remove();
            }
        });
    });

    // Handle application status updates
    const statusButtons = document.querySelectorAll('.status-update');
    statusButtons.forEach(button => {
        button.addEventListener('click', function() {
            const applicationCard = this.closest('.application-card');
            const applicationId = applicationCard.dataset.applicationId;
            const newStatus = this.dataset.status;
            
            updateApplicationStatus(applicationId, newStatus);
        });
    });

    // Handle program deadline updates
    const deadlineButtons = document.querySelectorAll('.update-deadline');
    deadlineButtons.forEach(button => {
        button.addEventListener('click', function() {
            const deadlineCard = this.closest('.deadline-card');
            const programId = deadlineCard.dataset.programId;
            const deadlineInput = deadlineCard.querySelector('.deadline-input');
            const newDeadline = deadlineInput.value;
            
            updateProgramDeadline(programId, newDeadline);
        });
    });

    // Handle deadline form submission
    const deadlineForm = document.querySelector('.deadline-form form');
    if (deadlineForm) {
        deadlineForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            // Validate date
            const deadline = new Date(formData.get('deadline'));
            const today = new Date();
            
            if (deadline < today) {
                alert('Please select a future date for the deadline.');
                return;
            }
            
            submitDeadline(formData);
        });
    }

    // Search functionality
    const searchInput = document.querySelector('.search-bar input');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const applicationCards = document.querySelectorAll('.application-card');
            
            applicationCards.forEach(card => {
                const studentName = card.querySelector('h4').textContent.toLowerCase();
                const programName = card.querySelector('p').textContent.toLowerCase();
                
                if (studentName.includes(searchTerm) || programName.includes(searchTerm)) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }

    // Handle sidebar navigation
    const sidebarLinks = document.querySelectorAll('.sidebar-nav a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const section = this.getAttribute('href').substring(1);
            navigateToSection(section);
        });
    });
});

function updateApplicationStatus(applicationId, status) {
    const formData = new FormData();
    formData.append('action', 'update_status');
    formData.append('application_id', applicationId);
    formData.append('status', status);
    formData.append('ajax', 'true');

    fetch('university-dashboard.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            const applicationCard = document.querySelector(`[data-application-id="${applicationId}"]`);
            const statusElement = applicationCard.querySelector('.application-status');
            statusElement.className = `application-status ${status.toLowerCase()}`;
            statusElement.querySelector('span').textContent = status;
            
            // Show success message
            showNotification('Application status updated successfully', 'success');
        } else {
            showNotification(data.message || 'Failed to update application status', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while updating the status', 'error');
    });
}

function updateProgramDeadline(programId, deadline) {
    const formData = new FormData();
    formData.append('action', 'update_deadline');
    formData.append('program_id', programId);
    formData.append('deadline', deadline);
    formData.append('ajax', 'true');

    fetch('university-dashboard.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update UI
            const deadlineCard = document.querySelector(`[data-program-id="${programId}"]`);
            const dateElement = deadlineCard.querySelector('.deadline-date span');
            dateElement.textContent = new Date(deadline).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
            
            // Show success message
            showNotification('Program deadline updated successfully', 'success');
        } else {
            showNotification(data.message || 'Failed to update program deadline', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while updating the deadline', 'error');
    });
}

function submitDeadline(formData) {
    fetch('api/manage-deadlines.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Add new deadline to the list
            const deadlineList = document.querySelector('.deadline-list');
            const newDeadline = createDeadlineElement(data.deadline);
            deadlineList.insertBefore(newDeadline, deadlineList.firstChild);
            
            // Reset form
            formData.get('form').reset();
            
            // Show success message
            showNotification('Deadline added successfully', 'success');
        } else {
            showNotification('Failed to add deadline', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while adding the deadline', 'error');
    });
}

function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.textContent = message;
    
    // Add to document
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    // Remove notification after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

function createDeadlineElement(deadline) {
    const element = document.createElement('div');
    element.className = 'deadline-card';
    element.innerHTML = `
        <div class="deadline-info">
            <h4>${deadline.program}</h4>
            <p>${deadline.term} Admission</p>
        </div>
        <div class="deadline-date">
            <i class="fas fa-calendar"></i>
            <span>${new Date(deadline.date).toLocaleDateString()}</span>
        </div>
    `;
    return element;
}

function navigateToSection(section) {
    // Remove active class from all sections
    document.querySelectorAll('.dashboard-section').forEach(s => {
        s.classList.remove('active');
    });
    
    // Add active class to selected section
    const selectedSection = document.getElementById(section);
    if (selectedSection) {
        selectedSection.classList.add('active');
    }
    
    // Update sidebar active state
    document.querySelectorAll('.sidebar-nav a').forEach(link => {
        link.classList.remove('active');
        if (link.getAttribute('href') === `#${section}`) {
            link.classList.add('active');
        }
    });
} 