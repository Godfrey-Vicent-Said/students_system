<?php
require_once 'User.php';
require_once 'Security.php';

class Admin extends User {

    /**
     * Mbinu ya kuongeza kozi mpya (CRUD: Create)
     */
    public function addCourse($course_code, $course_name) {
        $query = "INSERT INTO courses (course_code, course_name) VALUES (:code, :name)";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':code', $course_code);
        $stmt->bindParam(':name', $course_name);
        return $stmt->execute();
    }

    /**
     * Mbinu ya kuona wanafunzi wote na kufungua data zao (CRUD: Read & Decrypt)
     * Inajumuisha sehemu ya KUTAFUTA (Search Functionality)
     */
    public function getAllStudents($search_query = "") {
        // Kama kuna search, tunatafuta kwa Reg Number (ambayo haiko encrypted)
        if (!empty($search_query)) {
            $query = "SELECT * FROM students WHERE reg_number LIKE :search";
            $stmt = $this->db->prepare($query);
            $search_param = "%" . $search_query . "%";
            $stmt->bindParam(':search', $search_param);
        } else {
            $query = "SELECT * FROM students";
            $stmt = $this->db->prepare($query);
        }
        
        $stmt->execute();
        $students = $stmt->fetchAll();

        // Kufungua data (Decrypt) za wanafunzi wote waliopatikana kwa ajili ya ripoti
        foreach ($students as &$student) {
            $student['full_name'] = Security::decrypt($student['full_name_text']);
            $student['email'] = Security::decrypt($student['email_text']);
            $student['phone'] = Security::decrypt($student['phone_text']);
        }

        return $students;
    }

    /**
     * Mbinu ya kutoa Takwimu/Ripoti fupi (Reporting Feature)
     */
    public function getSystemStats() {
        $stats = [];
        
        // Idadi ya wanafunzi
        $res = $this->db->query("SELECT COUNT(*) as total FROM students");
        $stats['total_students'] = $res->fetch()['total'];

        // Idadi ya kozi
        $res = $this->db->query("SELECT COUNT(*) as total FROM courses");
        $stats['total_courses'] = $res->fetch()['total'];

        return $stats;
    }
}
?>