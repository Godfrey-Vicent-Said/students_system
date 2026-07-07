<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Ulinzi wa Session: Hakikisha ni Admin pekee anayeingia hapa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'Database.php';
require_once 'Admin.php';

$database = new Database();
$db = $database->connect();
$adminObj = new Admin($db);

$msg = "";

// 1. Shughuli ya Kuongeza Kozi (Form Action)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_course'])) {
    $code = trim($_POST['course_code']);
    $name = trim($_POST['course_name']);
    
    if (!empty($code) && !empty($name)) {
        if ($adminObj->addCourse($code, $name)) {
            $msg = "Kozi imeongezwa kikamilifu!";
        } else {
            $msg = "Hitilafu imetokea wakati wa kuongeza kozi.";
        }
    }
}

// 2. Shughuli ya Kutafuta (Search)
$search = isset($_GET['search']) ? trim($_GET['search']) : "";
$students_list = $adminObj->getAllStudents($search);

// 3. Kuchukua Takwimu za Ripoti
$stats = $adminObj->getSystemStats();
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - CBE</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 font-sans">

    <nav class="bg-slate-900 text-white p-4 shadow-md flex justify-between items-center">
        <h1 class="text-xl font-bold">CBE Portal | Msimamizi (Admin)</h1>
        <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-1.5 rounded text-sm font-semibold transition">Logout</a>
    </nav>

    <div class="max-w-7xl mx-auto p-6 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-emerald-600 text-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium opacity-80">Jumla ya Wanafunzi</h3>
            <p class="text-4xl font-bold mt-2"><?php echo $stats['total_students']; ?></p>
        </div>
        <div class="bg-blue-600 text-white p-6 rounded-lg shadow-md">
            <h3 class="text-lg font-medium opacity-80">Jumla ya Kozi Zilizopo</h3>
            <p class="text-4xl font-bold mt-2"><?php echo $stats['total_courses']; ?></p>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200">
            <h2 class="text-lg font-bold text-gray-800 mb-4">Sajili Kozi Mpya</h2>
            <?php if(!empty($msg)): ?>
                <div class="p-2 mb-3 bg-blue-50 text-blue-700 text-sm rounded"><?php echo $msg; ?></div>
            <?php endif; ?>
            <form action="admin_dashboard.php" method="POST" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-600">Siri ya Kozi (Course Code)</label>
                    <input type="text" name="course_code" placeholder="e.g., BIT 212" required class="w-full p-2 border rounded mt-1 focus:ring-2 focus:ring-slate-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-600">Jina la Kozi</label>
                    <input type="text" name="course_name" placeholder="e.g., Internet and Web Development" required class="w-full p-2 border rounded mt-1 focus:ring-2 focus:ring-slate-500 outline-none">
                </div>
                <button type="submit" name="add_course" class="w-full bg-slate-800 text-white p-2 rounded font-bold hover:bg-slate-700 transition">Hifadhi Kozi</button>
            </form>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border border-gray-200 lg:col-span-2">
            <div class="sm:flex sm:items-center sm:justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-800">Orodha ya Wanafunzi Waliosajiliwa</h2>
                
                <form action="admin_dashboard.php" method="GET" class="mt-2 sm:mt-0 flex">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Tafuta kwa Reg No..." class="p-2 border rounded-l focus:ring-2 focus:ring-slate-500 outline-none text-sm">
                    <button type="submit" class="bg-slate-800 text-white px-4 rounded-r text-sm hover:bg-slate-700">Tafuta</button>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm border-collapse">
                    <thead>
                        <tr class="bg-gray-100 text-gray-700 font-semibold">
                            <th class="p-3 border-b">Reg Number</th>
                            <th class="p-3 border-b">Jina Kamili (Decrypted)</th>
                            <th class="p-3 border-b">Email (Decrypted)</th>
                            <th class="p-3 border-b">Simu (Decrypted)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y text-gray-600">
                        <?php if(count($students_list) > 0): ?>
                            <?php foreach($students_list as $st): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="p-3 font-mono font-bold"><?php echo htmlspecialchars($st['reg_number']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($st['full_name']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($st['email']); ?></td>
                                    <td class="p-3"><?php echo htmlspecialchars($st['phone']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="p-3 text-center text-gray-400">Hakuna mwanafunzi aliyepatikana.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>