import os
import sys
import time
from playwright.sync_api import sync_playwright

BASE_URL = "http://127.0.0.1:8000"

ROUTES_TO_TEST = [
    ("/dashboard", "Dashboard"),
    ("/admissions", "Admissions"),
    ("/admissions/pipeline", "Admission Pipeline"),
    ("/admissions/seats", "Seat Availability"),
    ("/admissions/waitlist", "Waitlist Management"),
    ("/students", "Student Directory"),
    ("/students/promotions", "Student Promotions"),
    ("/students/id-cards", "ID Cards Generator"),
    ("/classes", "Classes List"),
    ("/classes/1", "Class Detail (Class 1)"),
    ("/academics", "Academics Overview"),
    ("/academics/subjects", "Subjects Management"),
    ("/academics/syllabus", "Syllabus Planning"),
    ("/academics/timetable", "Timetable Management"),
    ("/academics/notices", "Notice Board"),
    ("/attendance", "Attendance Marking"),
    ("/attendance/report", "Attendance Report"),
    ("/attendance/shortage", "Attendance Shortage"),
    ("/attendance/staff", "Staff Attendance"),
    ("/examinations", "Examinations List"),
    ("/fees", "Fee Dashboard"),
    ("/fees/structure", "Fee Structure"),
    ("/hr", "HR Overview"),
    ("/hr/employees", "Employee Directory"),
    ("/library", "Library Books"),
    ("/transport", "Transport Vehicles"),
    ("/hostel", "Hostel Buildings"),
    ("/reports/general-register", "General Register"),
    ("/reports/tc-register", "TC Register"),
    ("/reports/fee-collection-register", "Fee Collection Register"),
    ("/reports/attendance-register", "Attendance Register")
]

def main():
    with sync_playwright() as p:
        browser = p.chromium.launch(headless=True, channel="msedge")
        context = browser.new_context(viewport={"width": 1440, "height": 900})
        page = context.new_page()

        print("Navigating to login...")
        sys.stdout.flush()
        page.goto(f"{BASE_URL}/login", wait_until="domcontentloaded")
        page.fill("#email-field", "admin@schoolerp.in")
        page.fill("#pass-field", "Admin@1234")
        page.click(".submit-btn")
        page.wait_for_url("**/dashboard**")
        print("Logged in successfully!")
        sys.stdout.flush()

        print("=== CHECKING ROUTES FOR ERRORS ===")
        sys.stdout.flush()
        for route, label in ROUTES_TO_TEST:
            url = f"{BASE_URL}{route}"
            try:
                res = page.goto(url, wait_until="domcontentloaded", timeout=10000)
                time.sleep(1)
                status = res.status if res else "Unknown"
                content = page.content()
                title = page.title()
                
                is_error = False
                error_reason = ""

                if status >= 400:
                    is_error = True
                    error_reason = f"HTTP {status}"
                elif "404 | Not Found" in content or "500 | Server Error" in content or "Server Error" in title:
                    is_error = True
                    error_reason = f"Page Title/Content Error: {title}"
                elif "The route" in content and "could not be found" in content:
                    is_error = True
                    error_reason = "Route not found"
                
                if is_error:
                    print(f"[ERROR] {label} ({route}) -> {error_reason}")
                else:
                    print(f"[OK] {label} ({route}) -> HTTP {status} | Title: {title}")
            except Exception as e:
                print(f"[EXCEPTION] {label} ({route}) -> {e}")
            sys.stdout.flush()

        browser.close()

if __name__ == "__main__":
    main()
