<?php
class User {
    // Encapsulation: Kutumia 'protected' ili madarasa yaliyorithi yaweze kuzifikia sifa hizi
    protected $db;
    protected $id;
    protected $username;
    protected $role;

    // Constructor: Inajiendesha yenyewe kila kitu (object) kinapotengenezwa
    public function __construct($db_conn) {
        $this->db = $db_conn;
    }

    /**
     * Mbinu ya Kuingia Kwenye Mfumo (Login)
     */
    public function login($username, $password) {
        $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch();
            // Kuhakiki kama nenosiri (password) limefanana na hash iliyopo kwenye DB
            if (password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->username = $row['username'];
                $this->role = $row['role'];
                
                // Kuanzisha Session (Session Management)
                if (session_status() == PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['user_id'] = $this->id;
                $_SESSION['username'] = $this->username;
                $_SESSION['role'] = $this->role;
                
                return $this->role; // Inarudisha 'admin' au 'student'
            }
        }
        return false;
    }

    /**
     * Mbinu ya kutoka kwenye mfumo (Logout)
     */
    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_unset();
        session_destroy();
        return true;
    }
}
?>