<?php
class Database {
    private $host = "localhost";
    private $db_name = "student_registration_db";
    private $username = "root"; // Badilisha kulingana na mazingira yako
    private $password = "";     // Badilisha kulingana na mazingira yako
    private $conn;

    /**
     * Mbinu ya kuanzisha muunganiko wa Database kupitia PDO
     */
    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            // Kuweka mfumo wa makosa (Error Mode) wa PDO uonyeshe Exception
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // Kuweka muundo chaguomsingi wa kuchukua data kama Object au Associative Array
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            echo "Muunganiko wa Database umeshindwa: " . $exception->getMessage();
        }

        return $this->conn;
    }
}
?>