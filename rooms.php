<?php
// rooms.php - Rooms & Bed Capacity Management (PHP Backend)
require_once 'db.php';

$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action']) && $_POST['action'] === 'add_room') {
    if (!$db_connected) {
        $message = "Database offline. Please check connection.";
        $messageType = "error";
    } else {
        $room_number = trim($_POST['room_number'] ?? '');
        $block_name = trim($_POST['block_name'] ?? '');
        $floor = trim($_POST['floor'] ?? '1st Floor');
        $room_type = trim($_POST['room_type'] ?? 'Double Sharing AC');
        $capacity = intval($_POST['capacity'] ?? 2);
        $fee_per_semester = floatval($_POST['fee_per_semester'] ?? 38000.00);
        $status = trim($_POST['status'] ?? 'Available');

        if (empty($room_number) || empty($block_name)) {
            $message = "Please enter room number and block.";
            $messageType = "error";
        } else {
            $stmt = $conn->prepare("INSERT INTO rooms (room_number, block_name, floor, room_type, capacity, occupied_beds, fee_per_semester, status) VALUES (?, ?, ?, ?, ?, 0, ?, ?)");
            if ($stmt) {
                $stmt->bind_param("ssssids", $room_number, $block_name, $floor, $room_type, $capacity, $fee_per_semester, $status);
                if ($stmt->execute()) {
                    $message = "Room '$room_number' created successfully in $block_name!";
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

// Handle Room Deletion
if (isset($_GET['del_id']) && $db_connected) {
    $del_id = intval($_GET['del_id']);
    $dstmt = $conn->prepare("DELETE FROM rooms WHERE id = ?");
    if ($dstmt) {
        $dstmt->bind_param("i", $del_id);
        $dstmt->execute();
        $dstmt->close();
        header("Location: rooms.php?msg=deleted");
        exit;
    }
}

// Fetch all rooms
$rooms = [];
if ($db_connected) {
    $filter = $_GET['filter'] ?? 'all';
    $sql = "SELECT * FROM rooms";
    if ($filter !== 'all') {
        $sql .= " WHERE status = '" . $conn->real_escape_string($filter) . "'";
    }
    $sql .= " ORDER BY room_number ASC";
    $res = $conn->query($sql);
    if ($res) {
        while ($row = $res->fetch_assoc()) $rooms[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sumanth College Hostel - Rooms & Bed Capacity</title>
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
                <a href="rooms.php" class="nav-link active">Rooms</a>
                <a href="allocations.php" class="nav-link">Allocations</a>
                <a href="id_card.php" class="nav-link">Hostel Pass</a>
                <a href="allocations.php" class="nav-btn">Allocate Room ➔</a>
            </nav>
        </div>
    </header>

    <div style="max-width: 1380px; margin: 2.5rem auto 1rem; padding: 0 1.75rem;">
        <span class="hero-pill">🚪 Dormitory Infrastructure</span>
        <h1 class="section-title">Rooms & Bed Capacity Management</h1>
        <p class="section-desc" style="margin: 0.4rem 0 0 0; text-align: left;">
            Configure residential blocks, monitor real-time bed occupancy, and set semester accommodation fees.
        </p>
    </div>

    <main class="section-wrap" style="margin-top: 1.5rem;">
        <?php if (!empty($message)): ?>
            <div style="background: <?= $messageType === 'success' ? 'rgba(16, 185, 129, 0.2)' : 'rgba(244, 63, 94, 0.2)' ?>; border: 1px solid <?= $messageType === 'success' ? '#10b981' : '#f43f5e' ?>; color: #fff; padding: 1rem 1.25rem; border-radius: 12px; margin-bottom: 1.5rem;">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="app-layout">
            <!-- Left: Add Room Form -->
            <div class="glass-box">
                <h3 style="font-family: var(--font-heading); font-size: 1.35rem; margin-bottom: 1.25rem; color: #fff;">
                    Add Hostel Room
                </h3>

                <form method="POST" action="rooms.php" class="form-stack">
                    <input type="hidden" name="action" value="add_room">

                    <div class="form-row-custom">
                        <div class="form-group-custom">
                            <label for="room_number">Room Number</label>
                            <input type="text" id="room_number" name="room_number" placeholder="e.g. A-105" required>
                        </div>
                        <div class="form-group-custom">
                            <label for="block_name">Hostel Block / Wing</label>
                            <select id="block_name" name="block_name" required>
                                <option value="Aryabhata Block (Boys)">Aryabhata Block (Boys)</option>
                                <option value="Sarojini Block (Girls)">Sarojini Block (Girls)</option>
                                <option value="Ramanujan Executive">Ramanujan Executive</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row-custom">
                        <div class="form-group-custom">
                            <label for="floor">Floor Level</label>
                            <select id="floor" name="floor">
                                <option value="Ground Floor">Ground Floor</option>
                                <option value="1st Floor" selected>1st Floor</option>
                                <option value="2nd Floor">2nd Floor</option>
                                <option value="3rd Floor">3rd Floor</option>
                            </select>
                        </div>
                        <div class="form-group-custom">
                            <label for="room_type">Room Category</label>
                            <select id="room_type" name="room_type" required>
                                <option value="Single Deluxe AC">Single Deluxe AC</option>
                                <option value="Double Sharing AC" selected>Double Sharing AC</option>
                                <option value="Triple Sharing Non-AC">Triple Sharing Non-AC</option>
                                <option value="Single Premium Studio">Single Premium Studio</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row-custom">
                        <div class="form-group-custom">
                            <label for="capacity">Total Bed Capacity</label>
                            <input type="number" id="capacity" name="capacity" value="2" min="1" max="6" required>
                        </div>
                        <div class="form-group-custom">
                            <label for="fee_per_semester">Semester Fee (₹)</label>
                            <input type="number" id="fee_per_semester" name="fee_per_semester" value="38000" min="5000" max="200000" required>
                        </div>
                    </div>

                    <div class="form-group-custom">
                        <label for="status">Initial Status</label>
                        <select id="status" name="status">
                            <option value="Available" selected>Available for Allocation</option>
                            <option value="Maintenance">Under Renovation</option>
                        </select>
                    </div>

                    <button type="submit" class="submit-btn-glow">
                        <span>🚪</span> Create Room in System
                    </button>
                </form>
            </div>

            <!-- Right: Table -->
            <div class="glass-box">
                <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
                    <div>
                        <h3 style="font-family: var(--font-heading); font-size: 1.35rem; color: #fff;">
                            Hostel Rooms (<?= count($rooms) ?>)
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted);">
                            Live bed occupancy and capacity overview
                        </p>
                    </div>
                    <div>
                        <select onchange="window.location.href='rooms.php?filter='+this.value" class="form-group-custom" style="padding: 0.5rem 0.9rem; border-radius: 8px;">
                            <option value="all" <?= ($filter ?? 'all') === 'all' ? 'selected' : '' ?>>All Rooms</option>
                            <option value="Available" <?= ($filter ?? '') === 'Available' ? 'selected' : '' ?>>Available Only</option>
                            <option value="Full" <?= ($filter ?? '') === 'Full' ? 'selected' : '' ?>>Full Only</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive-custom">
                    <table class="modern-table">
                        <thead>
                            <tr>
                                <th>Room No</th>
                                <th>Block & Floor</th>
                                <th>Category</th>
                                <th>Occupancy</th>
                                <th>Fee / Sem</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($rooms)): ?>
                                <?php foreach ($rooms as $r): ?>
                                    <?php $pct = round(($r['occupied_beds'] / $r['capacity']) * 100); ?>
                                    <tr>
                                        <td style="font-family: var(--font-code); font-weight: 800; color: var(--primary-sakura); font-size: 1.1rem;"><?= htmlspecialchars($r['room_number']) ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($r['block_name']) ?></strong><br>
                                            <small style="color: var(--text-muted);"><?= htmlspecialchars($r['floor']) ?></small>
                                        </td>
                                        <td><span style="font-size: 0.85rem; color: #cbd5e1;"><?= htmlspecialchars($r['room_type']) ?></span></td>
                                        <td>
                                            <div style="font-size: 0.82rem; margin-bottom: 0.2rem; font-weight: 700; color: #fff;"><?= $r['occupied_beds'] ?> / <?= $r['capacity'] ?> Beds</div>
                                            <div class="bed-meter" style="width: 100px; margin: 0;">
                                                <div class="bed-meter-fill" style="width: <?= $pct ?>%;"></div>
                                            </div>
                                        </td>
                                        <td style="font-weight: 800; color: #34d399;">₹<?= number_format($r['fee_per_semester']) ?></td>
                                        <td><span class="badge-status badge-<?= strtolower($r['status']) ?>"><?= htmlspecialchars($r['status']) ?></span></td>
                                        <td>
                                            <a href="rooms.php?del_id=<?= $r['id'] ?>" onclick="return confirm('Remove room?')" class="icon-btn btn-del" title="Delete">🗑️</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" style="text-align: center; padding: 2.5rem; color: var(--text-faint);">
                                        No rooms found in database.
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
