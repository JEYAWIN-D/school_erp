import os
import sys
import time
from playwright.sync_api import sync_playwright

OUTPUT_DIR = os.path.join(os.path.dirname(__file__), "screenshots")
os.makedirs(OUTPUT_DIR, exist_ok=True)

BASE_URL = "http://127.0.0.1:8000"

PAGES_TO_CAPTURE = [
    ("01_login.png", f"{BASE_URL}/login"),
    ("02_dashboard.png", f"{BASE_URL}/dashboard"),
    ("03_admissions.png", f"{BASE_URL}/admissions"),
    ("03b_admissions_pipeline.png", f"{BASE_URL}/admissions/pipeline"),
    ("03c_admissions_seats.png", f"{BASE_URL}/admissions/seats"),
    ("03d_admissions_waitlist.png", f"{BASE_URL}/admissions/waitlist"),
    ("04_students.png", f"{BASE_URL}/students"),
    ("04b_students_promotions.png", f"{BASE_URL}/students/promotions"),
    ("04c_students_id_cards.png", f"{BASE_URL}/students/id-cards"),
    ("05_hr_employees.png", f"{BASE_URL}/hr/employees"),
    ("05b_hr_payroll.png", f"{BASE_URL}/hr"),
    ("06_fee_structure.png", f"{BASE_URL}/fees/structure"),
    ("06b_fees_collect.png", f"{BASE_URL}/fees"),
    ("07_academics.png", f"{BASE_URL}/academics"),
    ("07b_classes_list.png", f"{BASE_URL}/classes"),
    ("07c_class_detail.png", f"{BASE_URL}/classes/1"),
    ("07d_academics_subjects.png", f"{BASE_URL}/academics/subjects"),
    ("07e_academics_notices.png", f"{BASE_URL}/academics/notices"),
    ("08_attendance.png", f"{BASE_URL}/attendance"),
    ("08b_attendance_report.png", f"{BASE_URL}/attendance/report"),
    ("08c_attendance_shortage.png", f"{BASE_URL}/attendance/shortage"),
    ("08d_attendance_staff.png", f"{BASE_URL}/attendance/staff"),
    ("09_examinations.png", f"{BASE_URL}/examinations"),
    ("10_library.png", f"{BASE_URL}/library"),
    ("11_transport.png", f"{BASE_URL}/transport"),
    ("12_hostel.png", f"{BASE_URL}/hostel"),
    ("13_general_register.png", f"{BASE_URL}/reports/general-register"),
    ("13b_tc_register.png", f"{BASE_URL}/reports/tc-register"),
    ("13c_fee_collection_register.png", f"{BASE_URL}/reports/fee-collection-register"),
    ("13d_attendance_register.png", f"{BASE_URL}/reports/attendance-register"),
]

def main():
    print("Starting full deep screenshot capture process...")
    sys.stdout.flush()
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True, channel="msedge")
        context = browser.new_context(viewport={"width": 1440, "height": 900})
        page = context.new_page()

        # 1. Login page
        print("Navigating to login page...")
        sys.stdout.flush()
        page.goto(f"{BASE_URL}/login", wait_until="domcontentloaded")
        time.sleep(1)
        login_img = os.path.join(OUTPUT_DIR, "01_login.png")
        page.screenshot(path=login_img, full_page=False)
        print(f"Captured: {login_img}")
        sys.stdout.flush()

        # 2. Perform Login
        print("Logging in as Super Admin...")
        sys.stdout.flush()
        page.fill("#email-field", "admin@schoolerp.in")
        page.fill("#pass-field", "Admin@1234")
        page.click(".submit-btn")
        
        page.wait_for_url("**/dashboard**", timeout=15000)
        time.sleep(2)
        print(f"Successfully logged in! Current URL: {page.url}")
        sys.stdout.flush()

        # 3. Capture all pages & sub-pages
        for name, url in PAGES_TO_CAPTURE:
            if name == "01_login.png":
                continue
            print(f"Navigating to {url}...")
            sys.stdout.flush()
            try:
                page.goto(url, wait_until="domcontentloaded", timeout=15000)
                time.sleep(1.5)
                img_path = os.path.join(OUTPUT_DIR, name)
                page.screenshot(path=img_path, full_page=False)
                print(f"Captured: {img_path}")
                sys.stdout.flush()
            except Exception as e:
                print(f"Error capturing {name} at {url}: {e}")
                sys.stdout.flush()

        browser.close()
    print("All deep screenshots captured successfully!")
    sys.stdout.flush()

if __name__ == "__main__":
    main()
