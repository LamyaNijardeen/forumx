# 📰 ForumX

A minimal blogging platform for creating and sharing blogs.  
This README explains how to set up the project locally and where each file belongs.

---

##  Key Features

- User registration/login  
- Roles (user/admin)  
- Manage user profiles  
- Blog posts with optional image upload 
- Edit, delete blogs options for owners 
- Search posts by title  
- Mobile-friendly header with a hamburger menu  
- CSRF token protection and session-based authentication  

---

##  Requirements

- PHP 8+ (with `mysqli` extension)  
- MySQL  
- Web server or PHP built-in server  
- Write permission for `assets/images/` (for uploads)

---

##  Project Layout (Important Files & Folders)

- forumx/
- │
- ├─ includes/
- │ ├─ config.php # DB + session
- │ ├─ auth.php # auth helpers
- │ └─ csrf.php # CSRF helpers
- │
- ├─ pages/
- │ ├─ about.php
- │ ├─ create_blog.php
- │ ├─ delete_blog.php
- │ ├─ edit_blog.php
- │ ├─ entry.php
- │ ├─ home.php
- │ ├─ login.php
- │ ├─ logout.php
- │ ├─ profile.php
- │ ├─ register.php
- │ ├─ upload_profile_pic.php
- │ └─ view_blog.php
- │
- ├─ assets/
- │ ├─ css/
- │ │ ├─ about.css
- │ │ ├─ create_blog.css
- │ │ ├─ edit_blog.css
- │ │ ├─ entry.css
- │ │ ├─ home.css
- │ │ ├─ login.css
- │ │ ├─ profile.css
- │ │ ├─ register.css
- │ │ └─ view_blog.css
- │ │
- │ ├─ profile_photos/
- │ └─ images/ # Uploaded blog images
- │
- ├─ .env
- ├─ .gitignore
- ├─ index.php
- └─ README.md



---

##  Setup

### 1 Put files in place
- Copy PHP pages into the `pages/` folder  
- Copy `config.php`, `auth.php`, and `csrf.php` into `includes/`  
- Copy CSS into `assets/css/` and images into `assets/images/`

---

### 2 Create `.env` in project root

DB_HOST=your-database-host
DB_USER=your-database-username
DB_PASS=your-database-password
DB_NAME=your-database-name

sql
Copy code

Update values with your actual database credentials.

---

### 3 Create the Database and Tables

Run this SQL in MySQL / phpMyAdmin:

```sql
CREATE DATABASE IF NOT EXISTS forumx_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE forumx_db;

CREATE TABLE user (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  profile_photo VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE blogpost (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  content TEXT NOT NULL,
  image_path VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES user(id) ON DELETE CASCADE
);
```
### 4 Make uploads folder writable.
Ensure assets/images/ and assets/profile_photos/ exist and are writable by the webserver.

### 5 Start local server
bash
Copy code
cd path/to/forumx
php -S localhost:8000
Then open:
👉 http://localhost:8000/pages/entry.php

How to Use
Visit pages/register.php → create an account.

Log in at pages/login.php.

Create posts at pages/create_blog.php.

View posts on pages/home.php and pages/view_blog.php.

Edit or delete your posts at pages/profile.php.

-> Common Troubleshooting
*Session warnings:
Ensure session_start() is called only once (keep it in includes/config.php).

*Undefined function errors:
Check all helper files are included correctly using:
require_once __DIR__ . '/../includes/config.php';

*Image upload fails:
Verify permissions on assets/images/ and increase upload_max_filesize in php.ini.

*CSRF token error:
Add this hidden field inside your form:

html
Copy code
<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">
Warnings for null values:
Use htmlspecialchars($var ?? '') for safe output.

-> Deployment Notes
Upload your project to your hosting provider (FTP or File Manager).

Edit paths according to your hosting file structure.

Import the database schema and update .env with host DB credentials.

Ensure assets/images/ and assets/profile_photos/ are writable on the server.

### Author
Lamya Nijardeen

[GitHub](https://github.com/LamyaNijardeen/forumx)

[Live Demo](https://forumx-blog.infinityfreeapp.com/pages/entry.php)

