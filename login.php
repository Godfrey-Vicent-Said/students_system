<?php
if (session_status() == PHP_SESSION_NONE) { session_start(); }
require_once 'Database.php';

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = (new Database())->connect();
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT id, username, password, role FROM users WHERE username = :username");
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = trim($user['role']); 

        // Redirection ya Admin na Student
        if ($_SESSION['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: student_dashboard.php");
        }
        exit();
    } else {
        $error = "Username au Password siyo sahihi!";
    }
}
?>
<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <title>Ingia Mfumo</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-cover bg-center h-screen flex items-center justify-center" style="background-image: url('https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?q=80&w=1920');">
    <div class="absolute inset-0 bg-black opacity-60"></div>
    <div class="relative bg-white p-8 rounded-lg shadow-2xl w-full max-w-md z-10">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-6">Ingia Mfumo</h2>
        
        <?php if($error): ?>
            <p class="text-red-500 text-center mb-4 bg-red-100 p-2 rounded"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <input type="text" name="username" placeholder="Username" required class="w-full p-3 border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 outline-none">
            <input type="password" name="password" placeholder="Password" required class="w-full p-3 border border-gray-300 rounded focus:ring-2 focus:ring-emerald-500 outline-none">
            <button type="submit" class="w-full bg-emerald-600 text-white p-3 rounded font-bold hover:bg-emerald-700 transition duration-200">Ingia</button>
        </form>

        <div class="mt-4 text-center">
            <p class="text-sm text-gray-600">
                Huna akaunti? 
                <a href="register.php" class="text-emerald-600 font-bold hover:underline">Jisajili hapa</a>
            </p>
        </div>
    </div>
</body>
</html>