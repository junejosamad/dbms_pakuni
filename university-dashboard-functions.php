<?php
require_once 'config/database.php';
require_once 'config/session.php';
require_once 'includes/functions/auth.php';

class UniversityDashboard {
    private $db;
    private $university_id;
    private $user_id;

    public function __construct($user_id) {
        $database = new Database();
        $this->db = $database->getConnection();
        $this->user_id = $user_id;
        $this->university_id = $this->getUniversityId();
    }

    private function getUniversityId() {
        $query = "SELECT id FROM university_profiles WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":user_id", $this->user_id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['id'] : null;
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
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":university_id", $this->university_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_applications'] = $result['total'] ?? 0;

            // Get pending applications
            $query = "SELECT COUNT(*) as pending FROM applications WHERE university_id = :university_id AND status = 'Pending'";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":university_id", $this->university_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['pending_review'] = $result['pending'] ?? 0;

            // Get accepted applications
            $query = "SELECT COUNT(*) as accepted FROM applications WHERE university_id = :university_id AND status = 'Accepted'";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":university_id", $this->university_id);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['accepted'] = $result['accepted'] ?? 0;

            // Get active programs
            $query = "SELECT COUNT(*) as active FROM programs WHERE university_id = :university_id AND status = 'Active'";
            $stmt = $this->db->prepare($query);
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
            $query = "SELECT a.*, s.name as student_name, p.name as program_name 
                     FROM applications a 
                     JOIN students s ON a.student_id = s.id 
                     JOIN programs p ON a.program_id = p.id 
                     WHERE a.university_id = :university_id 
                     ORDER BY a.submitted_at DESC 
                     LIMIT :limit";
            
            $stmt = $this->db->prepare($query);
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
            $query = "SELECT id, name as program_name, admission_deadline 
                     FROM programs 
                     WHERE university_id = :university_id 
                     ORDER BY admission_deadline ASC";
            
            $stmt = $this->db->prepare($query);
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
            
            $stmt = $this->db->prepare($query);
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
                     SET status = :status, updated_at = NOW() 
                     WHERE id = :application_id AND university_id = :university_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":application_id", $application_id);
            $stmt->bindParam(":university_id", $this->university_id);
            
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Error updating application status: " . $e->getMessage());
            return false;
        }
    }

    public function updateProgramDeadline($program_id, $deadline) {
        try {
            $query = "UPDATE programs 
                     SET admission_deadline = :deadline, updated_at = NOW() 
                     WHERE id = :program_id AND university_id = :university_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":deadline", $deadline);
            $stmt->bindParam(":program_id", $program_id);
            $stmt->bindParam(":university_id", $this->university_id);
            
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
                     WHERE up.university_id = :university_id
                     AND (u.name LIKE :search_term 
                          OR up.program_name LIKE :search_term
                          OR sp.phone LIKE :search_term)
                     ORDER BY a.submitted_at DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(":university_id", $this->university_id);
            $stmt->bindParam(":search_term", $search_term);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error searching applications: " . $e->getMessage());
            return [];
        }
    }
}
?> 