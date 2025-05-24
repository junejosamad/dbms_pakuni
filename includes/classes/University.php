<?php
class University {
    protected $db;
    protected $university_id;
    protected $user_id;

    public function __construct($user_id) {
        $this->db = (new Database())->getConnection();
        $this->user_id = $user_id;
        $this->loadUniversityId();
    }

    protected function loadUniversityId() {
        try {
            $query = "SELECT id FROM university_profiles WHERE user_id = :user_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $this->user_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->university_id = $result['id'] ?? null;
        } catch (PDOException $e) {
            error_log("Error loading university ID: " . $e->getMessage());
            throw new Exception("Failed to load university information");
        }
    }

    public function getUniversityProfile() {
        try {
            $query = "SELECT * FROM university_profiles WHERE id = :university_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':university_id', $this->university_id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching university profile: " . $e->getMessage());
            throw new Exception("Failed to fetch university profile");
        }
    }

    public function getPrograms() {
        try {
            $query = "SELECT * FROM university_programs 
                     WHERE university_id = :university_id 
                     ORDER BY program_name ASC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':university_id', $this->university_id);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching programs: " . $e->getMessage());
            throw new Exception("Failed to fetch university programs");
        }
    }

    public function getApplications($status = null) {
        try {
            $query = "SELECT a.*, s.name as student_name, p.program_name 
                     FROM applications a 
                     JOIN students s ON a.student_id = s.id 
                     JOIN university_programs p ON a.program_id = p.id 
                     WHERE p.university_id = :university_id";
            
            if ($status) {
                $query .= " AND a.status = :status";
            }
            
            $query .= " ORDER BY a.created_at DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':university_id', $this->university_id);
            
            if ($status) {
                $stmt->bindParam(':status', $status);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching applications: " . $e->getMessage());
            throw new Exception("Failed to fetch applications");
        }
    }

    public function updateApplicationStatus($application_id, $status) {
        try {
            $query = "UPDATE applications a 
                     JOIN university_programs p ON a.program_id = p.id 
                     SET a.status = :status 
                     WHERE a.id = :application_id 
                     AND p.university_id = :university_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':application_id', $application_id);
            $stmt->bindParam(':university_id', $this->university_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating application status: " . $e->getMessage());
            throw new Exception("Failed to update application status");
        }
    }

    public function updateProgramDeadline($program_id, $deadline) {
        try {
            $query = "UPDATE university_programs 
                     SET admission_deadline = :deadline 
                     WHERE id = :program_id 
                     AND university_id = :university_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':deadline', $deadline);
            $stmt->bindParam(':program_id', $program_id);
            $stmt->bindParam(':university_id', $this->university_id);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error updating program deadline: " . $e->getMessage());
            throw new Exception("Failed to update program deadline");
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
            $query = "SELECT COUNT(*) as count 
                     FROM applications a 
                     JOIN university_programs p ON a.program_id = p.id 
                     WHERE p.university_id = :university_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':university_id', $this->university_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_applications'] = $result['count'];

            // Get pending applications
            $query = "SELECT COUNT(*) as count 
                     FROM applications a 
                     JOIN university_programs p ON a.program_id = p.id 
                     WHERE p.university_id = :university_id 
                     AND a.status = 'Pending'";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':university_id', $this->university_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['pending_review'] = $result['count'];

            // Get accepted applications
            $query = "SELECT COUNT(*) as count 
                     FROM applications a 
                     JOIN university_programs p ON a.program_id = p.id 
                     WHERE p.university_id = :university_id 
                     AND a.status = 'Accepted'";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':university_id', $this->university_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['accepted'] = $result['count'];

            // Get active programs
            $query = "SELECT COUNT(*) as count 
                     FROM university_programs 
                     WHERE university_id = :university_id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':university_id', $this->university_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['active_programs'] = $result['count'];

            return $stats;
        } catch (PDOException $e) {
            error_log("Error fetching dashboard stats: " . $e->getMessage());
            throw new Exception("Failed to fetch dashboard statistics");
        }
    }
} 