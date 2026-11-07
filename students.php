<?php
// students.php - Student Management & Registration (PHP Backend)
require_once 'db.php';

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add_student') {
    if (!$db_connected) {
        $message = "Database offline. Please check MySQL configuration.";
        $messageType = "error";
    } else {
        $student_id = trim($_POST['student_id'] ?? '');
        $full_name = trim($_POST['full_name'] ?? '');
        $gender = trim($_POST['gender'] ?? 'Male');
        $department = trim($_POST['department'] ?? '');
        $year_of_study = trim($_POST['year_of_study'] ?? '1st Year');
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $guardian_name = trim($_POST['guardian_name'] ?? '');
        $guardian_phone = trim($_POST['guardian_phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (empty($student_id) || empty($full_name) || empty($phone)) {
            $message = "Please complete all mandatory student registration fields.";
            $messageType = "error";
        } else {
            $stmt = $conn->prepare("INSERT INTO students (student_id, full_name, gender, department, year_of_study, phone, email, guardian_name, guardian_phone, address, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Active')");
            if ($stmt) {
                $stmt->bind_param("ssssssssss", $student_id, $full_name, $gender, $department, $year_of_study, $phone, $email, $guardian_name, $guardian_phone, $address);
                if ($stmt->execute()) {
                    $message = "Student '$full_name' ($student_id) registered successfully!";
                    $messageType = "success";
                } else {
                    $message = "Database Error: " . $stmt->error;
                    $messageType = "error";
                }
                $stmt->close();
            } else {
                $message = "SQL Error: " . $conn->error;
                $messageType = "error";
            }
        }
    }
}

// Handle Student Deletion
if (isset($_GET['del_id']) && $db_connected) {
    $del_id = intval($_GET['del_id']);
    $dstmt = $conn->prepare("DELETE FROM students WHERE id = ?");
    if ($dstmt) {
        $dstmt->bind_param("i", $del_id);
        $dstmt->execute();
        $dstmt->close();
        header("Location: students.php?msg=deleted");
        exit;
    }
}

// Fetch all students
$students = [];
if ($db_connected) {
    $search = trim($_GET['search'] ?? '');
    $sql = "SELECT * FROM students";
    if (!empty($search)) {
        $s = $conn->real_escape_string($search);
        $sql .= " WHERE full_name LIKE '%$s%' OR student_id LIKE '%$s%' OR department LIKE '%$s%'";
    }
    $sql .= " ORDER BY id DESC";
    $res = $conn->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) $students[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sumanth College Hostel - Student Directory</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <div class="bg-video-container">
        <video class="bg-video" autoplay muted loop playsinline poster="assets/images/anime_hostel_campus.jpg">
            <source src="https://assets.mixkit.co/videos/preview/mixkit-futuristic-city-traffic-and-flying-cars-at-night-42861-large.mp4" type="video/mp4">
        </video>
        <div class="bg-overlay"></div>
    </div>
    <canvas id="sakuraCanvas"></canvas>

    <header class="navbar">
        <div class="nav-wrap">
            <a href="index.php" class="brand">
                <span class="brand-icon">🏫</span>
                <span>Sumanth<span class="gradient-text">Hostel</span></span>
                <span class="brand-badge">ERP</span>
            </a>
            <nav class="nav-links">
                <a href="index.php" class="nav-link">Home</a>
                <a href="students.php" class="nav-link active">Students</a>
                <a href="rooms.php" class="nav-link">Rooms</a>
                <a href="allocations.php" class="nav-link">Allocations</a>
                <a href="id_card.php" class="nav-link">Hostel Pass</a>
                <a href="allocations.php" class="nav-btn">Allocate Room ➔</a>
            </nav>
        </div>
    </header>

    <div style="max-width: 1380px; margin: 2.5rem auto 1rem; padding: 0 1.75rem;">
        <span class="hero-pill">👥 Resident Directory</span>
        <h1 class="section-title">Hostel Students Management</h1>
        <p class="section-desc" style="margin: 0.4rem 0 0 0; text-align: left;">
            Enroll new hostel residents, maintain guardian records, and generate verified digital campus passes.
        </p>
    </div>

    <main class="section-wrap" style="margin-top: 1.5rem;">
        <?php if (!empty($message)): ?>
            <div style="background: <?= $messageType === 'success' ? 'rgba(16, 185, 129, 0.2)' : 'rgba(244, 63, 94, 0.2)' ?>; border: 1px solid <?= $messageType === 'success' ? '#10b981' : '#f43f5e' ?>; color: #fff; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="app-layout">
            <!-- Left: Form -->
            <div class="glass-box">
                <h3 style="font-family: var(--font-heading); font-size: 1.35rem; margin-bottom: 1.25rem; color: #fff;">
                    Register New Resident
                </h3>

                <form method="POST" action="students.php" class="form-stack">
                    <input type="hidden" name="action" value="add_student">

                    <div class="form-row-custom">
                        <div class="form-group-custom">
                            <label for="student_id">Student Reg ID</label>
                            <input type="text" id="student_id" name="student_id" value="SCH-2026-<?= rand(100, 999) ?>" required>
                        </div>
                        <div class="form-group-custom">
                            <label for="full_name">Full Name</label>
                            <input type="text" id="full_name" name="full_name" placeholder="e.g. Sumanth Varma" required>
                        </div>
                    </div>

                    <div class="form-row-custom">
                        <div class="form-group-custom">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" required>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="form-group-custom">
                            <label for="year_of_study">Year of Study</label>
                            <select id="year_of_study" name="year_of_study" required>
                                <option value="1st Year">1st Year (Freshman)</option>
                                <option value="2nd Year">2nd Year (Sophomore)</option>
                                <option value="3rd Year" selected>3rd Year (Junior)</option>
                                <option value="4th Year">4th Year (Senior)</option>
                                <option value="Post-Graduate">Post-Graduate</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group-custom">
                        <label for="department">Department / Course</label>
                        <select id="department" name="department" required>
                            <option value="Computer Science & AI">Computer Science & AI</option>
                            <option value="Data Science & Analytics">Data Science & Analytics</option>
                            <option value="Cyber Security & Forensics">Cyber Security & Forensics</option>
                            <option value="Electronics & Communication">Electronics & Communication</option>
                            <option value="Mechanical & Robotics">Mechanical & Robotics</option>
                        </select>
                    </div>

                    <div class="form-row-custom">
                        <div class="form-group-custom">
                            <label for="phone">Student Phone</label>
                            <input type="tel" id="phone" name="phone" placeholder="9848012345" required>
                        </div>
                        <div class="form-group-custom">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" placeholder="student@college.edu" required>
                        </div>
                    </div>

                    <div class="form-row-custom">
                        <div class="form-group-custom">
                            <label for="guardian_name">Guardian / Parent Name</label>
                            <input type="text" id="guardian_name" name="guardian_name" placeholder="e.g. Krishna Varma" required>
                        </div>
                        <div class="form-group-custom">
                            <label for="guardian_phone">Guardian Contact</label>
                            <input type="tel" id="guardian_phone" name="guardian_phone" placeholder="9848099887" required>
                        </div>
                    </div>

                    <div class="form-group-custom">
                        <label for="address">Permanent Address</label>
                        <input type="text" id="address" name="address" placeholder="City, State, Pincode" required>
                    </div>

                    <button type="submit" class="submit-btn-glow">
                        <span>📝</span> Save & Register Student
                    </button>
                </form>
            </div>

            <!-- Right: Table -->
            <div class="glass-box">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <h3 style="font-family: var(--font-heading); font-size: 1.35rem; color: #fff;">
                            Registered Residents (<?= count($students) ?>)
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">
                            Active MySQL database students directory
                        </p>
                    </div>
                    <form method="GET" action="students.php" style="display: flex; gap: 0.5rem;">
                        <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Search student..." style="background: var(--input-bg); border: 1px solid var(--input-border); border-radius: 8px; padding: 0.5rem 0.85rem; color: #fff; font-size: 0.85rem; outline: none;">
                    </form>
                </div>

                <div class="table-responsive-custom">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Student ID</th>
                                <th>Name & Dept</th>
                                <th>Year</th>
                                <th>Phone</th>
                                <th>Guardian</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($students)): ?>
                                <?php foreach ($students as $s): ?>
                                    <tr>
                                        <td style="font-family: var(--font-code); font-weight: 800; color: var(--primary-sakura);"><?= htmlspecialchars($s['student_id']) ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($s['full_name']) ?></strong><br>
                                            <small style="color: var(--primary-cyan);"><?= htmlspecialchars($s['department']) ?></small>
                                        </td>
                                        <td><span style="font-size: 0.85rem; color: #cbd5e1;"><?= htmlspecialchars($s['year_of_study']) ?></span></td>
                                        <td><?= htmlspecialchars($s['phone']) ?></td>
                                        <td>
                                            <span style="color: #e2e8f0;"><?= htmlspecialchars($s['guardian_name']) ?></span><br>
                                            <small style="color: var(--text-muted);"><?= htmlspecialchars($s['guardian_phone']) ?></small>
                                        </td>
                                        <td><span class="badge-status badge-active"><?= htmlspecialchars($s['status']) ?></span></td>
                                        <td>
                                            <div class="action-group">
                                                <a href="id_card.php?id=<?= urlencode($s['student_id']) ?>" class="icon-btn" title="View Resident ID Pass">🪪</a>
                                                <a href="students.php?del_id=<?= $s['id'] ?>" onclick="return confirm('Delete student?')" class="icon-btn btn-del" title="Delete">🗑️</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--text-faint);">
                                        No student records found in database.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <footer class="main-footer">
        <div class="footer-brand">🏫 Sumanth College Hostel Management System</div>
        <p class="footer-copy">© <?= date('Y') ?> Sumanth College of Technology & Science.</p>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
