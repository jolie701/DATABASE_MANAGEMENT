# DATABASE_MANAGEMENT
# Campus Psychological Counseling Reservation System - Database Core & Statistics

This repository contains the database schema implementations and backend statistical analysis modules for the Campus Psychological Counseling Reservation System. This component focuses on appointment records management, data aggregation, and administrative reporting.

---

## Team and Contributions

This project was a collaborative effort developed by a **5-member project team**. Within this team, my specific contributions were focused on the backend statistical architecture and fundamental relational database design:

* **My Core Responsibilities:**
  * **Database Schema Design:** Engineered the core database tables (`appointmentrecord` and `appointmentapply`), defining primary/foreign key relationships and applying structural adjustments via `ALTER TABLE`.
  * **Analytical Backend Modules:** Developed statistical reporting scripts (`workload.php`, `stat_top_consultant.php`, `stat_completion.php`) to aggregate and analyze counseling data.
  * **Administrative Management System:** Built the `admin_record.php` interface allowing system administrators to search, track, and audit historical appointment logs.

* **Other Team Components (Developed by 4 other members):**
  * Frontend UI/UX styling and layout responsive frameworks.
  * Student reservation dashboard interfaces and feedback questionnaire systems.
  * Microsoft Access desktop application prototyping and peripheral data query forms.
  
---

## Repository Structure

* `Campus Psychological Counseling Reservation System.sql` - The database schema initialization script containing table structures (`CREATE`, `ALTER`), constraints, and sample records for the appointment system.
* `workload.php` - Counselor workload analysis engine.
* `stat_top_consultant.php` - Peak demand and top consultant statistical reporter.
* `stat_completion.php` - Appointment fulfillment and status completion analyzer.
* `admin_record.php` - Administrative record management backend.

---

## Environment and Setup

### Prerequisites
* **Server Environment:** XAMPP, WampServer, or any standard LAMP/WAMP stack.
* **PHP Version:** PHP 7.4 / 8.0 or higher.
* **Database:** MySQL / MariaDB.

### Database Initialization
1. Open phpMyAdmin (`http://localhost/phpmyadmin/`) or your preferred MySQL terminal.
2. Create a new database named `consultation` with collation `utf8mb4_unicode_ci`.
3. Import the `consultation.sql` file to execute the schema creation and populate sample tracking data.

### Running the Modules
Place the PHP files into your server's web root directory (e.g., `C:/xampp/htdocs/`) ensuring they correctly point to your local database connection configurations (Host: `localhost`, User: `root`, Database: `consultation`). Access the administrative backend tools via:
`http://localhost/[your_directory]/admin_record.php`
