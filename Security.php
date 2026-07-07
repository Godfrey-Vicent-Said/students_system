<?php
class Security {
    // Katika mazingira halisi (AWS), ufunguo huu uwekwe kwenye .env file [cite: 11, 17]
    private static $encryption_key = "K@ziY@ChuoCBE2026!SiriKubwaHapa"; 
    private static $cipher_method = "AES-256-CBC";

    /**
     * Mbinu ya kusimba data (Encryption) kabla ya kwenda kwenye Database 
     */
    public static function encrypt($data) {
        if (empty($data)) return $data;
        
        // Kutengeneza Initialization Vector (IV) ya urefu unaotakiwa
        $iv_length = openssl_cipher_iv_length(self::$cipher_method);
        $iv = openssl_random_pseudo_bytes($iv_length);
        
        // Kusimba data
        $encrypted_data = openssl_encrypt($data, self::$cipher_method, self::$encryption_key, 0, $iv);
        
        // Kuchanganya IV na Data iliyosimbwa kwa kutumia base64 ili iweze kuhifadhiwa vizuri kama TEXT
        return base64_encode($iv . "::" . $encrypted_data);
    }

    /**
     * Mbinu ya kufungua data (Decryption) wakati wa kuisoma kutoka kwenye Database 
     */
    public static function decrypt($data) {
        if (empty($data)) return $data;
        
        // Kurudisha data kutoka kwenye base64
        $raw_data = base64_decode($data);
        
        // Kutenganisha IV na Data iliyosimbwa
        if (strpos($raw_data, "::") !== false) {
            list($iv, $encrypted_text) = explode("::", $raw_data, 2);
            $iv_length = openssl_cipher_iv_length(self::$cipher_method);
            
            // Kufungua kodi
            return openssl_decrypt($encrypted_text, self::$cipher_method, self::$encryption_key, 0, $iv);
        }
        return false; // Ikishindikana kufunguka
    }
}

// === MFANO WA JINSI INAVYOFANYA KAZI (Unaweza kuifuta hii baadaye) ===
// $jina_asili = "Juma Kapuya";
// $jina_lililosimbwa = Security::encrypt($jina_asili);
// echo "Lilizosimbwa: " . $jina_lililosimbwa . "<br>";
// echo "Lililofunguliwa: " . Security::decrypt($jina_lililosimbwa);
?>