<?php
// Anza session kwa usalama
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'Database.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $db = (new Database())->connect();
    
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Tunatafuta mtumiaji kwenye database
    $query = "SELECT id, username, password, role FROM users WHERE username = :username";
    $stmt = $db->prepare($query);
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // Kagua kama password ni sahihi
    if ($user && password_verify($password, $user['password'])) {
        // Hifadhi taarifa kwenye session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = strtolower(trim($user['role']));

        // Elekeza kulingana na role
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingia Mfumo | CBE Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 h-screen flex items-center justify-center p-4">

    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-sm border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-black text-slate-800">CBE PORTAL</h1>
            <p class="text-slate-500 text-sm">Karibu tena, tafadhali ingia.</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50 text-red-600 p-3 rounded-lg text-sm mb-4 border border-red-200 text-center font-medium">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Username</label>
                <input type="text" name="username" required 
                       class="w-full p-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
            </div>
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase mb-1">Password</label>
                <input type="password" name="password" required 
                       class="w-full p-3 rounded-lg bg-gray-50 border border-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition">
            </div>
            
            <button type="submit" 
                    class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 rounded-lg shadow-lg transition transform active:scale-95">
                INGIA MFUMO
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="register.php" class="text-emerald-600 text-sm font-bold hover:underline">Huna akaunti? Jisajili hapa</a>
        </div>
    </div>
</body>
</html>