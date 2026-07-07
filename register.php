<?php
require_once 'Database.php';
require_once 'Student.php';

$message = "";
$message_type = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Kuchukua data kutoka kwenye fomu (Form Validation ya msingi)
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $reg_number = trim($_POST['reg_number']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);

    // Uhakiki rahisi (Hapa unaweza kuongeza kulingana na coding standards) 
    if (empty($username) || empty($password) || empty($reg_number) || empty($full_name) || empty($email) || empty($phone)) {
        $message = "Tafadhali jaza nafasi zote!";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Barua pepe (Email) haina muundo sahihi!";
        $message_type = "error";
    } else {
        // 2. Kuanzisha muunganiko wa Database na Class ya Student
        $database = new Database();
        $db = $database->connect();
        $student = new Student($db);

        // 3. Kujaribu kusajili
        if ($student->register($username, $password, $reg_number, $full_name, $email, $phone)) {
            $message = "Usajili umefanikiwa! Sasa unaweza kuingia kwenye mfumo.";
            $message_type = "success";
        } else {
            $message = "Hitilafu imetokea! Labda Username au Reg Number tayari vimeshatumika.";
            $message_type = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usajili wa Mwanafunzi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-cover bg-center h-screen flex items-center justify-center" style="background-image: url('https://images.unsplash.com/photo-1523050854058-8df90110c9f1?q=80&w=1920');">
    
    <div class="absolute inset-0 bg-black opacity-60"></div>

    <div class="relative bg-white p-8 rounded-lg shadow-2xl w-full max-w-md z-10 mx-4">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Sajili Akaunti ya Mwanafunzi</h2>
        
        <?php if (!empty($message)): ?>
            <div class="p-3 rounded mb-4 text-sm text-center <?php echo $message_type == 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form action="register.php" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-600">Jina Kamili</label>
                <input type="text" name="full_name" required class="w-full p-2 border border-gray-300 rounded mt-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="grid grid-cols-2 gap-2">
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Namba ya Usajili</label>
                    <input type="text" name="reg_number" placeholder="CBE/BIT/..." required class="w-full p-2 border border-gray-300 rounded mt-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Username</label>
                    <input type="text" name="username" required class="w-full p-2 border border-gray-300 rounded mt-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Barua Pepe (Email)</label>
                <input type="email" name="email" required class="w-full p-2 border border-gray-300 rounded mt-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Namba ya Simu</label>
                <input type="text" name="phone" required class="w-full p-2 border border-gray-300 rounded mt-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Nenosiri (Password)</label>
                <input type="password" name="password" required class="w-full p-2 border border-gray-300 rounded mt-1 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            
            <button type="submit" class="w-full bg-blue-600 text-white p-2 rounded font-bold hover:bg-blue-700 transition">Sajili Sasa</button>
        </form>

        <p class="text-sm text-center text-gray-600 mt-4">
            Tayari una akaunti? <a href="login.php" class="text-blue-600 font-bold hover:underline">Ingia Hapa</a>
        </p>
    </div>
</body>
</html>