# 🏫 Sumanth College Hostel Management System

> **Next-Generation Smart Campus Living & Hostel ERP Platform** featuring Anime Aesthetics, AI Campus Imagery, Ambient Video Backgrounds, Falling Sakura Particles, Room & Bed Allocations, and MySQL Workbench Integration.

---

## 🌟 Modules & Features

1. **🏠 Home Page (`index.html` / `index.php`)**:
   - Hero section with anime video loop and interactive falling sakura canvas petals.
   - Live metrics: 1,250+ Residents, 420 Rooms, 92.4% Occupancy, 4.9★ Rating.
   - Residential wings showcase (*Aryabhata Block*, *Sarojini Block*, *Ramanujan Tower*).
   - MySQL Workbench integration overview and direct schema download.

2. **👥 Students Roster (`students.html` / `students.php`)**:
   - Register new residents with Student ID, Department, Year, Contact, Guardian info, and Address.
   - Searchable and filterable student roster with direct link to digital ID passes.

3. **🚪 Rooms & Bed Capacities (`rooms.html` / `rooms.php`)**:
   - Configure rooms across blocks, floors, categories (AC/Non-AC, Single/Double/Triple sharing), and semester fees.
   - Visual occupancy progress meters and live room status tracking (`Available`, `Full`, `Maintenance`).

4. **🛏️ Room & Bed Allocations (`allocations.html` / `allocations.php`)**:
   - Select registered student and available room; auto-fills semester fee.
   - Bed assignment (`Bed-1`, `Bed-2`, `Bed-3`, `Bed-4`), allocation date, and payment status (`Paid`, `Pending`, `Partial`).
   - One-click **Vacate / Checkout** button that restores room capacity dynamically!

5. **🪪 Digital Resident ID Card & Gate Pass (`id_card.html` / `id_card.php`)**:
   - Official hostel resident pass with student photo avatar, room assignment, emergency contact, and barcode.
   - Print-ready stylesheet for one-click physical printing or saving as PDF (`window.print()`).

6. **🐬 MySQL Workbench Integration**:
   - Complete `schema.sql` ready to execute in MySQL Workbench 8.0 on port 3306.
   - Relational foreign key integrity between students, rooms, and allocations.

---

## 🚀 How to Run the Website

### Option A: Standalone Instant Browser Mode (No Server Needed)
Double-click any of the HTML files to open them in your browser:
- `index.html`
- `students.html`
- `rooms.html`
- `allocations.html`
- `id_card.html`

### Option B: PHP + MySQL Workbench Mode
1. Open MySQL Workbench, open `schema.sql`, and click ⚡ Execute.
2. Copy `sumanth_college_hostel_management_system` to `C:\xampp\htdocs\`.
3. Open `http://localhost/sumanth_college_hostel_management_system/index.php`.
