<?php
// id_card.php - Student Digital Hostel ID Card / Resident Pass (PHP Backend)
require_once 'db.php';

$student_code = $_GET['id'] ?? '';
$student = null;
$allocation = null;

if (!empty($student_code) && $db_connected) {
    $sstmt = $conn->prepare("SELECT * FROM students WHERE student_id = ? LIMIT 1");
    if ($sstmt) {
        $sstmt->bind_param("s", $student_code);
        $sstmt->execute();
        $sres = $sstmt->get_result();
        if ($sres && $sres->num_rows > 0) $student = $sres->fetch_assoc();
        $sstmt->close();
    }

    if ($student) {
        $sid = $student['id'];
        $astmt = $conn->prepare("SELECT a.*, r.room_number, r.block_name, r.floor, r.room_type 
                                FROM allocations a 
                                LEFT JOIN rooms r ON a.room_id = r.id 
                                WHERE a.student_id = ? AND a.allocation_status = 'Allocated' LIMIT 1");
        if ($astmt) {
            $astmt->bind_param("i", $sid);
            $astmt->execute();
            $ares = $astmt->get_result();
            if ($ares && $ares->num_rows > 0) $allocation = $ares->fetch_assoc();
            $astmt->close();
        }
    }
}

// Fallback to first student if not chosen
if (!$student && $db_connected) {
    $sres = $conn->query("SELECT * FROM students ORDER BY id ASC LIMIT 1");
    if ($sres && $sres->num_rows > 0) {
        $student = $sres->fetch_assoc();
        $sid = $student['id'];
        $astmt = $conn->query("SELECT a.*, r.room_number, r.block_name, r.floor, r.room_type 
                               FROM allocations a 
                               LEFT JOIN rooms r ON a.room_id = r.id 
                               WHERE a.student_id = $sid LIMIT 1");
        if ($astmt && $astmt->num_rows > 0) $allocation = $astmt->fetch_assoc();
    }
}

// Fetch all students for dropdown
$all_students = [];
if ($db_connected) {
    $res = $conn->query("SELECT student_id, full_name FROM students ORDER BY full_name ASC");
    if ($res) {
        while ($row = $res->fetch_assoc()) $all_students[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sumanth College Hostel - Resident ID Pass</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        @media print {
            body { background: #fff !important; color: #000 !important; }
            .navbar, .bg-video-container, #sakuraCanvas, .main-footer, .no-print { display: none !important; }
            .id-pass-card { border: 2px solid #000; box-shadow: none; background: #fff !important; color: #000 !important; }
            .id-pass-header { background: #000 !important; color: #fff !important; }
        }
    </style>
</head>
<body>

    <div class="bg-video-container">
        <video class="bg-video" autoplay muted loop playsinline poster="assets/images/anime_hostel_campus.jpg">
            <source src="https://assets.mixkit.co/videos/preview/mixkit-futuristic-city-traffic-and-flying-cars-at-night-42861-large.mp4" type="video/mp4">
        </video>
        <div class="bg-overlay"></div>
    </div>
    <canvas id="sakuraCanvas"></canvas>

    <header class="navbar no-print">
        <div class="nav-wrap">
            <a href="index.php" class="brand">
                <span class="brand-icon">🏫</span>
                <span>Sumanth<span class="gradient-text">Hostel</span></span>
                <span class="brand-badge">ERP</span>
            </a>
            <nav class="nav-links">
                <a href="index.php" class="nav-link">Home</a>
                <a href="students.php" class="nav-link">Students</a>
                <a href="rooms.php" class="nav-link">Rooms</a>
                <a href="allocations.php" class="nav-link">Allocations</a>
                <a href="id_card.php" class="nav-link active">Hostel Pass</a>
                <a href="allocations.php" class="nav-btn">Allocate Room ➔</a>
            </nav>
        </div>
    </header>

    <main class="section-wrap" style="margin-top: 2.5rem;">
        <div style="text-align: center; margin-bottom: 2rem;" class="no-print">
            <span class="hero-pill">🪪 Verified Campus Resident Pass</span>
            <h1 class="section-title">Digital Hostel Resident ID Card</h1>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.4rem;">
                Official student residency credential for hostel gate biometric entry, dining mess, and library study hubs.
            </p>

            <div style="margin-top: 1.25rem; display: flex; justify-content: center; gap: 1rem; align-items: center; flex-wrap: wrap;">
                <select onchange="window.location.href='id_card.php?id='+this.value" class="form-group-custom" style="padding: 0.65rem 1.25rem; border-radius: 8px; max-width: 320px;">
                    <?php foreach ($all_students as $st): ?>
                        <option value="<?= htmlspecialchars($st['student_id']) ?>" <?= ($student && $student['student_id'] === $st['student_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($st['full_name']) ?> (<?= htmlspecialchars($st['student_id']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <button onclick="window.print()" class="submit-btn-glow" style="padding: 0.75rem 1.75rem;">
                    🖨️ Print / Save ID Card (PDF)
                </button>
            </div>
        </div>

        <?php if ($student): ?>
            <div class="id-pass-card">
                <div class="id-pass-header">
                    <div>
                        <h2 style="font-family: var(--font-heading); color: #fff; font-size: 1.25rem; display: flex; align-items: center; gap: 0.5rem;">
                            🏫 Sumanth College
                        </h2>
                        <span style="font-size: 0.75rem; color: #ffe4e6; text-transform: uppercase; letter-spacing: 0.05em;">
                            Official Hostel Resident Pass · <?= htmlspecialchars($allocation['academic_year'] ?? '2026-2027') ?>
                        </span>
                    </div>
                    <div style="background: rgba(0, 0, 0, 0.25); padding: 0.35rem 0.75rem; border-radius: 8px; font-family: var(--font-code); font-weight: 800; font-size: 0.85rem; color: #fff;">
                        VERIFIED RESIDENT
                    </div>
                </div>

                <div class="id-pass-body">
                    <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1.25rem; border-bottom: 1px dashed rgba(255, 255, 255, 0.1);">
                        <div style="width: 85px; height: 85px; border-radius: 18px; background: linear-gradient(135deg, rgba(244, 63, 94, 0.2), rgba(139, 92, 246, 0.2)); border: 2px solid var(--primary-sakura); display: flex; align-items: center; justify-content: center; font-size: 2.8rem; box-shadow: 0 4px 15px var(--primary-sakura-glow);">
                            🎓
                        </div>
                        <div>
                            <h3 style="font-family: var(--font-heading); font-size: 1.4rem; color: #fff;"><?= htmlspecialchars($student['full_name']) ?></h3>
                            <div style="color: var(--primary-cyan); font-weight: 700; font-family: var(--font-code); font-size: 0.92rem;"><?= htmlspecialchars($student['student_id']) ?></div>
                            <div style="color: var(--text-muted); font-size: 0.85rem;"><?= htmlspecialchars($student['department']) ?> · <?= htmlspecialchars($student['year_of_study']) ?></div>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.25rem; margin-bottom: 1.5rem;">
                        <div>
                            <div style="font-size: 0.72rem; color: var(--text-faint); text-transform: uppercase;">Hostel Block</div>
                            <div style="font-weight: 800; color: #fff; font-size: 0.95rem;"><?= htmlspecialchars($allocation['block_name'] ?? 'Unallocated Wing') ?></div>
                        </div>
                        <div>
                            <div style="font-size: 0.72rem; color: var(--text-faint); text-transform: uppercase;">Allocated Room & Bed</div>
                            <div style="font-weight: 800; color: var(--primary-sakura); font-size: 1.05rem;">
                                <?= $allocation ? "Room " . htmlspecialchars($allocation['room_number']) . " (" . htmlspecialchars($allocation['bed_number']) . ")" : "Pending Allocation" ?>
                            </div>
                        </div>
                        <div>
                            <div style="font-size: 0.72rem; color: var(--text-faint); text-transform: uppercase;">Emergency Guardian</div>
                            <div style="font-weight: 700; color: #e2e8f0; font-size: 0.9rem;"><?= htmlspecialchars($student['guardian_name']) ?> (<?= htmlspecialchars($student['guardian_phone']) ?>)</div>
                        </div>
                        <div>
                            <div style="font-size: 0.72rem; color: var(--text-faint); text-transform: uppercase;">Student Phone</div>
                            <div style="font-weight: 700; color: #e2e8f0; font-size: 0.9rem;"><?= htmlspecialchars($student['phone']) ?></div>
                        </div>
                    </div>

                    <div style="text-align: center; padding-top: 1.25rem; border-top: 1px solid rgba(255, 255, 255, 0.08);">
                        <div style="height: 38px; background: repeating-linear-gradient(90deg, #fff 0, #fff 2px, transparent 2px, transparent 5px, #fff 5px, #fff 8px, transparent 8px, transparent 10px); margin: 0.5rem auto; max-width: 260px; opacity: 0.85;"></div>
                        <span style="font-family: var(--font-code); font-size: 0.78rem; color: var(--text-faint);">
                            *<?= htmlspecialchars($student['student_id']) ?>-<?= htmlspecialchars($allocation['allocation_code'] ?? 'HOSTEL') ?>-2026*
                        </span>
                        <p style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem;">
                            Gate Curfew: 09:30 PM. For emergency warden assistance: 0866-249999
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <footer class="main-footer no-print">
        <div class="footer-brand">🏫 Sumanth College Hostel Management System</div>
        <p class="footer-copy">© <?= date('Y') ?> Sumanth College of Technology & Science.</p>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
