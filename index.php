<?php
// index.php - Sumanth College Hostel Management System (PHP Backend)
require_once 'db.php';

$total_students = 1250;
$total_rooms = 420;
$total_allocations = 388;

if ($db_connected) {
    $s_res = $conn->query("SELECT COUNT(*) AS total FROM students");
    if ($s_res) $total_students = $s_res->fetch_assoc()['total'];

    $r_res = $conn->query("SELECT COUNT(*) AS total FROM rooms");
    if ($r_res) $total_rooms = $r_res->fetch_assoc()['total'];

    $a_res = $conn->query("SELECT COUNT(*) AS total FROM allocations WHERE allocation_status = 'Allocated'");
    if ($a_res) $total_allocations = $a_res->fetch_assoc()['total'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sumanth College Hostel - Smart Campus Living</title>
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
                <a href="index.php" class="nav-link active">Home</a>
                <a href="students.php" class="nav-link">Students</a>
                <a href="rooms.php" class="nav-link">Rooms</a>
                <a href="allocations.php" class="nav-link">Allocations</a>
                <a href="id_card.php" class="nav-link">Hostel Pass</a>
                <a href="allocations.php" class="nav-btn">Allocate Room ➔</a>
            </nav>
        </div>
    </header>

    <section class="hero-block">
        <div class="hero-text-wrap">
            <div class="hero-pill">
                <span>🌸</span> Smart Campus Residency & Hostel Management
            </div>
            <h1 class="hero-title">
                Next-Gen Student Living at <span class="gradient-text">Sumanth College</span>
            </h1>
            <p class="hero-lead">
                Welcome to Sumanth College Hostel Management System. Real-time room availability, seamless student bed allocation, digital hostel gate passes, and complete MySQL Workbench integration.
            </p>

            <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                <a href="allocations.php" class="submit-btn-glow" style="text-decoration: none; display: inline-flex; width: auto; padding: 0.9rem 1.8rem;">
                    <span>🛏️</span> Room Allocations Portal
                </a>
                <a href="students.php" class="nav-btn" style="background: rgba(30, 41, 59, 0.8); border: 1px solid rgba(255, 255, 255, 0.15); display: inline-flex; align-items: center; padding: 0.9rem 1.8rem;">
                    <span>👥</span> Student Directory
                </a>
                <a href="rooms.php" class="nav-btn" style="background: rgba(30, 41, 59, 0.8); border: 1px solid rgba(255, 255, 255, 0.15); display: inline-flex; align-items: center; padding: 0.9rem 1.8rem;">
                    <span>🚪</span> Manage Rooms
                </a>
            </div>
        </div>

        <div class="hero-visual">
            <img src="assets/images/anime_hostel_campus.jpg" alt="Sumanth College Smart Hostel Campus" class="hero-img">
            <div class="floating-badge">
                <div>
                    <div style="font-weight: 800; font-size: 1.05rem; color: #fff;">Asahi International Residence</div>
                    <div style="font-size: 0.82rem; color: var(--primary-cyan);">24/7 Biometric Smart Gates & High-Speed Optical 5G</div>
                </div>
                <span class="badge-status badge-active">Live ERP System</span>
            </div>
        </div>
    </section>

    <!-- Stats -->
    <div class="stats-strip">
        <div class="stats-grid">
            <div class="stat-item">
                <div class="stat-number"><?= number_format($total_students) ?></div>
                <div class="stat-label">Enrolled Residents</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= number_format($total_rooms) ?></div>
                <div class="stat-label">Hostel Dorm Rooms</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?= number_format($total_allocations) ?></div>
                <div class="stat-label">Allocated Beds</div>
            </div>
            <div class="stat-item">
                <div class="stat-number">4.9 ★</div>
                <div class="stat-label">Student Satisfaction</div>
            </div>
        </div>
    </div>

    <!-- MySQL Workbench Integration Banner -->
    <section class="section-wrap" style="margin-bottom: 3rem;">
        <div class="glass-box" style="border: 1px solid rgba(6, 182, 212, 0.3); background: rgba(14, 20, 40, 0.9);">
            <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
                <div>
                    <span class="hero-pill" style="background: rgba(6, 182, 212, 0.15); border-color: rgba(6, 182, 212, 0.3); color: #67e8f9;">
                        🐬 MySQL Workbench Active
                    </span>
                    <h3 style="font-family: var(--font-heading); font-size: 1.6rem; color: #fff; margin: 0.5rem 0;">
                        Live Database Integration
                    </h3>
                    <p style="color: var(--text-muted); max-width: 700px; font-size: 0.95rem;">
                        Connected to <code>sumanth_hostel_db</code> via port <code>3306</code>. Foreign key constraints maintain relational integrity across students, rooms, and allocations.
                    </p>
                </div>
                <div>
                    <a href="allocations.php" class="submit-btn-glow" style="text-decoration: none; padding: 0.85rem 1.6rem;">
                        ⚡ Manage Room Allocations
                    </a>
                </div>
            </div>
        </div>
    </section>

    <footer class="main-footer">
        <div class="footer-brand">🏫 Sumanth College Hostel Management System</div>
        <p class="footer-copy">© <?= date('Y') ?> Sumanth College of Technology & Science.</p>
    </footer>

    <script src="assets/js/main.js"></script>
</body>
</html>
