document.addEventListener('DOMContentLoaded', function() {
    const uploadBtn = document.querySelector('.btn-upload');
    const documentCards = document.querySelectorAll('.document-card');
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

    // Handle document upload
    uploadBtn.addEventListener('click', function() {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = '.pdf,.jpg,.jpeg,.png';
        
        input.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Check file size (5MB limit)
                if (file.size > 5 * 1024 * 1024) {
                    showNotification('File size exceeds 5MB limit', 'error');
                    return;
                }

                // Here you would typically upload the file to your server
                // and add the new document to the documents list
                console.log('Document uploaded:', file.name);
                
                // Show success message
                showNotification('Document uploaded successfully!', 'success');
            }
        });
        
        input.click();
    });

    // Handle document actions
    documentCards.forEach(card => {
        const viewBtn = card.querySelector('.btn-view');
        const deleteBtn = card.querySelector('.btn-delete');

        viewBtn.addEventListener('click', function() {
            const documentName = card.querySelector('h3').textContent;
            // Here you would typically open the document in a new window
            // or show it in a modal
            console.log('Viewing document:', documentName);
        });

        deleteBtn.addEventListener('click', function() {
            const documentName = card.querySelector('h3').textContent;
            if (confirm(`Are you sure you want to delete ${documentName}?`)) {
                // Here you would typically send a request to your server
                // to delete the document
                card.remove();
                showNotification('Document deleted successfully!', 'success');
            }
        });
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