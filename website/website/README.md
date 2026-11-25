# Interior Studio — PHP & MySQL

A responsive interior-design studio website and role-based project portal built with PHP 8+, MySQL, HTML, CSS and vanilla JavaScript. It is structured for learning and academic demonstration.

## Features

- Public Home, About, Services, Portfolio, Project Details and Contact pages.
- Client registration, login, logout, password change and PHP session handling.
- Three roles: Admin, Designer and Client.
- Admin: manage users and roles, projects, categories and design requests.
- Designer: view assigned projects, update progress/status and add progress notes.
- Client: browse the portfolio, submit requests and view their own requests/projects.
- Project search and category filtering, project image uploads, status tracking and dashboard counts.
- PDO prepared statements, password hashing, output escaping, CSRF tokens and role checks.

## Requirements

- XAMPP with Apache, PHP 8.1+ and MySQL/MariaDB.
- A modern browser.
- Internet connection for Google Fonts and the sample Unsplash photographs. Replace remote photographs with locally licensed images for an offline presentation.

## XAMPP setup

1. Extract the `interior-studio` folder into `C:\xampp\htdocs\` (Windows) or the XAMPP `htdocs` directory on your system.
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Open `http://localhost/phpmyadmin`.
4. Choose **Import**, select `database/interior_studio.sql`, and import it. The script creates the `interior_studio` database and tables.
5. Check `config/config.php`. The default XAMPP credentials are host `localhost`, user `root`, and an empty password. Change these values if your MySQL setup differs.
6. Visit `http://localhost/interior-studio/`.

## Demo accounts

| Role | Email | Password |
|---|---|---|
| Admin | `admin@interiorstudio.test` | `Studio123!` |
| Designer | `designer@interiorstudio.test` | `Studio123!` |
| Client | `client@interiorstudio.test` | `Studio123!` |

**Important:** Change demo passwords immediately using the Change password link in the dashboard (or remove the demo accounts for a live deployment). The sample database uses a pre-generated password hash for demonstration.

## Image uploads

Project images are stored in `uploads/`. The directory must be writable by the web server. The project form accepts JPG, PNG and WebP images up to 5 MB. For a production deployment, add server-level upload limits, image re-encoding, malware scanning and stricter storage isolation.

## Main folders

```text
interior-studio/
├── admin/                 # Admin-only management pages
├── assets/
│   ├── css/style.css      # Responsive visual design
│   └── js/app.js          # Navigation and small interactions
├── config/config.php      # PDO and session configuration
├── database/              # MySQL schema and starter data
├── designer/               # Designer project workflow
├── includes/               # Shared functions and page layout
├── uploads/                # Uploaded project photographs
├── index.php
├── dashboard.php
├── login.php
├── register.php
└── ...
```

## Academic project walkthrough

1. Import the SQL schema and explain the relationships between users, categories, projects, requests and project updates.
2. Register a client account and submit a design request.
3. Sign in as the admin and change the request status, create a project, assign a designer and client, and publish it.
4. Sign in as the designer and update progress with a note.
5. Sign in as the client to see the assigned project and status.
6. Demonstrate portfolio filtering, contact form submission and password change.

## Notes and production hardening

This is a functional educational foundation, not a production-audited deployment. Before publishing it on the internet, add email verification and password reset tokens, rate limiting, audit logs, stronger upload processing, pagination, automated tests, backups, HTTPS, secure cookie settings, and a privacy policy. Restrict administrator account creation to a trusted setup process. The public contact form stores messages in `contact_messages`; an admin inbox UI can be added as a follow-on feature.
