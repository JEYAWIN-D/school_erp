# DC Innovision — School ERP

A full-featured School ERP built with Laravel 12, Tailwind CSS v3 and Alpine.js. Covers 10 core modules: Admissions, Students, Academics, Attendance, Examinations, Fees, HR & Payroll, Library, Transport and Hostel.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.2 + Laravel 12 |
| Frontend | Blade + Tailwind CSS v3 + Alpine.js v3 |
| Database | MySQL 8.x |
| PDF | barryvdh/laravel-dompdf v3.1 |
| Excel | maatwebsite/excel v3.1 |
| Auth / RBAC | Spatie Laravel Permission v6 |
| Build | Vite 7 |
| Queue | Database driver (no Redis — shared hosting safe) |
| Cache | File driver |

---

## Local Setup (XAMPP / Windows)

### Prerequisites
- PHP 8.2 (XAMPP) with extensions: `pdo_mysql`, `gd`, `zip`, `mbstring`, `openssl`
- MySQL 8.x running on port 3306
- Node.js 18+ and npm
- Composer 2

### Steps

```bash
# 1. Clone
git clone https://github.com/dcinnovisions-lang/school-erp.git
cd school-erp

# 2. Install PHP dependencies
composer install

# 3. Install JS dependencies and build assets
npm install
npm run build

# 4. Environment
cp .env.example .env
php artisan key:generate

# 5. Edit .env — set these:
#    DB_DATABASE=school_erp
#    DB_USERNAME=root
#    DB_PASSWORD=

# 6. Create the database in MySQL
#    CREATE DATABASE school_erp CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 7. Migrate and seed
php artisan migrate
php artisan db:seed

# 8. Start dev server
php artisan serve
```

App runs at **http://127.0.0.1:8000**

---

## Login Credentials

| Role | Email | Password |
|---|---|---|
| Super Admin | `admin@schoolerp.in` | `Admin@1234` |

---

## Key URLs

| Page | URL |
|---|---|
| Login | http://127.0.0.1:8000/login |
| Public Enquiry Form | http://127.0.0.1:8000/enquiry |
| Dashboard | http://127.0.0.1:8000/dashboard |
| Students | http://127.0.0.1:8000/students |
| Admissions | http://127.0.0.1:8000/admissions |
| Academics | http://127.0.0.1:8000/academics |
| Attendance | http://127.0.0.1:8000/attendance |
| Examinations | http://127.0.0.1:8000/examinations |
| Fees | http://127.0.0.1:8000/fees |
| HR | http://127.0.0.1:8000/hr |
| Library | http://127.0.0.1:8000/library |
| Transport | http://127.0.0.1:8000/transport |
| Hostel | http://127.0.0.1:8000/hostel |

---

## Project Structure

```
app/
  Http/Controllers/Admin/   ← 10 module controllers
  Models/                   ← 60 Eloquent models
  Exports/                  ← 5 Excel export classes
resources/
  views/
    layouts/                ← app.blade.php, sidebar, topbar
    admissions/             ← 8 views
    students/               ← 10 views
    academics/              ← 7 views
    attendance/             ← 9 views
    examinations/           ← 10 views
    fees/                   ← 12 views
    hr/                     ← 10 views
    library/                ← 10 views
    transport/              ← 9 views
    hostel/                 ← 8 views
    pdf/                    ← 10 DomPDF templates
routes/
  web.php                   ← 230 named routes
database/
  migrations/               ← 20+ migration files
  seeders/                  ← Roles, Settings, SuperAdmin
```

---

## Development Status

### Completed — Modules 1–10 (Backend + Views)

#### Module 1 — Admissions
- [x] Public enquiry form (no login required)
- [x] Enquiry management with full status workflow
- [x] Multi-stage admission pipeline
- [x] Seat capacity management per class and category
- [x] Bulk student import via Excel with validation
- [x] Admission analytics (conversion funnel, source breakdown)

#### Module 2 — Students
- [x] Full student profile (personal, family, medical)
- [x] Medical records and vaccination tracking
- [x] Document upload and verification workflow
- [x] Disciplinary incident log
- [x] Scholarship and concession management
- [x] Student ID card PDF (bulk)
- [x] Year-end promotion and class rollover
- [x] Transfer Certificate (TC) PDF
- [x] Student list Excel export

#### Module 3 — Academics
- [x] Academic year setup with current year flag
- [x] Holiday master and calendar view
- [x] Subject CRUD with class mapping
- [x] Structured syllabus builder (unit → chapter → topic)
- [x] Teacher subject allocation per class/section
- [x] Section-wise timetable entry
- [x] Homework and assignment creation

#### Module 4 — Attendance
- [x] Daily attendance (one-click bulk present + mark absent)
- [x] Period-wise attendance per subject
- [x] Student leave management (apply, approve, reject)
- [x] Staff daily attendance and monthly register PDF
- [x] Monthly student attendance register PDF
- [x] Attendance shortage letter PDF

#### Module 5 — Examinations
- [x] Exam schedule with subject-wise time slots
- [x] Custom grading scheme builder (grade ranges, GPA)
- [x] Question bank (MCQ, short answer, long answer, true/false)
- [x] Hall ticket PDF generation (bulk by class)
- [x] Marks entry with validation (max marks, absent flag)
- [x] Auto result computation (total, %, grade, rank)
- [x] Report card PDF
- [x] Tabulation register with Excel export

#### Module 6 — Fees
- [x] Fee structure builder (heads × classes × academic year)
- [x] Cash/cheque/partial fee collection with auto-receipt
- [x] Installment plan management
- [x] Late fee rules (flat / per-day / percentage)
- [x] Stackable concessions with validity dates
- [x] Student fee ledger with PDF
- [x] Day book with PDF
- [x] Receipt cancellation with reason log
- [x] Tally XML and CSV export

#### Module 7 — HR & Payroll
- [x] Employee master (personal, bank, joining details)
- [x] Department and designation CRUD
- [x] Leave application with HOD → Principal approval workflow
- [x] Team leave calendar view
- [x] Monthly payroll processing and net salary computation
- [x] Payslip PDF per employee
- [x] Bank transfer file (NEFT Excel)
- [x] Monthly salary register Excel export
- [x] Appointment letter PDF
- [x] Experience certificate PDF

#### Module 8 — Library
- [x] Book catalogue (full metadata, condition, location)
- [x] Book issue and return with automatic fine calculation
- [x] Reservation queue management (mark ready, cancel)
- [x] Fine waiver by librarian with reason
- [x] Digital resources (file upload or external URL)
- [x] Member list with printable library card PDF
- [x] Stock register with Excel export

#### Module 9 — Transport
- [x] Route and stop master with sequence order
- [x] Vehicle fleet with 5 document expiry trackers (fitness, insurance, permit, PUC, tax)
- [x] Student route allotment
- [x] Stop-wise and route-wise student roster
- [x] Vehicle maintenance log
- [x] Fuel log with efficiency (km/litre) tracking
- [x] Document expiry color-coded alerts

#### Module 10 — Hostel
- [x] Hostel and room master
- [x] Student room allotment with letter PDF
- [x] Room-type wise fee structure configuration
- [x] Mess menu creation and publishing
- [x] Outpass workflow (apply → warden approve → return log)
- [x] Hostel visitor management (check-in / check-out)
- [x] Maintenance complaints (Open → In Progress → Resolved)

---

### Not Yet Built

#### Module 11 — Parent and Student Portal
- Parent dashboard (attendance, fees, results, notices in one view)
- Student dashboard (timetable, homework, exam schedule)
- Online fee payment via Razorpay / PayU
- Portal login with OTP authentication
- Leave application from parent portal

#### Module 12 — Communications
- SMS gateway integration (MSG91 / Textlocal)
- WhatsApp Business API
- Bulk email via SMTP
- Auto-trigger notifications (absence, fee due, birthday wish, low attendance)

#### Module 13 — Analytics Dashboard
- Executive KPI dashboard (student strength, fee collection, attendance %)
- Attendance and fee trend charts
- Custom report builder (drag-and-drop fields)

#### Module 14 — Inventory and Store
- Item master, vendor management, purchase orders (PR → PO → GRN)
- Stock issuance and return
- Low stock alerts

#### Module 15 — Events and Calendar
- Event creation with target audience and RSVP
- Public and portal-facing school calendar
- Photo gallery per event

#### Module 16 — Visitor / Gate Pass (School Level)
- School gate visitor registration and log
- Student outpass at gate level
- Pre-registered visitor and blacklist management

#### Module 17 — LMS (Online Learning)
- Course builder (unit → chapter → lesson)
- Video / PDF / quiz lesson types
- Student progress tracking

#### Module 18 — Alumni Management
- Alumni self-registration and directory
- Events, donation campaigns, batch newsletters

#### Module 20 — System Administration
- RBAC UI (assign roles and permissions via interface)
- Audit trail log viewer
- Database backup and restore utility
- School branding configuration (logo, letterhead, stamp)
- SMS / email / payment gateway API settings page

---

## Database Summary

- **60 Eloquent models** — all with fillable, casts, and relationships
- **20+ migration files** — all migrated, no pending
- Key tables: `students`, `student_enrollments`, `employees`, `fee_payments`, `attendance_records`, `exam_marks`, `book_issues`, `transport_allotments`, `hostel_allotments`, `payroll_records`
- Soft deletes on `students` and `employees`

## Routes Summary

- **230 named routes** across all 10 admin modules
- Naming convention: `module.resource.action` (e.g. `fees.daybook.pdf`, `attendance.leave.approve`)
- All admin routes protected by `auth` middleware
- Only `/enquiry` is public (no login required)

---

## Environment Variables

```env
APP_NAME="School ERP"
APP_URL=http://127.0.0.1:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_erp
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=database
CACHE_DRIVER=file
SESSION_DRIVER=file
FILESYSTEM_DISK=local
```

---

*DC Innovision Pvt Ltd — School ERP v1.0 — June 2026*
