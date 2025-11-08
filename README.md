# ForumX
A minimal blogging platform for creating and sharing blogs.  
This README explains how to set up the project locally and where each file belongs.
### Key features:###
User registration/login, roles (user/admin)
Create/edit/delete blog posts with optional image upload
Search posts by title
Mobile-friendly header with a hamburger menu
CSRF token protection and session-based auth
---

## Requirements
- PHP 8+ (with 'mysql' extension)
- MySQL
- Web server or PHP built-in server
- File write permission for 'assets/images/' (for uploads)

---

## Project layout (important files & folders)
forumx/
|
├─ includes/
│ ├─ config.php # DB + session
│ ├─ auth.php # auth helpers
│ └─ csrf.php # CSRF helpers
|
├─ pages/
│ ├─ about.php
│ ├─ create_blog.php
│ ├─ delete_blog.php
│ ├─ edit_blog.php
│ ├─ entry.php
│ ├─ home.php
│ ├─ login.php
│ ├─ logout.php
│ ├─ profile.php
│ |_ register.php
│ |_ upload_profile_pic.php
| |_ view_blog.php
|
├─ assets/
│ ├─ css/
│ │ ├─ about.css
│ │ ├─ create_blog.css
│ │ ├─ edit_blog.css
│ │ ├─ entry.css
│ │ ├─ home.css
│ │ ├─ login.css 
│ │ ├─ profile.css
│ │ ├─ register.css
│ │ └─ view_blog.css
| |
│ ├─ profile_photos/
│ └─ images/
│ └─ (uploaded images go here)
|
│─ .env
|─ .gitignore
|─ index.php
└─ README.md




## Setup

1. **Put files in places**
   - Copy  PHP pages into 'pages/'.
   - Copy 'config.php', 'auth.php', 'csrf.php' into 'includes/'.
   - Copy CSS to ,assets/css/' and images to 'assets/images/'.

2. **Create '.env'** in project root:
DB_HOST=your-database-host
DB_USER=your-database-username
DB_PASS=your-database-password
DB_NAME=your-database-name
Update values with your DB credentials.

3. **Create database and tables** (run in MySQL / phpMyAdmin):
```sql
CREATE DATABASE IF NOT EXISTS forumx_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE forumx_db;

CREATE TABLE user (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user',
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
Make uploads folder writable

Ensure assets/images/ exists and is writable by the webserver.

Start local server (from project root):
cd path/to/forumx
php -S localhost:8000

Then open: http://localhost:8000/pages/entry.php

How to use

Visit pages/register.php → create account.

Log in at pages/login.php.

Create posts at pages/create_blog.php.

View posts on pages/home.php and pages/view_blog.php.

Edit your posts at pages/edit_blog.php and view your posts at pages/profile.php.

Common troubleshooting

Session warnings: session_start() must be called once (keep it in includes/config.php). Remove duplicate session_start() calls.

Undefined function errors: ensure all helper files are included (require_once __DIR__ . '/../includes/config.php';).

Image upload fails: check assets/images permissions and upload_max_filesize in php.ini.

CSRF error on submit: ensure the form includes:

<input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrf_token()); ?>">


htmlspecialchars(null) warnings: wrap potentially null values as htmlspecialchars($var ?? '').

Deployment notes

Upload project to your host (FTP or file manager).
Edit paths according to folders arranged in hosting apps.
Import DB schema on host and update .env with host DB credentials.

Ensure assets/images/ is writable on the server.


Minimal personal project (no license specified).
Author: Lamya Nijardeen — [GitHub](https://github.com/LamyaNijardeen/forumx)

Live demo:
https://forumx-blog.rf.gd/pages/entry.php 
