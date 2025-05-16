document.addEventListener('DOMContentLoaded', function() {
    const profileForm = document.querySelector('.profile-form');
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
                    // Here you would typically upload the image to your server
                    // and update the user's profile picture in the database
                };
                reader.readAsDataURL(file);
            }
        });
        
        input.click();
    });

    // Handle form submission
    profileForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            fullName: document.getElementById('fullName').value,
            email: document.getElementById('email').value,
            phone: document.getElementById('phone').value,
            address: document.getElementById('address').value,
            education: document.getElementById('education').value
        };

        // Here you would typically send the formData to your server
        // using fetch or axios to update the user's profile
        console.log('Profile updated:', formData);
        
        // Show success message
        showNotification('Profile updated successfully!', 'success');
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