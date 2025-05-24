<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions/auth.php';

class UniversityDashboard {
    private $conn;
    private $university_id;
    private $user_id;

    public function __construct($user_id) {
        error_log("UniversityDashboard constructor called with user_id: " . $user_id);
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->user_id = $user_id;
        $this->university_id = $this->getUniversityId();
        error_log("Constructor completed. university_id: " . $this->university_id);
    }

    public function getUniversityId() {
        try {
            $query = "SELECT id FROM university_profiles WHERE user_id = ?";
            $stmt = $this->conn->prepare($query);
            $stmt->execute([$this->user_id]);
            $university_id = $stmt->fetchColumn();
            return $university_id ? $university_id : null;
        } catch (Exception $e) {
            error_log("Error getting university ID: " . $e->getMessage());
            return null;
        }
    }

    public function getDashboardStats() {
        try {
            $stats = [
                'total_applications' => 0,
                'pending_review' => 0,
                'accepted' => 0,
                'active_programs' => 0
            ];

            // Get total applications
            $query = "SELECT COUNT(*) as total FROM applications WHERE university_id = :university_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":university_id", $this->university_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_applications'] = $result['total'] ?? 0;

            // Get pending applications
            $query = "SELECT COUNT(*) as pending FROM applications WHERE university_id = :university_id AND status = 'Pending'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":university_id", $this->university_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['pending_review'] = $result['pending'] ?? 0;

            // Get accepted applications
            $query = "SELECT COUNT(*) as accepted FROM applications WHERE university_id = :university_id AND status = 'Accepted'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":university_id", $this->university_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['accepted'] = $result['accepted'] ?? 0;

            // Get active programs
            $query = "SELECT COUNT(*) as active FROM university_programs WHERE university_id = :university_id AND status = 'active'";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":university_id", $this->university_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['active_programs'] = $result['active'] ?? 0;

            return $stats;
        } catch (Exception $e) {
            error_log("Error getting dashboard stats: " . $e->getMessage());
            return null;
        }
    }

    public function getRecentApplications($limit = 5) {
        try {
            $query = "SELECT a.*, s.name as student_name, p.program_name 
                     FROM applications a 
                     JOIN students s ON a.student_id = s.id 
                     JOIN university_programs p ON a.program_id = p.id 
                     WHERE a.university_id = :university_id 
                     ORDER BY a.submitted_at DESC 
                     LIMIT :limit";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":university_id", $this->university_id);
            $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting recent applications: " . $e->getMessage());
            return [];
        }
    }

    public function getProgramDeadlines() {
        try {
            $query = "SELECT id, program_name, admission_deadline 
                     FROM university_programs 
                     WHERE university_id = :university_id 
                     ORDER BY admission_deadline ASC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":university_id", $this->university_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting program deadlines: " . $e->getMessage());
            return [];
        }
    }

    public function getUniversityInfo() {
        try {
            $query = "SELECT name, representative_name 
                     FROM university_profiles 
                     WHERE id = :university_id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(":university_id", $this->university_id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            error_log("Error getting university info: " . $e->getMessage());
            return null;
        }
    }

    public function updateApplicationStatus($application_id, $status) {
        try {
            $query = "UPDATE applications 
                     SET status = ?, updated_at = NOW() 
                     WHERE id = ? AND university_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sii", $status, $application_id, $this->university_id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error updating application status: " . $e->getMessage());
            return false;
        }
    }

    public function updateProgramDeadline($program_id, $deadline) {
        try {
            $query = "UPDATE programs 
                     SET admission_deadline = ?, updated_at = NOW() 
                     WHERE id = ? AND university_id = ?";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("sii", $deadline, $program_id, $this->university_id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error updating program deadline: " . $e->getMessage());
            return false;
        }
    }

    public function searchApplications($search_term) {
        if (!$this->university_id) {
            return [];
        }

        try {
            $search_term = "%{$search_term}%";
            
            $query = "SELECT a.id, a.status, a.submitted_at, 
                            u.name as student_name, up.program_name,
                            sp.phone as student_phone
                     FROM applications a
                     JOIN users u ON a.student_id = u.id
                     JOIN university_programs up ON a.university_program_id = up.id
                     LEFT JOIN student_profiles sp ON u.id = sp.user_id
                     WHERE up.university_id = ?
                     AND (u.name LIKE ? 
                          OR up.program_name LIKE ?
                          OR sp.phone LIKE ?)
                     ORDER BY a.submitted_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("isss", $this->university_id, $search_term, $search_term, $search_term);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $applications = [];
            while ($row = $result->fetch_assoc()) {
                $applications[] = $row;
            }
            
            return $applications;
        } catch (Exception $e) {
            error_log("Error searching applications: " . $e->getMessage());
            return [];
        }
    }

    public function getPendingDocuments() {
        $query = "SELECT d.*, 
                        CONCAT(s.first_name, ' ', s.last_name) as student_name, 
                        p.program_name as program_name 
                 FROM documents d 
                 JOIN students s ON d.student_id = s.id 
                 JOIN applications a ON d.application_id = a.id 
                 JOIN university_programs p ON a.program_id = p.id 
                 WHERE p.university_id = :university_id AND d.status = 'pending' 
                 ORDER BY d.submission_date DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":university_id", $this->university_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateDocumentStatus($document_id, $status, $notes = '') {
        try {
            $query = "UPDATE documents 
                     SET status = ?, verification_notes = ?, verified_at = NOW() 
                     WHERE id = ? AND EXISTS (
                         SELECT 1 FROM applications a 
                         JOIN university_programs p ON a.program_id = p.id 
                         WHERE a.id = documents.application_id 
                         AND p.university_id = ?
                     )";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bind_param("ssii", $status, $notes, $document_id, $this->university_id);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error updating document status: " . $e->getMessage());
            return false;
        }
    }

    public function getDocumentStats() {
        $query = "SELECT 
                    COUNT(*) as total_documents,
                    SUM(CASE WHEN d.status = 'pending' THEN 1 ELSE 0 END) as pending_documents,
                    SUM(CASE WHEN d.status = 'verified' THEN 1 ELSE 0 END) as verified_documents,
                    SUM(CASE WHEN d.status = 'rejected' THEN 1 ELSE 0 END) as rejected_documents
                 FROM documents d
                 JOIN applications a ON d.application_id = a.id
                 JOIN university_programs p ON a.program_id = p.id
                 WHERE p.university_id = :university_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":university_id", $this->university_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUniversityPrograms() {
        $query = "SELECT * FROM university_programs WHERE university_id = :university_id ORDER BY program_name ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":university_id", $this->university_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProgramDetails($program_id) {
        $query = "SELECT * FROM university_programs WHERE id = :program_id AND university_id = :university_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":program_id", $program_id);
        $stmt->bindParam(":university_id", $this->university_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function addProgram($program_data) {
        try {
            // Get or create university ID
            $university_id = $this->getUniversityId();
            if (!$university_id) {
                error_log("Error: Could not get/create university ID");
                return false;
            }

            error_log("Adding program for university_id: " . $university_id);
            error_log("Program data: " . print_r($program_data, true));

            // Validate required fields
            if (empty($program_data['name']) || empty($program_data['admission_deadline'])) {
                error_log("Error: Missing required fields (name or admission_deadline)");
                return false;
            }

            // Format the admission deadline
            $admission_deadline = date('Y-m-d', strtotime($program_data['admission_deadline']));
            if (!$admission_deadline) {
                error_log("Error: Invalid admission deadline format");
                return false;
            }

            $query = "INSERT INTO university_programs (
                university_id, program_name, tuition_fee, ranking, admission_deadline
            ) VALUES (
                :university_id, :program_name, :tuition_fee, :ranking, :admission_deadline
            )";

            error_log("SQL Query: " . $query);

            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                error_log("Error preparing statement: " . print_r($this->conn->errorInfo(), true));
                return false;
            }
            
            // Set default values for optional fields
            $tuition_fee = $program_data['tuition_fee'] ?? 0;
            $ranking = $program_data['ranking'] ?? 0;
            
            // Bind parameters
            $stmt->bindParam(":university_id", $university_id);
            $stmt->bindParam(":program_name", $program_data['name']);
            $stmt->bindParam(":tuition_fee", $tuition_fee);
            $stmt->bindParam(":ranking", $ranking);
            $stmt->bindParam(":admission_deadline", $admission_deadline);

            // Execute the query
            $result = $stmt->execute();

            if (!$result) {
                $error = $stmt->errorInfo();
                error_log("Database error in addProgram: " . print_r($error, true));
                return false;
            }

            $new_program_id = $this->conn->lastInsertId();
            error_log("Program added successfully with ID: " . $new_program_id);
            return true;
        } catch (PDOException $e) {
            error_log("Exception in addProgram: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function updateProgram($program_id, $program_data) {
        $query = "UPDATE programs SET 
            program_name = :program_name,
            degree_level = :degree_level,
            duration = :duration,
            available_seats = :available_seats,
            fee_per_semester = :fee_per_semester,
            admission_deadline = :admission_deadline,
            description = :description,
            requirements = :requirements,
            is_active = :is_active
            WHERE id = :program_id AND university_id = :university_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":program_name", $program_data['program_name']);
        $stmt->bindParam(":degree_level", $program_data['degree_level']);
        $stmt->bindParam(":duration", $program_data['duration']);
        $stmt->bindParam(":available_seats", $program_data['available_seats']);
        $stmt->bindParam(":fee_per_semester", $program_data['fee_per_semester']);
        $stmt->bindParam(":admission_deadline", $program_data['admission_deadline']);
        $stmt->bindParam(":description", $program_data['description']);
        $stmt->bindParam(":requirements", $program_data['requirements']);
        $stmt->bindParam(":is_active", $program_data['is_active']);
        $stmt->bindParam(":program_id", $program_id);
        $stmt->bindParam(":university_id", $this->university_id);
        
        return $stmt->execute();
    }

    public function deleteProgram($program_id) {
        $query = "DELETE FROM programs WHERE id = :program_id AND university_id = :university_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":program_id", $program_id);
        $stmt->bindParam(":university_id", $this->university_id);
        return $stmt->execute();
    }

    public function getAllUniversities() {
        $query = "SELECT up.*, 
                        (SELECT COUNT(*) FROM programs WHERE university_id = up.id) as total_programs,
                        (SELECT COUNT(*) FROM applications a 
                         JOIN programs p ON a.program_id = p.id 
                         WHERE p.university_id = up.id) as total_students
                 FROM university_profiles up
                 ORDER BY up.name ASC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function ensureUniversityProfileExists() {
        try {
            error_log("ensureUniversityProfileExists - Starting check for user_id: " . $this->user_id);
            
            // Check if profile exists
            $query = "SELECT id FROM university_profiles WHERE user_id = :user_id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            error_log("ensureUniversityProfileExists - Query result: " . print_r($result, true));
            
            if (!$result) {
                error_log("ensureUniversityProfileExists - No profile found, creating new one");
                
                $query = "INSERT INTO university_profiles (user_id, name, representative_name, status) 
                         VALUES (:user_id, 'New University', :representative_name, 'active')";
                
                $stmt = $this->conn->prepare($query);
                $stmt->bindParam(':user_id', $this->user_id);
                $stmt->bindParam(':representative_name', $_SESSION['user_name']);
                
                if (!$stmt->execute()) {
                    error_log("ensureUniversityProfileExists - Failed to create profile: " . print_r($stmt->errorInfo(), true));
                    return false;
                }
                
                $this->university_id = $this->conn->lastInsertId();
                error_log("ensureUniversityProfileExists - New profile created with ID: " . $this->university_id);
            } else {
                $this->university_id = $result['id'];
                error_log("ensureUniversityProfileExists - Existing profile found with ID: " . $this->university_id);
            }
            
            return true;
        } catch (PDOException $e) {
            error_log("Error in ensureUniversityProfileExists: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }

    public function updateUniversityProfile($profile_data) {
        try {
            error_log("updateUniversityProfile - Starting update");
            error_log("updateUniversityProfile - University ID: " . $this->university_id);
            error_log("updateUniversityProfile - Profile data: " . print_r($profile_data, true));

            // Validate university_id
            if (!$this->university_id) {
                error_log("updateUniversityProfile - Error: Invalid university_id");
                return false;
            }

            // Ensure profile exists
            if (!$this->ensureUniversityProfileExists()) {
                error_log("updateUniversityProfile - Error: Failed to ensure university profile exists");
                return false;
            }

            // Validate required fields
            $required_fields = ['name', 'representative_name', 'location', 'phone', 'email'];
            foreach ($required_fields as $field) {
                if (empty($profile_data[$field])) {
                    error_log("updateUniversityProfile - Error: Missing required field: " . $field);
                    return false;
                }
            }

            $query = "UPDATE university_profiles SET 
                name = :name,
                representative_name = :representative_name,
                location = :location,
                address = :address,
                phone = :phone,
                email = :email,
                website = :website,
                description = :description,
                updated_at = CURRENT_TIMESTAMP
                WHERE id = :university_id";
            
            error_log("updateUniversityProfile - SQL Query: " . $query);
            
            $stmt = $this->conn->prepare($query);
            if (!$stmt) {
                error_log("updateUniversityProfile - Error preparing statement: " . print_r($this->conn->errorInfo(), true));
                return false;
            }

            // Bind parameters
            $stmt->bindParam(':name', $profile_data['name']);
            $stmt->bindParam(':representative_name', $profile_data['representative_name']);
            $stmt->bindParam(':location', $profile_data['location']);
            $stmt->bindParam(':address', $profile_data['address']);
            $stmt->bindParam(':phone', $profile_data['phone']);
            $stmt->bindParam(':email', $profile_data['email']);
            $stmt->bindParam(':website', $profile_data['website']);
            $stmt->bindParam(':description', $profile_data['description']);
            $stmt->bindParam(':university_id', $this->university_id);
            
            // Execute the query
            $result = $stmt->execute();
            
            if (!$result) {
                $error = $stmt->errorInfo();
                error_log("updateUniversityProfile - Database error: " . print_r($error, true));
                return false;
            }

            error_log("updateUniversityProfile - Profile updated successfully");
            return true;
            
        } catch (PDOException $e) {
            error_log("Exception in updateUniversityProfile: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return false;
        }
    }
}

// Handle AJAX requests for document verification
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_document_status') {
    $response = ['success' => false, 'message' => ''];
    
    if (isset($_POST['document_id']) && isset($_POST['status'])) {
        $dashboard = new UniversityDashboard($_SESSION['user_id']);
        $notes = $_POST['notes'] ?? '';
        
        if ($dashboard->updateDocumentStatus($_POST['document_id'], $_POST['status'], $notes)) {
            $response['success'] = true;
            $response['message'] = 'Document status updated successfully';
        } else {
            $response['message'] = 'Failed to update document status';
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?> 