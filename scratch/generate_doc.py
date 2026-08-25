import os
import sys
import shutil
import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import parse_xml
from docx.oxml.ns import nsdecls

WORKSPACE_DIR = r"e:\school_erp - Copy"
SCRATCH_DIR = os.path.join(WORKSPACE_DIR, "scratch")
SCREENSHOTS_DIR = os.path.join(SCRATCH_DIR, "screenshots")
PRIMARY_DOCX_PATH = os.path.join(WORKSPACE_DIR, "DASA_EduERP_Project_Documentation.docx")
SECONDARY_DOCX_PATH = os.path.join(WORKSPACE_DIR, "DASA_EduERP_Project_Documentation_v3.docx")
ARTIFACT_DOCX_PATH = r"C:\Users\mithu\.gemini\antigravity-ide\brain\1182a651-d098-43e3-b7ed-c6d19122d362\DASA_EduERP_Project_Documentation.docx"

# Strict Requirement: Pure Black Text for All Content
COLOR_BLACK = RGBColor(0, 0, 0)
COLOR_WHITE = RGBColor(255, 255, 255)

HEX_TITLE_BG = "F1F5F9"
HEX_HDR_BG = "E2E8F0"
HEX_ALT_ROW = "F8FAFC"
HEX_BORDER = "CBD5E1"
HEX_ACCENT_BG = "F1F5F9"
HEX_BLACK_BORDER = "000000"

def set_cell_background(cell, hex_color):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{hex_color}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=140, bottom=140, left=180, right=180):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(
        f'<w:tcMar {nsdecls("w")}>'
        f'<w:top w:w="{top}" w:type="dxa"/>'
        f'<w:bottom w:w="{bottom}" w:type="dxa"/>'
        f'<w:left w:w="{left}" w:type="dxa"/>'
        f'<w:right w:w="{right}" w:type="dxa"/>'
        '</w:tcMar>'
    )
    tcPr.append(tcMar)

def set_table_borders(table, hex_color=HEX_BORDER, sz="4", val="single"):
    tblPr = table._tbl.tblPr
    borders = parse_xml(
        f'<w:tblBorders {nsdecls("w")}>\n'
        f'  <w:top w:val="{val}" w:sz="{sz}" w:space="0" w:color="{hex_color}"/>\n'
        f'  <w:bottom w:val="{val}" w:sz="{sz}" w:space="0" w:color="{hex_color}"/>\n'
        f'  <w:left w:val="none"/>\n'
        f'  <w:right w:val="none"/>\n'
        f'  <w:insideH w:val="{val}" w:sz="{sz}" w:space="0" w:color="{hex_color}"/>\n'
        f'  <w:insideV w:val="none"/>\n'
        '</w:tblBorders>'
    )
    tblPr.append(borders)

def add_heading_1(doc, text):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(22)
    p.paragraph_format.space_after = Pt(8)
    p.paragraph_format.keep_with_next = True
    run = p.add_run(text)
    run.font.name = 'Calibri'
    run.font.size = Pt(18)
    run.font.bold = True
    run.font.color.rgb = COLOR_BLACK
    return p

def add_heading_2(doc, text):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(16)
    p.paragraph_format.space_after = Pt(6)
    p.paragraph_format.keep_with_next = True
    run = p.add_run(text)
    run.font.name = 'Calibri'
    run.font.size = Pt(14)
    run.font.bold = True
    run.font.color.rgb = COLOR_BLACK
    return p

def add_heading_3(doc, text):
    p = doc.add_paragraph()
    p.paragraph_format.space_before = Pt(12)
    p.paragraph_format.space_after = Pt(4)
    p.paragraph_format.keep_with_next = True
    run = p.add_run(text)
    run.font.name = 'Calibri'
    run.font.size = Pt(12)
    run.font.bold = True
    run.font.color.rgb = COLOR_BLACK
    return p

def add_paragraph(doc, text, bold_prefix=None, space_after=4):
    p = doc.add_paragraph()
    p.paragraph_format.space_after = Pt(space_after)
    p.paragraph_format.line_spacing = 1.15
    if bold_prefix:
        r_pre = p.add_run(bold_prefix)
        r_pre.font.name = 'Calibri'
        r_pre.font.size = Pt(11)
        r_pre.font.bold = True
        r_pre.font.color.rgb = COLOR_BLACK
    run = p.add_run(text)
    run.font.name = 'Calibri'
    run.font.size = Pt(11)
    run.font.color.rgb = COLOR_BLACK
    return p

def add_bullet(doc, lead_in, text):
    p = doc.add_paragraph(style='List Bullet')
    p.paragraph_format.space_after = Pt(3)
    p.paragraph_format.line_spacing = 1.15
    r_lead = p.add_run(lead_in + ": ")
    r_lead.font.name = 'Calibri'
    r_lead.font.size = Pt(11)
    r_lead.font.bold = True
    r_lead.font.color.rgb = COLOR_BLACK
    r_text = p.add_run(text)
    r_text.font.name = 'Calibri'
    r_text.font.size = Pt(11)
    r_text.font.color.rgb = COLOR_BLACK
    return p

def add_callout(doc, text, title="KEY HIGHLIGHT"):
    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    cell = table.cell(0, 0)
    cell.width = Inches(6.5)
    set_cell_background(cell, HEX_ACCENT_BG)
    set_cell_margins(cell, top=140, bottom=140, left=200, right=180)
    
    tcPr = cell._tc.get_or_add_tcPr()
    borders = parse_xml(
        f'<w:tcBorders {nsdecls("w")}>\n'
        f'  <w:left w:val="single" w:sz="24" w:space="0" w:color="{HEX_BLACK_BORDER}"/>\n'
        f'  <w:top w:val="none"/>\n'
        f'  <w:right w:val="none"/>\n'
        f'  <w:bottom w:val="none"/>\n'
        f'</w:tcBorders>'
    )
    tcPr.append(borders)

    p = cell.paragraphs[0]
    p.paragraph_format.space_after = Pt(2)
    r_t = p.add_run(f"📌 {title}\n")
    r_t.font.name = 'Calibri'
    r_t.font.size = Pt(10.5)
    r_t.font.bold = True
    r_t.font.color.rgb = COLOR_BLACK

    r_txt = p.add_run(text)
    r_txt.font.name = 'Calibri'
    r_txt.font.size = Pt(10.5)
    r_txt.font.italic = True
    r_txt.font.color.rgb = COLOR_BLACK
    
    p_space = doc.add_paragraph()
    p_space.paragraph_format.space_after = Pt(6)

def add_table_data(doc, headers, data_rows, col_widths=None):
    table = doc.add_table(rows=len(data_rows) + 1, cols=len(headers))
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    set_table_borders(table)

    hdr_cells = table.rows[0].cells
    for i, title in enumerate(headers):
        hdr_cells[i].text = title
        set_cell_background(hdr_cells[i], HEX_HDR_BG)
        set_cell_margins(hdr_cells[i], top=140, bottom=140, left=150, right=150)
        p = hdr_cells[i].paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.LEFT
        for run in p.runs:
            run.font.name = 'Calibri'
            run.font.size = Pt(10.5)
            run.font.bold = True
            run.font.color.rgb = COLOR_BLACK

    for r_idx, row_data in enumerate(data_rows):
        row_cells = table.rows[r_idx + 1].cells
        bg_color = HEX_ALT_ROW if r_idx % 2 == 1 else "FFFFFF"
        for c_idx, cell_value in enumerate(row_data):
            row_cells[c_idx].text = str(cell_value)
            set_cell_background(row_cells[c_idx], bg_color)
            set_cell_margins(row_cells[c_idx], top=120, bottom=120, left=150, right=150)
            p = row_cells[c_idx].paragraphs[0]
            for run in p.runs:
                run.font.name = 'Calibri'
                run.font.size = Pt(10)
                run.font.color.rgb = COLOR_BLACK

    if col_widths:
        for row in table.rows:
            for i, w in enumerate(col_widths):
                row.cells[i].width = Inches(w)

    p_space = doc.add_paragraph()
    p_space.paragraph_format.space_after = Pt(6)

def add_screenshot(doc, img_name, caption):
    img_path = os.path.join(SCREENSHOTS_DIR, img_name)
    if not os.path.exists(img_path):
        print(f"Warning: Screenshot {img_path} not found.")
        return

    table = doc.add_table(rows=1, cols=1)
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    table.autofit = False
    cell = table.cell(0, 0)
    cell.width = Inches(6.5)
    set_cell_background(cell, "FFFFFF")
    set_cell_margins(cell, top=100, bottom=100, left=100, right=100)
    
    tcPr = cell._tc.get_or_add_tcPr()
    borders = parse_xml(
        f'<w:tcBorders {nsdecls("w")}>\n'
        f'  <w:top w:val="single" w:sz="8" w:space="0" w:color="{HEX_BORDER}"/>\n'
        f'  <w:bottom w:val="single" w:sz="8" w:space="0" w:color="{HEX_BORDER}"/>\n'
        f'  <w:left w:val="single" w:sz="8" w:space="0" w:color="{HEX_BORDER}"/>\n'
        f'  <w:right w:val="single" w:sz="8" w:space="0" w:color="{HEX_BORDER}"/>\n'
        f'</w:tcBorders>'
    )
    tcPr.append(borders)

    p = cell.paragraphs[0]
    p.alignment = WD_ALIGN_PARAGRAPH.CENTER
    run = p.add_run()
    run.add_picture(img_path, width=Inches(6.3))

    p_cap = doc.add_paragraph()
    p_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_cap.paragraph_format.space_before = Pt(4)
    p_cap.paragraph_format.space_after = Pt(14)
    r_cap = p_cap.add_run(f"Figure: {caption}")
    r_cap.font.name = 'Calibri'
    r_cap.font.size = Pt(9.5)
    r_cap.font.italic = True
    r_cap.font.color.rgb = COLOR_BLACK

def build_document():
    doc = Document()
    
    for section in doc.sections:
        section.top_margin = Inches(1.0)
        section.bottom_margin = Inches(1.0)
        section.left_margin = Inches(1.0)
        section.right_margin = Inches(1.0)

    # ── COVER / TITLE SECTION ─────────────────────────────────────
    table_title = doc.add_table(rows=1, cols=1)
    table_title.alignment = WD_TABLE_ALIGNMENT.CENTER
    table_title.autofit = False
    cell_title = table_title.cell(0, 0)
    cell_title.width = Inches(6.5)
    set_cell_background(cell_title, HEX_TITLE_BG)
    set_cell_margins(cell_title, top=240, bottom=240, left=240, right=240)

    p_main = cell_title.paragraphs[0]
    p_main.alignment = WD_ALIGN_PARAGRAPH.LEFT
    r_t1 = p_main.add_run("DASA EduERP\n")
    r_t1.font.name = 'Calibri'
    r_t1.font.size = Pt(26)
    r_t1.font.bold = True
    r_t1.font.color.rgb = COLOR_BLACK

    r_t2 = p_main.add_run("Comprehensive School Enterprise Resource Planning System\n")
    r_t2.font.name = 'Calibri'
    r_t2.font.size = Pt(14)
    r_t2.font.bold = True
    r_t2.font.color.rgb = COLOR_BLACK

    r_t3 = p_main.add_run("Detailed Technical Architecture, Module Specifications, Security Matrix & Complete Visual Previews")
    r_t3.font.name = 'Calibri'
    r_t3.font.size = Pt(11)
    r_t3.font.italic = True
    r_t3.font.color.rgb = COLOR_BLACK

    p_meta = doc.add_paragraph()
    p_meta.paragraph_format.space_before = Pt(8)
    p_meta.paragraph_format.space_after = Pt(16)
    r_m = p_meta.add_run("Project Status: 100% Validated  |  Version: 1.0.0  |  Stack: Laravel 12 / PHP 8.2  |  Date: August 2026")
    r_m.font.name = 'Calibri'
    r_m.font.size = Pt(9.5)
    r_m.font.bold = True
    r_m.font.color.rgb = COLOR_BLACK

    add_callout(doc, "DASA EduERP is an end-to-end, enterprise-grade school management system engineered to automate academic, administrative, financial, and operational processes across K-12 schools and multi-branch educational networks. Built with Laravel 12, Blade, Tailwind CSS v3, and Alpine.js v3, it provides 13 core modules backed by 23 granular user roles.", "EXECUTIVE PROJECT SUMMARY")

    # ── SECTION 1: EXECUTIVE OVERVIEW & VISION ─────────────────────
    add_heading_1(doc, "1. Executive Overview & System Vision")
    add_paragraph(doc, "Educational institutions face complex operational challenges ranging from manual admission tracking, fragmented fee collection registers, paper-based attendance logging, error-prone report card generation, and compliance reporting. DASA EduERP unifies all departmental workflows into a centralized cloud platform.")
    
    add_heading_2(doc, "1.1 Key Institutional Objectives")
    add_bullet(doc, "Operational Automation", "Eliminate redundant data entry by linking admissions directly to student profiles, class enrollments, fee ledgers, and attendance registers.")
    add_bullet(doc, "Financial Accuracy & Auditability", "Track every fee transaction, partial payment, late penalty, and concession with automated receipt generation and audit logging.")
    add_bullet(doc, "Real-Time Stakeholder Visibility", "Provide tailored portals for Administrators, Teachers, Accountants, HR Managers, Parents, and Students.")
    add_bullet(doc, "Statutory & Regulatory Compliance", "Generate government-mandated General Registers, Transfer Certificate (TC) logs, PF/ESI statutory challans, and attendance shortage warning letters.")

    # ── SECTION 2: TECHNICAL ARCHITECTURE & STACK ──────────────────
    add_heading_1(doc, "2. Technical Architecture & Technology Stack")
    add_paragraph(doc, "DASA EduERP follows modern Model-View-Controller (MVC) design patterns, clean database normalization, modular Blade components, and reactive Alpine.js frontend micro-interactions.")

    tech_headers = ["Component Layer", "Selected Technology", "Architectural Rationale & Benefits"]
    tech_rows = [
        ["Backend Engine", "PHP 8.2 + Laravel 12", "High-performance framework providing Eloquent ORM, secure authentication guards, middleware pipelines, and robust CLI tooling."],
        ["Frontend UI Framework", "Blade + Tailwind CSS v3", "Custom utility-first design system with responsive layouts, consistent color tokens, and optimized CSS bundles."],
        ["Reactivity Engine", "Alpine.js v3", "Lightweight frontend script engine handling client-side modal toggles, dynamic row additions, and reactive search inputs."],
        ["Database Layer", "MySQL 8.x / PostgreSQL 14+", "Relational database enforcing strict foreign keys, transactional safety, indexing, and soft-delete capabilities."],
        ["PDF Rendering Engine", "barryvdh/laravel-dompdf v3.1", "HTML-to-PDF rendering engine producing crisp fee receipts, student ID cards, payslips, and hall tickets."],
        ["Excel Data Engine", "maatwebsite/excel v3.1", "Bulk spreadsheet processor enabling CSV/XLSX import of student records and export of financial reports."],
        ["Access Guard (RBAC)", "Spatie Laravel-Permission v6", "Granular role and permission management middleware guarding every HTTP route."],
        ["Build & Bundling", "Vite 7", "Next-gen build tool compiling CSS/JS assets with hot module replacement (HMR) during development."]
    ]
    add_table_data(doc, tech_headers, tech_rows, [1.5, 2.0, 3.0])

    # ── SECTION 3: USER ROLES & ACCESS CONTROL ─────────────────────
    add_heading_1(doc, "3. User Roles & Security Matrix")
    add_paragraph(doc, "The system implements granular Role-Based Access Control (RBAC) via Spatie Laravel-Permission. User access is dynamically evaluated per request, restricting menu visibility and controller endpoints.")

    role_headers = ["Role Name", "Access Guard / Permission Scope", "Primary Operational Responsibilities"]
    role_rows = [
        ["Super Admin / Owner", "Global System Access", "Full administrative control, institution setup, user account creation, system logs."],
        ["School Admin / Principal", "Full Administrative Scope", "Academic oversight, teacher allocations, report approvals, Transfer Certificate issuance."],
        ["Vice Principal / HOD", "Departmental Scope", "Subject allocation, timetable supervision, syllabus progress monitoring, teacher reviews."],
        ["Class / Subject Teacher", "Classroom / Scope Guard", "Marking daily attendance, gradebook entries, homework management, conduct logs."],
        ["Accountant", "Finance & Fee Guard", "Fee collection, receipt issuance, concession processing, expense logging, financial reports."],
        ["HR Manager", "HR & Payroll Guard", "Staff directory, leave approvals, salary structure configuration, statutory PF/ESI payslips."],
        ["Librarian", "Library Guard", "Book cataloging, issuance, return tracking, overdue fine collection."],
        ["Transport Manager", "Transport Guard", "Vehicle fleet management, driver assignment, route stop configuration."],
        ["Hostel Warden", "Hostel Guard", "Dormitory room allocation, student bed assignments, gate pass approvals."],
        ["Student / Parent", "Portal Guard", "Viewing personal timetable, fee receipts, attendance summaries, report cards."]
    ]
    add_table_data(doc, role_headers, role_rows, [1.8, 2.0, 2.7])

    # ── SECTION 4: CORE FUNCTIONAL MODULES WITH COMPLETE SCREENSHOTS ─────────
    add_heading_1(doc, "4. Core Functional Modules & Visual Documentation")
    add_paragraph(doc, "Below is the complete architectural breakdown and visual preview of every core functional module and its associated sub-views / operational features within DASA EduERP.")

    # Module 1: Auth
    add_heading_2(doc, "4.1 Authentication & Multi-Role Gateway")
    add_paragraph(doc, "The authentication gateway provides a secure entry point for all institutional users. It includes CSRF protection, rate limiting against brute-force attacks, session regeneration, and quick demo role selection for rapid testing.")
    add_bullet(doc, "Security Protocol", "Bcrypt hashing with 12 rounds, active session validation, and scope middleware.")
    add_bullet(doc, "Multi-Identifier Support", "Allows users to log in using either their registered email address or mobile number.")
    add_screenshot(doc, "01_login.png", "Authentication Gateway & Multi-Role Demo Account Selection Screen")

    # Module 2: Dashboard
    add_heading_2(doc, "4.2 Executive Analytics Dashboard")
    add_paragraph(doc, "The executive dashboard serves as the central command hub for school administrators, presenting real-time statistical metrics, financial totals, attendance percentages, and quick-action shortcuts.")
    add_bullet(doc, "Real-Time Metric Cards", "Live counts of Total Active Students, Staff, Today's Collection, and Overall Attendance Rate.")
    add_bullet(doc, "Role-Specific Widgets", "Dynamically renders relevant dashboard panels based on whether the logged-in user is an Admin, Teacher, Accountant, or Warden.")
    add_screenshot(doc, "02_dashboard.png", "Super Admin Executive Analytics Dashboard & KPI Summary Cards")

    # Module 3: Admissions
    add_heading_2(doc, "4.3 Admissions & Enquiry Pipeline Management")
    add_paragraph(doc, "A comprehensive admissions pipeline managing public applicant inquiries, entrance examination scheduling, hall ticket generation, interview evaluation, seat allocation, and waitlist promotion.")
    add_bullet(doc, "Visual Pipeline Stages", "Inquiry Received -> Screening -> Entrance Test -> Interview -> Seat Confirmation.")
    add_bullet(doc, "Public Inquiry Portal", "Unauthenticated public endpoint allowing prospective parents to fill out application forms online.")
    add_screenshot(doc, "03_admissions.png", "Admission Enquiries Master List & Search Filter")
    add_screenshot(doc, "03b_admissions_pipeline.png", "Admission Pipeline Kanban Board View")
    add_screenshot(doc, "03c_admissions_seats.png", "Grade-wise Seat Availability & Capacity Management")
    add_screenshot(doc, "03d_admissions_waitlist.png", "Applicant Waitlist Management & Seat Promotion Queue")

    # Module 4: Student SIS
    add_heading_2(doc, "4.4 Student Information System (SIS)")
    add_paragraph(doc, "The student master directory acts as the single source of truth for all student data, storing personal information, parent contacts, document vaults with expiration alerts, health records, disciplinary logs, fee concessions, and Transfer Certificates.")
    add_bullet(doc, "Bulk Data Operations", "Import hundreds of student records via Excel template; generate printable batch ID cards in PDF.")
    add_bullet(doc, "Student Lifecycle History", "Tracks yearly class promotions, section transfers, and alumni statuses.")
    add_screenshot(doc, "04_students.png", "Student Information System (SIS) Master Directory & Search Filters")
    add_screenshot(doc, "04b_students_promotions.png", "Annual Class Promotion & Student Progression Studio")
    add_screenshot(doc, "04c_students_id_cards.png", "Wing-Wise Student ID Card Generator Studio")

    # Module 5: HR & Payroll
    add_heading_2(doc, "4.5 HR Management & Automated Payroll Engine")
    add_paragraph(doc, "End-to-end human resource engine managing employee onboarding, departmental designations, leave balances, salary structures, statutory PF/ESI/TDS deductions, and automated monthly payslip generation.")
    add_bullet(doc, "Statutory Deduction Engine", "Computes Employee Provident Fund (PF), ESI, Income Tax (TDS), and Professional Tax automatically.")
    add_bullet(doc, "Batch Payroll Processing", "One-click generation of monthly payslips with PDF export and bank payment registers.")
    add_screenshot(doc, "05_hr_employees.png", "HR Employee Directory & Staff Master Management")
    add_screenshot(doc, "05b_hr_payroll.png", "HR & Payroll Processing Overview Dashboard")

    # Module 6: Fees
    add_heading_2(doc, "4.6 Fee Management, Invoicing & Collections")
    add_paragraph(doc, "Flexible financial management module supporting customizable fee heads (Tuition, Admission, Transport, Lab), installment schedules, partial payment recording, concession grants, and instant PDF receipt generation.")
    add_bullet(doc, "Automated Ledger Updates", "Real-time adjustments to student pending balances upon receiving payments.")
    add_bullet(doc, "Thermal & A4 Receipts", "Prints branded fee receipts with QR code verification and fee itemization.")
    add_screenshot(doc, "06_fee_structure.png", "Fee Head Configuration & Grade-Wise Fee Structure")
    add_screenshot(doc, "06b_fees_collect.png", "Fee Collection Hub, Payment Recording & Ledger Dashboard")

    # Module 7: Academics
    add_heading_2(doc, "4.7 Academic Planning, Classes & Timetables")
    add_paragraph(doc, "Structure classes, sections, subjects, and weekly class timetables. Assign class teachers, monitor syllabus completion percentages, and manage substitute teacher allocations.")
    add_bullet(doc, "Class & Section Setup", "Define grades (Pre-KG to Grade 12), streams (Science, Commerce, Arts), and section capacities.")
    add_bullet(doc, "Conflict-Free Timetable Generator", "Ensures subject teachers and physical classrooms are never double-booked.")
    add_screenshot(doc, "07_academics.png", "Academic Management Overview & Course Setup")
    add_screenshot(doc, "07b_classes_list.png", "Classes & Sections Directory")
    add_screenshot(doc, "07c_class_detail.png", "Class Detail View, Student Roster & Weekly Timetable Schedule")
    add_screenshot(doc, "07d_academics_subjects.png", "Subject Management & Course Allocation")
    add_screenshot(doc, "07e_academics_notices.png", "Institutional Notice Board & Communications Engine")

    # Module 8: Attendance
    add_heading_2(doc, "4.8 Attendance Tracking & Shortage Automation")
    add_paragraph(doc, "Daily attendance recording for students and staff supporting Present, Absent, Late, and Half-Day statuses. Features automated percentage calculation, monthly attendance registers, and automated attendance shortage letter PDF generation for students below 75%.")
    add_bullet(doc, "Shortage Alert System", "Identifies chronic absentees and generates printable warning notices for parents.")
    add_screenshot(doc, "08_attendance.png", "Daily Student Attendance Marking Screen")
    add_screenshot(doc, "08b_attendance_report.png", "Class-Wise & Monthly Attendance Summary Report")
    add_screenshot(doc, "08c_attendance_shortage.png", "Attendance Shortage Tracking (Below 75%) & Notice Generator")
    add_screenshot(doc, "08d_attendance_staff.png", "Staff & Faculty Daily Attendance Register")

    # Module 9: Examinations
    add_heading_2(doc, "4.9 Examination, Gradebooks & Report Cards")
    add_paragraph(doc, "Complete evaluation engine managing exam schedules, marks entry per subject, grade scale configuration, GPA calculation, pass/fail thresholds, and multi-term report card generation.")
    add_bullet(doc, "Multi-Format Grading", "Supports percentage marks, grade letters (A+, A, B, C), and grade points.")
    add_bullet(doc, "Online Testing Engine", "Built-in online exam module with timed multiple-choice questions (MCQs) and automatic grading.")
    add_screenshot(doc, "09_examinations.png", "Examination Schedule, Gradebook & Evaluation Manager")

    # Module 10: Library
    add_heading_2(doc, "4.10 Library Management & Circulation Desk")
    add_paragraph(doc, "Catalog books by ISBN, author, publisher, and rack location. Manage book issuance, return processing, maximum borrow limits, overdue fine calculations, and reservation queues.")
    add_screenshot(doc, "10_library.png", "Library Book Catalog & Circulation Desk Manager")

    # Module 11: Transport
    add_heading_2(doc, "4.11 Transport & Vehicle Fleet Management")
    add_paragraph(doc, "Register school buses, vans, drivers, route stops, and pickup fees. Automatically sync transport fees into student monthly fee invoices.")
    add_screenshot(doc, "11_transport.png", "Transport Fleet, Route Stops & Vehicle Registration")

    # Module 12: Hostel
    add_heading_2(doc, "4.12 Hostel & Dormitory Management")
    add_paragraph(doc, "Manage hostel blocks, room types, bed capacities, student room assignments, mess fee structures, and digital gate pass approvals.")
    add_screenshot(doc, "12_hostel.png", "Hostel Buildings, Dormitories & Bed Allocation Manager")

    # Module 13: Reports
    add_heading_2(doc, "4.13 Reports, Statutory Registers & Exports")
    add_paragraph(doc, "Executive reporting suite generating government-mandated General Student Registers, TC Registers, Fee Collection Summaries, Monthly Attendance Registers, and Excel data exports.")
    add_screenshot(doc, "13_general_register.png", "Government Mandated Student General Register")
    add_screenshot(doc, "13b_tc_register.png", "Transfer Certificate (TC) Issuance Register")
    add_screenshot(doc, "13c_fee_collection_register.png", "Fee Collection & Receipt History Register")
    add_screenshot(doc, "13d_attendance_register.png", "Monthly Master Attendance Register & Log")

    # ── SECTION 5: DATABASE ARCHITECTURE SUMMARY ───────────────────
    add_heading_1(doc, "5. Database Schema & Architecture")
    add_paragraph(doc, "DASA EduERP relies on a highly normalized relational database design enforcing primary key indexing, foreign key constraints, timestamps, and soft deletes across all core entities.")

    db_headers = ["Domain / Model", "Primary Table", "Key Relationships & Operational Description"]
    db_rows = [
        ["User Auth", "users, roles, permissions", "Stores credentials, password hashes, and Spatie permission mappings."],
        ["Students (SIS)", "students, student_enrollments", "Stores student biodata, admission numbers, class enrollments, guardian contacts."],
        ["Admissions", "admissions, admission_seats", "Inquiry records, entrance exam scores, seat capacities per grade."],
        ["HR & Payroll", "employees, payslips, statutory_rules", "Staff master profiles, salary components, statutory PF/ESI monthly payslips."],
        ["Academics", "classes, class_sections, subjects", "Academic grade levels, section capacities, subject allocations."],
        ["Fee Management", "fee_structures, fee_payments", "Fee heads, installment due dates, student receipt logs."],
        ["Attendance", "attendance_records", "Daily student/staff attendance status, arrival times, cutoff overrides."],
        ["Examinations", "examinations, exam_marks", "Exams schedule, subject-wise marks, pass/fail status."]
    ]
    add_table_data(doc, db_headers, db_rows, [1.5, 2.0, 3.0])

    # ── SECTION 6: INSTALLATION & DEPLOYMENT GUIDE ──────────────────
    add_heading_1(doc, "6. Setup, Installation & Deployment Guide")
    add_paragraph(doc, "Follow this developer guide to configure and run DASA EduERP locally or deploy to server environments.")

    add_heading_2(doc, "6.1 System Prerequisites")
    add_bullet(doc, "PHP Version", "PHP 8.2+ with pdo_mysql, gd, zip, mbstring, openssl extensions enabled.")
    add_bullet(doc, "Database Engine", "MySQL 8.0+ or PostgreSQL 14+ database server.")
    add_bullet(doc, "Node & Composer", "Node.js 18+ (with npm) and Composer 2.x.")

    add_heading_2(doc, "6.2 CLI Installation Commands")
    
    setup_cmd_text = (
        "# 1. Install Dependencies\n"
        "composer install\n"
        "npm install && npm run build\n\n"
        "# 2. Environment Configuration\n"
        "cp .env.example .env\n"
        "php artisan key:generate\n\n"
        "# 3. Database Migration & Data Seeding\n"
        "php artisan migrate\n"
        "php artisan db:seed\n\n"
        "# 4. Start Local Development Server\n"
        "php artisan serve"
    )
    add_callout(doc, setup_cmd_text, "DEVELOPER CLI SETUP COMMANDS")

    add_heading_2(doc, "6.3 Administrative Login Credentials")
    cred_headers = ["System Role", "Login Email", "Password"]
    cred_rows = [
        ["Super Admin", "admin@schoolerp.in", "Admin@1234"],
        ["School Admin", "schooladmin@schoolerp.in", "Demo@2026"],
        ["Principal", "principal@schoolerp.in", "Demo@2026"],
        ["Accountant", "accountant@schoolerp.in", "Demo@2026"],
        ["HR Manager", "hr@schoolerp.in", "Demo@2026"]
    ]
    add_table_data(doc, cred_headers, cred_rows, [2.0, 2.5, 2.0])

    # Save Document safely handling file lock
    target_path = PRIMARY_DOCX_PATH
    try:
        doc.save(PRIMARY_DOCX_PATH)
        print(f"Document successfully created at: {PRIMARY_DOCX_PATH}")
    except PermissionError:
        target_path = SECONDARY_DOCX_PATH
        doc.save(SECONDARY_DOCX_PATH)
        print(f"Primary file locked by Word. Document created at: {SECONDARY_DOCX_PATH}")

    os.makedirs(os.path.dirname(ARTIFACT_DOCX_PATH), exist_ok=True)
    try:
        shutil.copy2(target_path, ARTIFACT_DOCX_PATH)
        print(f"Document copied to artifact path: {ARTIFACT_DOCX_PATH}")
    except Exception as e:
        print(f"Artifact copy note: {e}")

if __name__ == "__main__":
    build_document()
