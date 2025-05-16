document.addEventListener('DOMContentLoaded', function() {
    const passwordForm = document.querySelector('.settings-form');
    const notificationSettings = document.querySelectorAll('.notification-settings input[type="checkbox"]');
    const privacySettings = document.querySelectorAll('.privacy-settings input[type="checkbox"]');
    const deleteAccountBtn = document.querySelector('.btn-delete-account');
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

    // Handle password change
    passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const currentPassword = document.getElementById('currentPassword').value;
        const newPassword = document.getElementById('newPassword').value;
        const confirmPassword = document.getElementById('confirmPassword').value;

        if (newPassword !== confirmPassword) {
            showNotification('New passwords do not match', 'error');
            return;
        }

        // Here you would typically send the password change request to your server
        const passwordData = {
            currentPassword,
            newPassword
        };

        console.log('Password changed:', passwordData);
        showNotification('Password updated successfully!', 'success');
        
        // Clear the form
        passwordForm.reset();
    });

    // Handle notification settings
    notificationSettings.forEach(setting => {
        setting.addEventListener('change', function() {
            const settingName = this.nextElementSibling.textContent.trim();
            const isEnabled = this.checked;

            // Here you would typically send the setting update to your server
            console.log(`${settingName} notifications ${isEnabled ? 'enabled' : 'disabled'}`);
            showNotification(`${settingName} notifications ${isEnabled ? 'enabled' : 'disabled'}`, 'success');
        });
    });

    // Handle privacy settings
    privacySettings.forEach(setting => {
        setting.addEventListener('change', function() {
            const settingName = this.nextElementSibling.textContent.trim();
            const isEnabled = this.checked;

            // Here you would typically send the setting update to your server
            console.log(`${settingName} ${isEnabled ? 'enabled' : 'disabled'}`);
            showNotification(`${settingName} ${isEnabled ? 'enabled' : 'disabled'}`, 'success');
        });
    });

    // Handle account deletion
    deleteAccountBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
            // Here you would typically send the account deletion request to your server
            console.log('Account deletion requested');
            showNotification('Account deletion request sent. You will be logged out shortly.', 'warning');
            
            // Redirect to home page after a delay
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 3000);
        }
    });

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