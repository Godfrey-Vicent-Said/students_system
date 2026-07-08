<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kuzuia watu wasioingia au wanafunzi wasiingie kwenye jopo la Admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

require_once 'Database.php';

$database = new Database();
$db = $database->connect();

$message = "";

// === SHUGHULIKIA KUONGEZA KOZI MPYA (CRUD: Create) ===
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_course'])) {
    $course_code = strtoupper(trim($_POST['course_code']));
    $course_name = trim($_POST['course_name']);

    if (!empty($course_code) && !empty($course_name)) {
        try {
            $query = "INSERT INTO courses (course_code, course_name) VALUES (:course_code, :course_name)";
            $stmt = $db->prepare($query);
            $stmt->execute(['course_code' => $course_code, 'course_name' => $course_name]);
            $message = "<div class='bg-green-100 text-green-800 p-3 rounded-lg mb-4 text-sm font-semibold border border-green-200'>✅ Kozi mpya imeongezwa kikamilifu!</div>";
        } catch (PDOException $e) {
            $message = "<div class='bg-red-100 text-red-800 p-3 rounded-lg mb-4 text-sm font-semibold border border-red-200'>⚠️ Hitilafu: Siri ya kozi (Code) tayari ipo!</div>";
        }
    } else {
        $message = "<div class='bg-red-100 text-red-800 p-3 rounded-lg mb-4 text-sm font-semibold border border-red-200'>⚠️ Tafadhali jaza nafasi zote!</div>";
    }
}

// === SHUGHULIKIA KUFUTA KOZI (CRUD: Delete) ===
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $query = "DELETE FROM courses WHERE id = :id";
    $stmt = $db->prepare($query);
    $stmt->execute(['id' => $delete_id]);
    header("Location: admin_dashboard.php");
    exit();
}

// === CHUKUA KOZI ZOTE ZILIZOPO ===
$query_courses = "SELECT * FROM courses ORDER BY course_name ASC";
$stmt_courses = $db->prepare($query_courses);
$stmt_courses->execute();
$all_courses = $stmt_courses->fetchAll(PDO::FETCH_ASSOC);

// === CHUKUA WANAFUNZI WALIOJISAJILI NA KOZI ZAO ===
$query_students = "SELECT u.username, sc.registration_date, c.course_code, c.course_name 
                   FROM student_courses sc 
                   JOIN users u ON sc.user_id = u.id 
                   JOIN courses c ON sc.course_id = c.id 
                   ORDER BY sc.registration_date DESC";
$stmt_students = $db->prepare($query_students);
$stmt_students->execute();
$registered_students = $stmt_students->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jopo la Msimamizi | Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <nav class="bg-slate-900 text-white p-4 shadow-md flex justify-between items-center">
        <h1 class="text-xl font-bold tracking-wide">CBE Portal | Jopo la Admin 🛠️</h1>
        <div class="flex items-center space-x-4">
            <span>Msimamizi: <strong class="text-yellow-400"><?php echo htmlspecialchars($_SESSION['username']); ?></strong></span>
            <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-semibold transition">Ondoka (Logout)</a>
        </div>
    </nav>

    <div class="max-w-7xl mx-auto p-6 grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-indigo-600 h-fit">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Ongeza Kozi Mpya</h2>
            
            <?php echo $message; ?>

            <form method="POST" action="" class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Siri ya Kozi (Course Code)</label>
                    <input type="text" name="course_code" placeholder="Mfano: BIT" required 
                           class="w-full p-2.5 border border-gray-300 rounded mt-1 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-600">Jina Kamili la Kozi</label>
                    <input type="text" name="course_name" placeholder="Mfano: Bachelor of Science in BIT" required 
                           class="w-full p-2.5 border border-gray-300 rounded mt-1 focus:outline-none focus:ring-2 focus:ring-indigo-500 text-sm">
                </div>
                <button type="submit" name="add_course" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white p-2.5 rounded font-bold transition shadow">
                    Hifadhi Kozi
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-6">
            
            <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-emerald-600">
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Orodha ya Kozi Zilizopo Chuoni</h2>
                
                <?php if (count($all_courses) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-100 text-gray-700 uppercase text-xs">
                                    <th class="p-3 border-b">Code</th>
                                    <th class="p-3 border-b">Jina la Kozi</th>
                                    <th class="p-3 border-b text-center">Hatua</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y text-gray-600">
                                <?php foreach ($all_courses as $course): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-3 font-mono font-bold text-blue-600"><?php echo htmlspecialchars($course['course_code']); ?></td>
                                        <td class="p-3"><?php echo htmlspecialchars($course['course_name']); ?></td>
                                        <td class="p-3 text-center">
                                            <a href="admin_dashboard.php?delete_id=<?php echo $course['id']; ?>" 
                                               onclick="return confirm('Je, una uhakika unataka kufuta kozi hii? Wanafunzi waliojisajili watafutwa pia!');" 
                                               class="bg-red-100 hover:bg-red-200 text-red-700 px-2.5 py-1 rounded text-xs font-semibold transition">
                                                Futa
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 text-sm italic">Hakuna kozi yoyote iliyosajiliwa kwa sasa.</p>
                <?php endif; ?>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-amber-500">
                <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Wanafunzi Waliojisajili Kwenye Kozi</h2>
                
                <?php if (count($registered_students) > 0): ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-gray-100 text-gray-700 uppercase text-xs">
                                    <th class="p-3 border-b">Username</th>
                                    <th class="p-3 border-b">Kozi Aliyochagua</th>
                                    <th class="p-3 border-b">Tarehe ya Usajili</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y text-gray-600">
                                <?php foreach ($registered_students as $reg_student): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="p-3 font-semibold text-gray-800"><?php echo htmlspecialchars($reg_student['username']); ?></td>
                                        <td class="p-3">
                                            <span class="font-mono font-bold text-indigo-600">[<?php echo htmlspecialchars($reg_student['course_code']); ?>]</span> 
                                            <?php echo htmlspecialchars($reg_student['course_name']); ?>
                                        </td>
                                        <td class="p-3 text-xs text-gray-500"><?php echo htmlspecialchars($reg_student['registration_date']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 text-sm italic">Bado hakuna mwanafunzi aliyesajili kozi yoyote kwa sasa.</p>
                <?php endif; ?>
            </div>

        </div>

    </div>

</body>
</html>