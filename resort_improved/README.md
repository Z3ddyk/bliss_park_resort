# Bliss Park Resort - Management System

A complete resort booking and management system built with **PHP**, **MySQL**, **HTML/CSS**, and **JavaScript**.

---

## 📋 Features

### Guest Features:
- **User Registration** with password hashing and validation
- **User Login** with session management
- **Browse Rooms** with type and status filters
- **Book Rooms** with dynamic price calculator
- **View Bookings** with booking history
- **Cancel Bookings** 
- **Contact Form** that saves messages to database

### Admin Features:
- **Dashboard** with live statistics (rooms, bookings, revenue, guests)
- **Manage Rooms** - Full CRUD (Add, Edit, Delete rooms)
- **Booking Reports** - View all bookings with status filters
- **Contact Messages** - View, mark as read, delete guest messages
- **Role-based Access Control** - Admin-only pages protected

### Technical Features:
- Prepared statements (SQL injection prevention)
- Password hashing with `password_hash()` and `password_verify()`
- Session-based authentication
- Client-side JavaScript form validation
- Server-side PHP validation
- Responsive CSS design (mobile-friendly)
- CSS custom properties (variables)
- CSS Grid and Flexbox layouts

---

## 🛠️ Setup Instructions

### Prerequisites:
- **XAMPP** installed (Apache + MySQL + PHP)

### Step 1: Copy Files
1. Copy the entire `resort_improved` folder into `C:\xampp\htdocs\`
2. Rename it to `resort` (or any name you prefer)

### Step 2: Create Database
1. Start **Apache** and **MySQL** from the XAMPP Control Panel
2. Open **phpMyAdmin**: http://localhost/phpmyadmin
3. Click **"Import"** tab
4. Select the `database.sql` file from the project folder
5. Click **"Go"** to import

### Step 3: Generate Admin Password
1. Open your browser and go to: `http://localhost/resort/hash.php`
2. Copy the SQL command shown on the page
3. In phpMyAdmin, click the **"SQL"** tab
4. Paste and run the SQL command to set the admin password
5. **Delete `hash.php`** from the project folder

### Step 4: Configure Database Connection
If your MySQL settings differ from default, edit `includes/db.php`:
```php
$host = "127.0.0.1";   // Your MySQL host
$user = "root";          // Your MySQL username
$pass = "";              // Your MySQL password
$dbname = "resort_management"; // Database name
```

### Step 5: Access the System
- **Homepage**: http://localhost/resort/index.php
- **Register**: http://localhost/resort/register.php
- **Login**: http://localhost/resort/login.php
- **Admin Dashboard**: http://localhost/resort/admin/dashboard.php

### Default Admin Credentials:
- **Email**: admin@blisspark.com
- **Password**: admin123 (after running hash.php)

---

## 📁 Project Structure

```
resort_improved/
├── admin/                    # Admin panel pages
│   ├── dashboard.php         # Admin dashboard with stats
│   ├── manage_rooms.php      # Room CRUD operations
│   ├── reports.php           # All bookings report
│   └── messages.php          # Contact messages viewer
├── assets/
│   ├── css/
│   │   └── style.css         # Main stylesheet (CSS3)
│   ├── js/
│   │   └── script.js         # JavaScript validation & interactivity
│   └── images/               # Room images folder
├── includes/
│   ├── db.php                # Database connection
│   ├── auth.php              # User authentication guard
│   ├── admin_auth.php        # Admin authentication guard
│   ├── header.php            # Shared header/navigation
│   └── footer.php            # Shared footer
├── uploads/                  # File uploads directory
├── index.php                 # Homepage
├── login.php                 # User login
├── register.php              # User registration
├── logout.php                # Session logout
├── rooms.php                 # Room listing with filters
├── bookings.php              # Booking form
├── my_bookings.php           # User booking history
├── cancel.php                # Cancel booking handler
├── about.php                 # About page
├── contact.php               # Contact form
├── hash.php                  # Password hash generator
├── database.sql              # MySQL database schema + sample data
└── README.md                 # This file
```

---

## 📚 Course Topics Covered

| Lesson | Topic | Implementation |
|--------|-------|---------------|
| Lesson 1 | Web Design Overview | Full website structure |
| Lesson 2 | Design Principles | Layout, color scheme, navigation |
| Lesson 3 | HTML Introduction | Semantic HTML throughout |
| Lesson 4 | HTML Forms | Registration, Login, Booking, Contact forms |
| Lesson 5 | CSS | Custom properties, Grid, Flexbox, Media Queries |
| Lesson 6 | JavaScript Intro | Variables, DOM manipulation |
| Lesson 7 | JS Control Flow | If/else validation, loops |
| Lesson 8 | JS Functions & Events | Form validation, price calculator, events |
| Lesson 9 | PHP Introduction | Sessions, control structures, functions |
| Lesson 10 | PHP + Database | CRUD operations, prepared statements, form handling |
