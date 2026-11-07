// ========================================================
// Sumanth College Hostel Management System - Main Engine
// Features: Sakura Canvas Particles, LocalStorage Engine,
// Student Roster, Room Capacity Tracking & Allocations
// ========================================================

document.addEventListener('DOMContentLoaded', () => {
    initSakuraParticles();
    initStorageDefaults();
});

// --------------------------------------------------------
// 1. Anime Sakura Falling Petals Canvas Animation
// --------------------------------------------------------
function initSakuraParticles() {
    const canvas = document.getElementById('sakuraCanvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    let width = canvas.width = window.innerWidth;
    let height = canvas.height = window.innerHeight;

    window.addEventListener('resize', () => {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
    });

    const petalCount = 35;
    const petals = [];

    class Petal {
        constructor() {
            this.reset();
        }

        reset() {
            this.x = Math.random() * width;
            this.y = Math.random() * -height;
            this.size = Math.random() * 8 + 6;
            this.speedX = Math.random() * 1.5 - 0.5;
            this.speedY = Math.random() * 1.2 + 0.8;
            this.rotation = Math.random() * 360;
            this.rotSpeed = Math.random() * 1.5 - 0.75;
            this.opacity = Math.random() * 0.5 + 0.35;
        }

        update() {
            this.x += this.speedX + Math.sin(this.y * 0.01) * 0.5;
            this.y += this.speedY;
            this.rotation += this.rotSpeed;

            if (this.y > height + 20 || this.x < -20 || this.x > width + 20) {
                this.reset();
                this.y = -10;
            }
        }

        draw() {
            ctx.save();
            ctx.translate(this.x, this.y);
            ctx.rotate((this.rotation * Math.PI) / 180);
            ctx.globalAlpha = this.opacity;

            ctx.beginPath();
            ctx.moveTo(0, 0);
            ctx.bezierCurveTo(this.size / 2, -this.size / 2, this.size, 0, 0, this.size);
            ctx.bezierCurveTo(-this.size, 0, -this.size / 2, -this.size / 2, 0, 0);
            ctx.fillStyle = '#fb7185';
            ctx.shadowBlur = 10;
            ctx.shadowColor = 'rgba(244, 63, 94, 0.4)';
            ctx.fill();
            ctx.restore();
        }
    }

    for (let i = 0; i < petalCount; i++) {
        petals.push(new Petal());
    }

    function animate() {
        ctx.clearRect(0, 0, width, height);
        petals.forEach(p => {
            p.update();
            p.draw();
        });
        requestAnimationFrame(animate);
    }

    animate();
}

// --------------------------------------------------------
// 2. LocalStorage Default Store (Standalone Browser Database)
// --------------------------------------------------------
const STORAGE_KEYS = {
    STUDENTS: 'sch_students',
    ROOMS: 'sch_rooms',
    ALLOCATIONS: 'sch_allocations'
};

function initStorageDefaults() {
    if (!localStorage.getItem(STORAGE_KEYS.STUDENTS)) {
        const defaultStudents = [
            {
                id: 1,
                student_id: 'SCH-2026-001',
                name: 'Sumanth Varma',
                gender: 'Male',
                dept: 'Computer Science & AI',
                year: '3rd Year',
                phone: '9848012345',
                email: 'sumanth.varma@college.edu',
                guardian: 'Krishna Varma',
                guardian_phone: '9848099887',
                status: 'Active'
            },
            {
                id: 2,
                student_id: 'SCH-2026-002',
                name: 'Ananya Sharma',
                gender: 'Female',
                dept: 'Data Science',
                year: '2nd Year',
                phone: '9123456780',
                email: 'ananya.sharma@college.edu',
                guardian: 'Rajesh Sharma',
                guardian_phone: '9123459999',
                status: 'Active'
            },
            {
                id: 3,
                student_id: 'SCH-2026-003',
                name: 'Kiran Kumar',
                gender: 'Male',
                dept: 'Electronics & Comm',
                year: '4th Year',
                phone: '9440123456',
                email: 'kiran.kumar@college.edu',
                guardian: 'Narayana Swamy',
                guardian_phone: '9440199999',
                status: 'Active'
            },
            {
                id: 4,
                student_id: 'SCH-2026-004',
                name: 'Deepika Reddy',
                gender: 'Female',
                dept: 'Cyber Security',
                year: '1st Year',
                phone: '9988776655',
                email: 'deepika.reddy@college.edu',
                guardian: 'Murali Reddy',
                guardian_phone: '9988770000',
                status: 'Active'
            }
        ];
        localStorage.setItem(STORAGE_KEYS.STUDENTS, JSON.stringify(defaultStudents));
    }

    if (!localStorage.getItem(STORAGE_KEYS.ROOMS)) {
        const defaultRooms = [
            {
                id: 1,
                room_no: 'A-101',
                block: 'Aryabhata Block (Boys)',
                floor: '1st Floor',
                type: 'Double Sharing AC',
                capacity: 2,
                occupied: 1,
                fee: 38000,
                status: 'Available'
            },
            {
                id: 2,
                room_no: 'A-102',
                block: 'Aryabhata Block (Boys)',
                floor: '1st Floor',
                type: 'Single Deluxe AC',
                capacity: 1,
                occupied: 1,
                fee: 55000,
                status: 'Full'
            },
            {
                id: 3,
                room_no: 'A-201',
                block: 'Aryabhata Block (Boys)',
                floor: '2nd Floor',
                type: 'Triple Sharing Non-AC',
                capacity: 3,
                occupied: 0,
                fee: 24000,
                status: 'Available'
            },
            {
                id: 4,
                room_no: 'B-101',
                block: 'Sarojini Block (Girls)',
                floor: '1st Floor',
                type: 'Double Sharing AC',
                capacity: 2,
                occupied: 1,
                fee: 38000,
                status: 'Available'
            },
            {
                id: 5,
                room_no: 'B-102',
                block: 'Sarojini Block (Girls)',
                floor: '1st Floor',
                type: 'Double Sharing AC',
                capacity: 2,
                occupied: 1,
                fee: 38000,
                status: 'Available'
            },
            {
                id: 6,
                room_no: 'C-301',
                block: 'Ramanujan Executive',
                floor: '3rd Floor',
                type: 'Single Premium Studio',
                capacity: 1,
                occupied: 0,
                fee: 65000,
                status: 'Available'
            }
        ];
        localStorage.setItem(STORAGE_KEYS.ROOMS, JSON.stringify(defaultRooms));
    }

    if (!localStorage.getItem(STORAGE_KEYS.ALLOCATIONS)) {
        const defaultAllocations = [
            {
                id: 1,
                code: 'ALC-9011',
                student_id: 'SCH-2026-001',
                student_name: 'Sumanth Varma',
                room_no: 'A-101',
                bed: 'Bed-1',
                block: 'Aryabhata Block (Boys)',
                date: '2026-07-15',
                academic_year: '2026-2027',
                fee: 38000,
                payment_status: 'Paid',
                status: 'Allocated'
            },
            {
                id: 2,
                code: 'ALC-9012',
                student_id: 'SCH-2026-003',
                student_name: 'Kiran Kumar',
                room_no: 'A-102',
                bed: 'Bed-1',
                block: 'Aryabhata Block (Boys)',
                date: '2026-07-16',
                academic_year: '2026-2027',
                fee: 55000,
                payment_status: 'Paid',
                status: 'Allocated'
            },
            {
                id: 3,
                code: 'ALC-9013',
                student_id: 'SCH-2026-002',
                student_name: 'Ananya Sharma',
                room_no: 'B-101',
                bed: 'Bed-1',
                block: 'Sarojini Block (Girls)',
                date: '2026-07-18',
                academic_year: '2026-2027',
                fee: 38000,
                payment_status: 'Paid',
                status: 'Allocated'
            },
            {
                id: 4,
                code: 'ALC-9014',
                student_id: 'SCH-2026-004',
                student_name: 'Deepika Reddy',
                room_no: 'B-102',
                bed: 'Bed-2',
                block: 'Sarojini Block (Girls)',
                date: '2026-08-01',
                academic_year: '2026-2027',
                fee: 38000,
                payment_status: 'Paid',
                status: 'Allocated'
            }
        ];
        localStorage.setItem(STORAGE_KEYS.ALLOCATIONS, JSON.stringify(defaultAllocations));
    }
}

// Helper methods to read/write storage
function getStudents() {
    return JSON.parse(localStorage.getItem(STORAGE_KEYS.STUDENTS) || '[]');
}
function saveStudents(data) {
    localStorage.setItem(STORAGE_KEYS.STUDENTS, JSON.stringify(data));
}

function getRooms() {
    return JSON.parse(localStorage.getItem(STORAGE_KEYS.ROOMS) || '[]');
}
function saveRooms(data) {
    localStorage.setItem(STORAGE_KEYS.ROOMS, JSON.stringify(data));
}

function getAllocations() {
    return JSON.parse(localStorage.getItem(STORAGE_KEYS.ALLOCATIONS) || '[]');
}
function saveAllocations(data) {
    localStorage.setItem(STORAGE_KEYS.ALLOCATIONS, JSON.stringify(data));
}
