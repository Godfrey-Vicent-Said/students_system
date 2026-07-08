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
        $_SESSION['role'] = strtolower(trim($user['role']));

        header("Location: " . ($_SESSION['role'] === 'admin' ? "admin_dashboard.php" : "student_dashboard.php"));
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingia Mfumo - CBE Student System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-cover bg-center h-screen flex items-center justify-center" style="background-image: url('https://images.unsplash.com/photo-1498243691581-b145c3f54a5a?q=80&w=1920');">
    
    <div class="absolute inset-0 bg-black opacity-60"></div>

    <div class="relative bg-white p-8 rounded-lg shadow-2xl w-full max-w-md z-10 mx-4">
        <h2 class="text-2xl font-bold text-center text-gray-800 mb-2">Karibu Tena</h2>
        <p class="text-sm text-center text-gray-500 mb-6">Ingia ili kuendelea na Mfumo wa Usajili</p>
        
        <?php if (!empty($error)): ?>
            <div class="p-3 bg-red-100 text-red-700 rounded mb-4 text-sm text-center"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-600">Username</label>
                <input type="text" name="username" required class="w-full p-3 border border-gray-300 rounded mt-1 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-600">Nenosiri (Password)</label>
                <input type="password" name="password" required class="w-full p-3 border border-gray-300 rounded mt-1 focus:outline-none focus:ring-2 focus:ring-emerald-500">
            </div>
            <button type="submit" class="w-full bg-emerald-600 text-white p-3 rounded font-bold hover:bg-emerald-700 transition">Ingia Mfumo</button>
        </form>

        <p class="text-sm text-center text-gray-600 mt-6">
            Hujasajiliwa bado? <a href="register.php" class="text-emerald-600 font-bold hover:underline">Tengeneza Akaunti hapa</a>
        </p>
    </div>
</body>
</html>