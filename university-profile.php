<?php
require_once 'config/session.php';
require_once 'university-dashboard-functions.php';

// Check if user is logged in and is a university
require_auth();
require_role('university');

// Initialize dashboard
$dashboard = new UniversityDashboard($_SESSION['user_id']);

// Get user information from session
$user_name = $_SESSION['user_name'] ?? 'University Representative';
$university_info = $dashboard->getUniversityInfo();
if (!$university_info) {
    $university_info = [
        'name' => 'Your University',
        'representative_name' => $user_name
    ];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("Form submitted with POST data: " . print_r($_POST, true));
    error_log("Files data: " . print_r($_FILES, true));
    
    try {
        $update_data = [
            'name' => $_POST['name'],
            'representative_name' => $_POST['representative_name'],
            'location' => $_POST['location'],
            'address' => $_POST['address'],
            'phone' => $_POST['phone'],
            'email' => $_POST['email'],
            'website' => $_POST['website'],
            'description' => $_POST['description']
        ];
        
        error_log("Prepared update data: " . print_r($update_data, true));
        
        // Handle logo upload
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            error_log("Processing logo upload");
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            $max_size = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($_FILES['logo']['type'], $allowed_types)) {
                $error_message = "Invalid file type. Please upload a JPG, PNG, or GIF image.";
                error_log("Invalid file type: " . $_FILES['logo']['type']);
            } elseif ($_FILES['logo']['size'] > $max_size) {
                $error_message = "File is too large. Maximum size is 5MB.";
                error_log("File too large: " . $_FILES['logo']['size']);
            } else {
                $file_extension = pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION);
                $new_filename = 'university_' . $dashboard->getUniversityId() . '_' . time() . '.' . $file_extension;
                $upload_path = 'uploads/university_logos/' . $new_filename;
                
                error_log("Attempting to move uploaded file to: " . $upload_path);
                
                if (move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                    $update_data['logo_url'] = $upload_path;
                    error_log("Logo uploaded successfully");
                } else {
                    $error_message = "Failed to upload image. Please try again.";
                    error_log("Failed to move uploaded file");
                }
            }
        }
        
        if (!isset($error_message)) {
            error_log("Attempting to update university profile");
            if ($dashboard->updateUniversityProfile($update_data)) {
                $success_message = "Profile updated successfully!";
                error_log("Profile update successful");
                // Refresh university info
                $university_info = $dashboard->getUniversityInfo();
            } else {
                $error_message = "Failed to update profile.";
                error_log("Profile update failed");
            }
        }
    } catch (PDOException $e) {
        $error_message = "Error updating profile: " . $e->getMessage();
        error_log("Exception in profile update: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University Profile - PakUni</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/university-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .profile-container {
            padding: 20px;
            max-width: 800px;
            margin: 0 auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .profile-form {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }

        .form-group textarea {
            min-height: 100px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        .logo-preview {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
            border: 3px solid #4a90e2;
        }

        .logo-upload {
            margin-bottom: 20px;
        }

        .logo-upload label {
            display: block;
            margin-bottom: 10px;
        }

        .logo-upload input[type="file"] {
            display: none;
        }

        .logo-upload .upload-btn {
            display: inline-block;
            padding: 10px 20px;
            background: #4a90e2;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.3s;
        }

        .logo-upload .upload-btn:hover {
            background: #357abd;
        }

        .submit-btn {
            background: #4a90e2;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: background 0.3s;
        }

        .submit-btn:hover {
            background: #357abd;
        }

        .error-message {
            color: #dc3545;
            margin-top: 5px;
            font-size: 0.9em;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="user-profile">
                <img src="<?php echo htmlspecialchars($university_info['logo_url'] ?? 'https://via.placeholder.com/100'); ?>" 
                     alt="University Logo" 
                     class="profile-pic">
                <h3><?php echo htmlspecialchars($user_name); ?></h3>
                <p>University Representative</p>
                <p class="university-name"><?php echo htmlspecialchars($university_info['name']); ?></p>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="university-dashboard.php"><i class="fas fa-home"></i> Overview</a></li>
                    <li><a href="applications.php"><i class="fas fa-file-alt"></i> Applications</a></li>
                    <li><a href="document-verification.php"><i class="fas fa-file-upload"></i> Document Verification</a></li>
                    <li><a href="deadlines.php"><i class="fas fa-calendar"></i> Manage Deadlines</a></li>
                    <li><a href="programs.php"><i class="fas fa-graduation-cap"></i> Programs</a></li>
                    <li><a href="university-profile.php" class="active"><i class="fas fa-university"></i> University Profile</a></li>
                    <li><a href="settings.php"><i class="fas fa-cog"></i> Settings</a></li>
                </ul>
            </nav>
        </aside>

        <main class="profile-container">
            <div class="page-header">
                <h2>University Profile</h2>
            </div>

            <?php if (isset($success_message)): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($error_message)): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>

            <form class="profile-form" method="POST" enctype="multipart/form-data">
                <div class="logo-upload">
                    <img src="<?php echo htmlspecialchars($university_info['logo_url'] ?? 'https://via.placeholder.com/150'); ?>" 
                         alt="University Logo" 
                         class="logo-preview" 
                         id="logoPreview">
                    <label for="logo" class="upload-btn">
                        <i class="fas fa-upload"></i> Upload Logo
                    </label>
                    <input type="file" 
                           id="logo" 
                           name="logo" 
                           accept="image/jpeg,image/png,image/gif"
                           onchange="previewLogo(this)">
                </div>

                <div class="form-group">
                    <label for="name">University Name *</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($university_info['name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="representative_name">Representative Name *</label>
                    <input type="text" id="representative_name" name="representative_name" value="<?php echo htmlspecialchars($university_info['representative_name'] ?? ''); ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="location">Location *</label>
                        <input type="text" id="location" name="location" value="<?php echo htmlspecialchars($university_info['location'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number *</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($university_info['phone'] ?? ''); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"><?php echo htmlspecialchars($university_info['address'] ?? ''); ?></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email Address *</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($university_info['email'] ?? ''); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="website">Website</label>
                        <input type="url" id="website" name="website" value="<?php echo htmlspecialchars($university_info['website'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">University Description</label>
                    <textarea id="description" name="description"><?php echo htmlspecialchars($university_info['description'] ?? ''); ?></textarea>
                </div>

                <button type="submit" class="submit-btn">
                    <i class="fas fa-save"></i> Save Changes
                </button>
            </form>
        </main>
    </div>

    <script>
        function previewLogo(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logoPreview').src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html> 