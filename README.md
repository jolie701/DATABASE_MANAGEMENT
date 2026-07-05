# DATABASE_MANAGEMENT
# Campus Psychological Counseling Reservation System - Database Core & Statistics

This repository contains the database schema implementations and backend statistical analysis modules for the Campus Psychological Counseling Reservation System. This component focuses on appointment records management, data aggregation, and administrative reporting.

---

## My Contributions and Scope

As a core developer of this project, my responsibilities focused on the database schema engineering and the implementation of administrative data visualization and statistical reporting modules.

### 1. Database Engineering (SQL)
Designed, created, and optimized the structural definitions and relationships for the core appointment workflow:
* **`appointmentrecord` Table:** Implemented the table structure and constraints to log completed, canceled, and ongoing counseling records.
* **`appointmentapply` Table:** Implemented the application tracking system handling initial student requests and metadata.
* **Schema Evolution:** Managed database migration and relations using `ALTER TABLE` statements to ensure data integrity and foreign key constraints.

### 2. Backend & Analytical Modules (PHP)
Developed the administrative interface and analytical backend to convert raw database records into actionable insights for campus administrators:
* `workload.php`: Analyzes and tracks the workload metrics of individual counselors over specific time frames.
* `stat_top_consultant.php`: Aggregates booking data to identify and report the most requested counselors and peak reservation periods.
* `stat_completion.php`: Computes completion, cancellation, and no-show rates across counseling sessions for service optimization.
* `admin_record.php`: The administrative portal for managing, searching, and auditing historical counseling logs and applications.

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
