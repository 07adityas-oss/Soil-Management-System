# Soil Management System

A database management project developed using MySQL to store, manage, and maintain soil sample information, including farmer details, soil types, pH values, nutrient levels, moisture, and recommended crops.

## 📌 About the Project

The Soil Management System is designed to make soil record management easier and more organised. It stores important information about soil samples, such as farmer name, location, soil type, pH value, nitrogen, phosphorus, potassium, moisture percentage, and recommended crops.

The system allows users to add, view, update, delete, and search soil records. It helps maintain soil information in a single database and makes it easier to access and manage records whenever required.

The project demonstrates the practical implementation of Database Management System (DBMS) concepts using MySQL, SQL, and PHP.

## 🎯 Objectives

- Store and manage soil sample records.
- Maintain farmer and location information.
- Record different types of soil.
- Store soil pH values and nutrient levels.
- Maintain nitrogen, phosphorus, and potassium details.
- Store soil moisture information.
- Add, update, view, and delete soil records.
- Search soil records easily.
- Maintain organised soil information.
- Practise relational database design and SQL queries.

## 🛠️ Technologies Used

- MySQL
- SQL
- PHP
- HTML
- CSS
- XAMPP
- MySQL Workbench
- Git and GitHub

## 📂 Project Structure

```
Soil-Management-System/
├── README.md
├── database/
│   └── soil_management.sql
├── config/
│   └── database.php
├── css/
│   └── style.css
├── index.php
├── add.php
├── edit.php
├── delete.php
└── screenshots/
    └── .gitkeep
```


## 🗄️ Database Tables

| Table        | Description                                                                                                            |
| ------------ | ---------------------------------------------------------------------------------------------------------------------- |
| soil_records | Stores soil sample details, farmer information, soil type, pH values, nutrient levels, moisture and recommended crops. |

### Soil Records Fields

| Column           | Description                            |
| ---------------- | -------------------------------------- |
| soil_id          | Unique ID for each soil record         |
| sample_code      | Unique soil sample identification code |
| farmer_name      | Name of the farmer                     |
| location         | Location of soil sample collection     |
| soil_type        | Type of soil                           |
| ph_value         | pH value of the soil                   |
| nitrogen         | Nitrogen level                         |
| phosphorus       | Phosphorus level                       |
| potassium        | Potassium level                        |
| moisture         | Soil moisture percentage               |
| recommended_crop | Suggested crop                         |
| sample_date      | Date of sample collection              |
| notes            | Additional information                 |

## ⚙️ Features

- Soil record management
- Farmer information management
- Soil sample details storage
- Soil type management
- pH value management
- Nitrogen, phosphorus and potassium details
- Moisture information storage
- Add new soil records
- Update existing soil records
- Delete soil records
- View all soil records
- Search soil records
- MySQL database connectivity
- Organised storage of soil information

## 🚀 How to Run the Project

### Step 1: Install XAMPP

Download and install XAMPP on your computer.

### Step 2: Start the Server

Open XAMPP Control Panel and start Apache and MySQL.

### Step 3: Open the Project

Clone this repository or download the project files from GitHub.

### Step 4: Create the Database

Open MySQL Workbench or phpMyAdmin and execute the SQL file:

`database/soil_management.sql`

### Step 5: Configure Database Connection

Open the PHP database connection file and configure the database name, username and password according to your MySQL setup.

### Step 6: Run the Project

Place the project folder inside the XAMPP `htdocs` directory.

Open your browser and visit:

`http://localhost/Soil-Management-System/`

### Step 7: Manage Soil Records

Use the project interface to add, view, update, delete and search soil sample information.

## 🧠 DBMS Concepts Demonstrated

- Relational database design
- Database creation and table creation
- Primary keys
- Unique constraints
- NOT NULL constraints
- CHECK constraints (if implemented)
- Foreign keys (if implemented)
- SQL CRUD operations
- INSERT, SELECT, UPDATE and DELETE queries
- Searching and filtering records
- Data integrity
- Database connectivity using PHP and MySQL

## 🔮 Future Enhancements

- User login and authentication
- Farmer-wise soil record management
- Soil quality analysis reports
- PDF report generation
- Graphical representation of soil data
- Advanced soil nutrient analysis
- Crop recommendation system
- Improved dashboard and user interface
- Soil sample history tracking

## ⚠️ Project Scope

This repository contains an educational Soil Management System developed for college DBMS coursework. It focuses on storing, managing, updating, deleting and retrieving soil sample information using a MySQL database.

The project does not physically test soil or automatically provide certified agricultural recommendations. Soil information is entered and maintained by users.

## 👨‍🎓 Project Information

Project Name: Soil Management System

Category: Database Management System (DBMS)

Database: MySQL

Backend: PHP

Frontend: HTML and CSS

Platform: XAMPP

Purpose: Academic project and SQL learning

## 📄 License

This project is available for educational and learning purposes.
