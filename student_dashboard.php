<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Kuzuia watu wasioingia kwenye mfumo au ma-admin wasiingie hapa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'student') {
    header("Location: login.php");
    exit();
}

require_once 'Database.php';
require_once 'Student.php';

$database = new Database();
$db = $database->connect();
$studentObj = new Student($db);

// Kuchukua taarifa za mwanafunzi (Zitafunguliwa - Decrypted ndani ya class)
$current_student = $studentObj->getProfile($_SESSION['user_id']);

// === SHUGHULIKIA USAJILI WA KOZI (BACKEND LOGIC) ===
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register_course_id'])) {
    $course_id = $_POST['register_course_id'];
    $user_id = $_SESSION['user_id'];

    try {
        // Angalia kama ameshasajili kozi yoyote kabla (Mwanafunzi anasajili kozi moja tu)
        $check_query = "SELECT * FROM student_courses WHERE user_id = :user_id";
        $check_stmt = $db->prepare($check_query);
        $check_stmt->execute(['user_id' => $user_id]);
        
        if ($check_stmt->rowCount() > 0) {
            $message = "<div class='bg-red-100 text-red-800 p-3 rounded-lg mb-4 text-sm font-semibold border border-red-200'>⚠️ Tayari umeshasajili kozi! Huwezi kusajili kozi zaidi ya moja.</div>";
        } else {
            // Ingiza usajili mpya kwenye database
            $insert_query = "INSERT INTO student_courses (user_id, course_id) VALUES (:user_id, :course_id)";
            $insert_stmt = $db->prepare($insert_query);
            $insert_stmt->execute(['user_id' => $user_id, 'course_id' => $course_id]);
            $message = "<div class='bg-green-100 text-green-800 p-3 rounded-lg mb-4 text-sm font-semibold border border-green-200'>✅ Umefanikiwa kusajili kozi hii kikamilifu!</div>";
        }
    } catch (PDOException $e) {
        $message = "<div class='bg-red-100 text-red-800 p-3 rounded-lg mb-4 text-sm font-semibold border border-red-200'>⚠️ Hitilafu imetokea wakati wa kusajili kozi.</div>";
    }
}

// Kuchukua jina la kozi aliyoisajili mwanafunzi huyu kwa sasa kutoka database
$enrolled_course_name = "Bado Huijasajili Kozi";
$enrolled_query = "SELECT c.course_name FROM student_courses sc JOIN courses c ON sc.course_id = c.id WHERE sc.user_id = :user_id";
$enrolled_stmt = $db->prepare($enrolled_query);
$enrolled_stmt->execute(['user_id' => $_SESSION['user_id']]);
if ($enrolled_row = $enrolled_stmt->fetch(PDO::FETCH_ASSOC)) {
    $enrolled_course_name = $enrolled_row['course_name'];
}

// Kuchukua kozi zote zilizopo kwa ajili ya kuonyesha kwenye jedwali
$query_courses = "SELECT * FROM courses ORDER BY course_name ASC";
$stmt_courses = $db->prepare($query_courses);
$stmt_courses->execute();
$all_courses = $stmt_courses->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="sw">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard ya Mwanafunzi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <nav class="bg-blue-900 text-white p-4 shadow-md flex justify-between items-center">
        <h1 class="text-xl font-bold">CBE Portal | Mwanafunzi</h1>
        <div class="flex items-center space-x-4">
            <span>Karibu, <strong class="text-yellow-400"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Mwanafunzi'); ?></strong></span>
            <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-semibold transition">Ondoka (Logout)</a>
        </div>
    </nav>

    <div class="max-w-6xl mx-auto p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Upande wa Kushoto: Taarifa Binafsi -->
        <div class="bg-white p-6 rounded-lg shadow-md border-t-4 border-blue-600 h-fit">
            <h2 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Taarifa Zako Binafsi</h2>
            <div class="space-y-3 text-sm">
                <div>
                    <span class="block text-gray-500 font-medium">Jina Kamili:</span>
                    <strong class="text-gray-800 text-base"><?php echo htmlspecialchars($current_student['full_name'] ?? 'N/A'); ?></strong>
                </div>
                <div>
                    <span class="block text-gray-500 font-medium">Namba ya Usajili:</span>
                    <strong class="text-gray-800"><?php echo htmlspecialchars($current_student['reg_number'] ?? 'N/A'); ?></strong>
                </div>
                <div>
                    <span class="block text-gray-500 font-medium">Barua Pepe (Email):</span>
                    <strong class="text-gray-800"><?php echo htmlspecialchars($current_student['email'] ?? 'N/A'); ?></strong>
                </div>
                <div>
                    <span class="block text-gray-500 font-medium">Namba ya Simu:</span>
                    <strong class="text-gray-800"><?php echo htmlspecialchars($current_student['phone_number'] ?? $current_student['phone'] ?? 'N/A'); ?></strong>
                </div>
                <div class="pt-2 border-t">
                    <span class="block text-gray-500 font-medium">Kozi Uliyosajili:</span>
                    <strong class="text-emerald-600 text-sm font-bold block mt-1"><?php echo htmlspecialchars($enrolled_course_name); ?></strong>
                </div>
            </div>
            <div class="mt-6 p-3 bg-green-50 text-green-800 text-xs rounded border border-green-200">
                🛡️ Data zako zimesimbwa (Encrypted) kwenye database kwa usalama wa hali ya juu.
            </div>
        </div>

        <!-- Upande wa Kulia: Orodha ya Kozi na Search -->
        <div class="bg-white p-6 rounded-lg shadow-md md:col-span-2 border-t-4 border-emerald-600">
            <h2 class="text-lg font-bold text-gray-800 mb-2 border-b pb-2">Kozi Zinazopatikana Chuo</h2>
            
            <!-- Alert Message (Inaonyesha kama usajili umefanikiwa au umefeli) -->
            <?php echo $message; ?>

            <!-- Search Input -->
            <div class="mb-4">
                <input type="text" id="courseSearch" placeholder="Tafuta kozi kwa jina au herufi fupi (Mfano: BIT)..." 
                       class="w-full p-2.5 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition text-sm">
            </div>
            
            <?php if (count($all_courses) > 0): ?>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-sm">
                        <thead>
                            <tr class="bg-gray-100 text-gray-700 uppercase text-xs">
                                <th class="p-3 border-b">Siri ya Kozi</th>
                                <th class="p-3 border-b">Jina la Kozi</th>
                                <th class="p-3 border-b text-center">Hatua</th>
                            </tr>
                        </thead>
                        <tbody id="courseTableBody" class="divide-y text-gray-600">
                            <?php foreach ($all_courses as $course): ?>
                                <tr class="course-row hover:bg-gray-50 transition">
                                    <td class="p-3 font-mono font-bold text-blue-600 course-code"><?php echo htmlspecialchars($course['course_code']); ?></td>
                                    <td class="p-3 course-name"><?php echo htmlspecialchars($course['course_name']); ?></td>
                                    <td class="p-3 text-center">
                                        <!-- Form ya kutuma ID ya kozi kwenda backend ikibonyezwa -->
                                        <form method="POST" action="" onsubmit="return confirm('Je, una uhakika unataka kusajili kozi ya <?php echo htmlspecialchars($course['course_code']); ?>?');">
                                            <input type="hidden" name="register_course_id" value="<?php echo $course['id']; ?>">
                                            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-3 py-1 rounded text-xs font-semibold shadow-sm transition">Register for a Course</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            
                            <!-- Ujumbe kama hakuna kozi inayolingana na iliyotafutwa -->
                            <tr id="noMatchRow" style="display: none;">
                                <td colspan="3" class="p-8 text-center text-gray-500 italic">
                                    Hakuna kozi inayolingana na ulivyotafuta.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-gray-500 text-sm italic p-4 text-center">Hakuna kozi zilizowekwa bado na Msimamizi.</p>
            <?php endif; ?>
        </div>

    </div>

    <!-- JavaScript ya Real-time Search ya Jedwali -->
    <script>
    document.getElementById('courseSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase().trim();
        let rows = document.querySelectorAll('.course-row');
        let matchCount = 0;

        rows.forEach(function(row) {
            let name = row.querySelector('.course-name').textContent.toLowerCase();
            let code = row.querySelector('.course-code').textContent.toLowerCase();
            
            if (name.includes(filter) || code.includes(filter)) {
                row.style.display = '';
                matchCount++;
            } else {
                row.style.display = 'none';
            }
        });

        let noMatchRow = document.getElementById('noMatchRow');
        if (noMatchRow) {
            if (matchCount === 0 && filter !== '') {
                noMatchRow.style.display = '';
            } else {
                noMatchRow.style.display = 'none';
            }
        }
    });
    </script>

</body>
</html>