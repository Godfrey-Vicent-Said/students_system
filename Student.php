<?php
require_once 'User.php';
require_once 'Security.php';

class Student extends User {
    
    /**
     * Mbinu ya Kusajili Mwanafunzi Mpya (CRUD: Create)
     * Inajumuisha Usimbaji wa Data (Encryption)
     */
    public function register($username, $password, $reg_number, $full_name, $email, $phone) {
        try {
            // 1. Kuingiza taarifa kwanza kwenye meza ya 'users'
            $query_user = "INSERT INTO users (username, password, role) VALUES (:username, :password, 'student')";
            $stmt_user = $this->db->prepare($query_user);
            
            // Kufanya password iwe salama (Hashing)
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            
            $stmt_user->bindParam(':username', $username);
            $stmt_user->bindParam(':password', $hashed_password);
            $stmt_user->execute();
            
            // Kupata ID ya mtumiaji aliyesajiliwa sasa hivi
            $user_id = $this->db->lastInsertId();

            // 2. Kusimba (Encrypt) Data Nyeti za Mwanafunzi kabla ya kuzihifadhi
            $encrypted_name = Security::encrypt($full_name);
            $encrypted_email = Security::encrypt($email);
            $encrypted_phone = Security::encrypt($phone);

            // 3. Kuingiza taarifa kwenye meza ya 'students'
            $query_student = "INSERT INTO students (user_id, reg_number, full_name_text, email_text, phone_text) 
                              VALUES (:user_id, :reg_number, :full_name, :email, :phone)";
            
            $stmt_student = $this->db->prepare($query_student);
            $stmt_student->bindParam(':user_id', $user_id);
            $stmt_student->bindParam(':reg_number', $reg_number);
            $stmt_student->bindParam(':full_name', $encrypted_name);
            $stmt_student->bindParam(':email', $encrypted_email);
            $stmt_student->bindParam(':phone', $encrypted_phone);
            
            return $stmt_student->execute();

        } catch (PDOException $e) {
            // Unaweza kurekodi hili kosa (Error Logging)
            return false;
        }
    }

    /**
     * Mbinu ya Kusoma Taarifa za Mwanafunzi (CRUD: Read & Decrypt)
     */
    public function getProfile($user_id) {
        $query = "SELECT * FROM students WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        
        $student = $stmt->fetch();
        
        if ($student) {
            // Kufungua data (Decrypt) ili mtumiaji aliyeidhinishwa azione zikiwa wazi
            $student['full_name'] = Security::decrypt($student['full_name_text']);
            $student['email'] = Security::decrypt($student['email_text']);
            $student['phone'] = Security::decrypt($student['phone_text']);
        }
        
        return $student;
    }
}
?>