# SoilCare — Soil Management System

A beginner-friendly DBMS mini-project demonstrating Create, Read, Update and Delete (CRUD) operations on soil sample records.

## Stack
- HTML/CSS: user interface
- PHP 8+: server-side form handling
- MySQL 8+: relational database
- PDO prepared statements: database access

## Requirements
- XAMPP (or Apache + PHP + MySQL)
- A browser
- MySQL account credentials

## Run locally with XAMPP
1. Copy the `soil_management_project` folder into XAMPP's `htdocs` directory (for example `C:\\xampp\\htdocs\\soil_management_project`).
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`.
4. Select **Import**, choose `soil_management.sql`, and run it. The script creates the `soil_management` database, its table, constraints, indexes and three sample records.
5. If your MySQL username/password differs from XAMPP defaults, update `config.php`.
6. Open `http://localhost/soil_management_project/`.

## How to use
- Create: fill the left-hand form and click Create record.
- Read: records appear in the table.
- Search: search by sample code, farmer, location or soil type.
- Update: click Edit, change the form, and save changes.
- Delete: click Delete and confirm.

## Database
One table, `soil_records`, stores the sample and its measurements. `soil_id` is the primary key and `sample_code` is unique. The schema validates pH (0–14), moisture (0–100%) and non-negative nutrient readings. Indexes support common location/date lookups.

## Important notes
- The included data is fictional demonstration data; replace it with measured data for real use.
- This is an educational prototype, not a certified agronomy recommendation system.
- Use a dedicated, least-privilege MySQL account and a strong password before deploying beyond localhost.
- The interface uses an external Google Fonts stylesheet; the system otherwise works without JavaScript. If internet access is unavailable, a local fallback font is used.
- `CHECK` constraints are enforced by MySQL 8.0.16+; application-level validation is also included.
