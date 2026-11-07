<?php
// allocations.php - Room & Bed Allocation Management (PHP Backend)
require_once 'db.php';

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'allocate_room') {
    if (!$db_connected) {
        $message = "Database offline. Please check connection.";
        $messageType = "error";
    } else {
        $student_id = intval($_POST['student_id'] ?? 0);
        $room_id = intval($_POST['room_id'] ?? 0);
        $bed_number = trim($_POST['bed_number'] ?? 'Bed-1');
        $allocation_date = trim($_POST['allocation_date'] ?? date('Y-m-d'));
        $academic_year = trim($_POST['academic_year'] ?? '2026-2027');
        $fee_amount = floatval($_POST['fee_amount'] ?? 38000.00);
        $payment_status = trim($_POST['payment_status'] ?? 'Paid');
        $remarks = trim($_POST['remarks'] ?? '');

        if ($student_id <= 0 || $room_id <= 0) {
            $message = "Please select valid student and room.";
            $messageType = "error";
        } else {
            $allocation_code = 'ALC-' . rand(1000, 9999);

            $stmt = $conn->prepare("INSERT INTO allocations (allocation_code, student_id, room_id, bed_number, allocation_date, academic_year, fee_amount, payment_status, allocation_status, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'Allocated', ?)");
            if ($stmt) {
                $stmt->bind_param("siisssdss", $allocation_code, $student_id, $room_id, $bed_number, $allocation_date, $academic_year, $fee_amount, $payment_status, $remarks);
                if ($stmt->execute()) {
                    // Update room occupancy
                    $conn->query("UPDATE rooms SET occupied_beds = occupied_beds + 1, status = IF(occupied_beds >= capacity, 'Full', 'Available') WHERE id = $room_id");
                    
                    // Fetch student_id string for redirect
                    $sres = $conn->query("SELECT student_id FROM students WHERE id = $student_id");
                    $sCode = ($sres && $srow = $sres->fetch_assoc()) ? $srow['student_id'] : '';

                    header("Location: id_card.php?id=" . urlencode($sCode));
                    exit;
                } else {
                    $message = "Database Error: " . $stmt->error;
                    $messageType = "error";
                }
                $stmt->close();
            } else {
                $message = "SQL Preparation Error: " . $conn->error;
                $messageType = "error";
            }
        }
    }
}

// Handle Vacating / Checkout
if (isset($_GET['vacate_id']) && $db_connected) {
    $vacate_id = intval($_GET['vacate_id']);
    
    // Find room_id first
    $ares = $conn->query("SELECT room_id FROM allocations WHERE id = $vacate_id");
    if ($ares && $arow = $ares->fetch_assoc()) {
        $roomId = $arow['room_id'];
        $conn->query("UPDATE allocations SET allocation_status = 'Vacated' WHERE id = $vacate_id");
        $conn->query("UPDATE rooms SET occupied_beds = GREATEST(occupied_beds - 1, 0), status = 'Available' WHERE id = $roomId");
    }
    header("Location: allocations.php?msg=vacated");
    exit;
}

// Fetch Students & Rooms for dropdowns
$students = [];
$rooms = [];
$allocations = [];

if ($db_connected) {
    $sres = $conn->query("SELECT id, student_id, full_name, department FROM students WHERE status = 'Active' ORDER BY full_name ASC");
    if ($sres) {
        while ($row = $sres->fetch_assoc()) $students[] = $row;
    }

    $rres = $conn->query("SELECT id, room_number, block_name, capacity, occupied_beds, fee_per_semester, status FROM rooms WHERE status != 'Full' ORDER BY room_number ASC");
    if ($rres) {
        while ($row = $rres->fetch_assoc()) $rooms[] = $row;
    }

    $filter = $_GET['filter'] ?? 'all';
    $asql = "SELECT a.*, s.student_id AS s_code, s.full_name AS s_name, r.room_number, r.block_name 
             FROM allocations a 
             LEFT JOIN students s ON a.student_id = s.id 
             LEFT JOIN rooms r ON a.room_id = r.id";
    if ($filter !== 'all') {
        $asql .= " WHERE a.allocation_status = '" . $conn->real_escape_string($filter) . "'";
    }
    $asql .= " ORDER BY a.id DESC";
    $ares = $conn->query($asql);
    if ($ares) {
        while ($row = $ares->fetch_assoc()) $allocations[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sumanth College Hostel - Room Allocations</title>
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
                <a href="students.php" class="nav-link">Students</a>
                <a href="rooms.php" class="nav-link">Rooms</a>
                <a href="allocations.php" class="nav-link active">Allocations</a>
                <a href="id_card.php" class="nav-link">Hostel Pass</a>
                <a href="allocations.php" class="nav-btn">Allocate Room ➔</a>
            </nav>
        </div>
    </header>

    <div style="max-width: 1380px; margin: 2.5rem auto 1rem; padding: 0 1.75rem;">
        <span class="hero-pill">🛏️ Room Assignment</span>
        <h1 class="section-title">Student Room & Bed Allocations</h1>
        <p class="section-desc" style="margin: 0.4rem 0 0 0; text-align: left;">
            Assign verified rooms to registered students, manage semester payments, and issue residency slips.
        </p>
    </div>

    <main class="section-wrap" style="margin-top: 1.5rem;">
        <?php if (!empty($message)): ?>
            <div style="background: <?= $messageType === 'success' ? 'rgba(16, 185, 129, 0.2)' : 'rgba(244, 63, 94, 0.2)' ?>; border: 1px solid <?= $messageType === 'success' ? '#10b981' : '#f43f5e' ?>; color: #fff; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="app-layout">
            <!-- Left Form -->
            <div class="glass-box">
                <h3 style="font-family: var(--font-heading); font-size: 1.35rem; margin-bottom: 1.25rem; color: #fff;">
                    New Room Allocation
                </h3>

                <form method="POST" action="allocations.php" class="form-stack">
                    <input type="hidden" name="action" value="allocate_room">

                    <div class="form-row-custom">
                        <div class="form-group-custom">
                            <label>Ref Code</label>
                            <input type="text" value="ALC-<?= rand(1000, 9999) ?>" readonly style="font-family: var(--font-code); color: var(--primary-sakura); font-weight: bold;">
                        </div>
                        <div class="form-group-custom">
                            <label for="academic_year">Academic Year</label>
                            <input type="text" id="academic_year" name="academic_year" value="2026-2027" required>
                        </div>
                    </div>

                    <div class="form-group-custom">
                        <label for="student_id">Select Student</label>
                        <select id="student_id" name="student_id" required>
                            <?php foreach ($students as $s): ?>
                                <option value="<?= $s['id'] ?>">
                                    <?= htmlspecialchars($s['full_name']) ?> (<?= htmlspecialchars($s['student_id']) ?> · <?= htmlspecialchars($s['department']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group-custom">
                        <label for="room_id">Select Available Room</label>
                        <select id="room_id" name="room_id" required>
                            <?php foreach ($rooms as $r): ?>
                                <option value="<?= $r['id'] ?>" data-fee="<?= $r['fee_per_semester'] ?>">
                                    Room <?= htmlspecialchars($r['room_number']) ?> (<?= htmlspecialchars($r['block_name']) ?> · <?= $r['occupied_beds'] ?>/<?= $r['capacity'] ?> Beds · ₹<?= number_format($r['fee_per_semester']) ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-row-custom">
                        <div class="form-group-custom">
                            <label for="bed_number">Bed Number</label>
                            <select id="bed_number" name="bed_number" required>
                                <option value="Bed-1">Bed-1</option>
                                <option value="Bed-2">Bed-2</option>
                                <option value="Bed-3">Bed-3</option>
                                <option value="Bed-4">Bed-4</option>
                            </select>
                        </div>
                        <div class="form-group-custom">
                            <label for="fee_amount">Semester Fee (₹)</label>
                            <input type="number" id="fee_amount" name="fee_amount" value="38000" required>
                        </div>
                    </div>

                    <div class="form-row-custom">
                        <div class="form-group-custom">
                            <label for="allocation_date">Allocation Date</label>
                            <input type="date" id="allocation_date" name="allocation_date" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="form-group-custom">
                            <label for="payment_status">Fee Payment Status</label>
                            <select id="payment_status" name="payment_status">
                                <option value="Paid" selected>Paid in Full</option>
                                <option value="Pending">Pending Payment</option>
                                <option value="Partial">Partial Deposit</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group-custom">
                        <label for="remarks">Remarks</label>
                        <input type="text" id="remarks" name="remarks" value="Allocated with Wi-Fi & Laundry access">
                    </div>

                    <button type="submit" class="submit-btn-glow">
                        <span>✨</span> Complete Allocation & Issue Pass
                    </button>
                </form>
            </div>

            <!-- Right Table -->
            <div class="glass-box">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <h3 style="font-family: var(--font-heading); font-size: 1.35rem; color: #fff;">
                            Database Allocations (<?= count($allocations) ?>)
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">
                            Active student residential records in MySQL
                        </p>
                    </div>
                    <div>
                        <select onchange="window.location.href='allocations.php?filter='+this.value" class="form-group-custom" style="padding: 0.5rem 0.9rem; border-radius: 8px;">
                            <option value="all" <?= ($filter ?? 'all') === 'all' ? 'selected' : '' ?>>All Records</option>
                            <option value="Allocated" <?= ($filter ?? '') === 'Allocated' ? 'selected' : '' ?>>Allocated Only</option>
                            <option value="Vacated" <?= ($filter ?? '') === 'Vacated' ? 'selected' : '' ?>>Vacated Only</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive-custom">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Code</th>
                                <th>Student</th>
                                <th>Room & Bed</th>
                                <th>Wing</th>
                                <th>Fee</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($allocations)): ?>
                                <?php foreach ($allocations as $a): ?>
                                    <tr>
                                        <td style="font-family: var(--font-code); font-weight: 800; color: var(--primary-sakura);"><?= htmlspecialchars($a['allocation_code']) ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($a['s_name']) ?></strong><br>
                                            <small style="color: var(--primary-cyan);"><?= htmlspecialchars($a['s_code']) ?></small>
                                        </td>
                                        <td><span style="font-weight: 800; color: #fff;"><?= htmlspecialchars($a['room_number']) ?></span> · <small style="color: var(--text-muted);"><?= htmlspecialchars($a['bed_number']) ?></small></td>
                                        <td><small style="color: #cbd5e1;"><?= htmlspecialchars($a['block_name']) ?></small></td>
                                        <td>
                                            <strong style="color: #34d399;">₹<?= number_format($a['fee_amount']) ?></strong><br>
                                            <span class="badge-status badge-<?= strtolower($a['payment_status']) ?>"><?= htmlspecialchars($a['payment_status']) ?></span>
                                        </td>
                                        <td><span class="badge-status badge-<?= strtolower($a['allocation_status']) ?>"><?= htmlspecialchars($a['allocation_status']) ?></span></td>
                                        <td>
                                            <div class="action-group">
                                                <a href="id_card.php?id=<?= urlencode($a['s_code']) ?>" class="icon-btn" title="View Digital Pass">🪪</a>
                                                <?php if ($a['allocation_status'] === 'Allocated'): ?>
                                                    <a href="allocations.php?vacate_id=<?= $a['id'] ?>" onclick="return confirm('Check-out/vacate this allocation?')" class="icon-btn" title="Check-out / Vacate" style="background: rgba(245, 158, 11, 0.2); border-color: #f59e0b;">🚪</a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--text-faint);">
                                        No allocation records found in database.
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
