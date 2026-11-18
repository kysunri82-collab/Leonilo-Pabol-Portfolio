# Secure Dynamic Personal Portfolio Website Using PHP and MySQL

**ITS 300-B Final Project**

This project is a responsive personal portfolio. PHP pages retrieve portfolio content from MySQL, and an authenticated admin area provides tools to manage the content.

- **Live website:** [leonilopabol.freedev.app](https://leonilopabol.freedev.app/)
- **GitHub repository:** [kysunri82-collab/Leonilo-Pabol-Portfolio](https://github.com/kysunri82-collab/Leonilo-Pabol-Portfolio)

## Public pages

- `index.php` — Home and profile information
- `skills.php` — Skills from the database
- `projects.php` — Projects from the database, including project images when present
- `resume.php` — Education, experience, and achievements from the database
- `contact.php` — Contact information and submission form
- `about.html` — About page

Some older static `.html` pages remain in the project. Use the PHP pages above for dynamic portfolio content. `resume.html` redirects visitors to `resume.php` and provides a fallback link.

## Main features

- Admin dashboard with database counts and links to management pages.
- Create, list, edit, and delete tools for projects, skills, education, experience, and achievements.
- Admin profile and website settings for portfolio information, website title, logo, and appearance color.
- Contact form submissions stored in MySQL, with an admin inbox to read, mark, and delete messages.
- Server-side form validation, CSRF tokens, prepared database statements, and escaped output.
- Admin sessions with login rate limiting and protected management pages.
- Image uploads for JPG, PNG, and WebP with server-side content checks, a 5 MB limit, image-dimension checks, and generated filenames.
- Responsive layout with shared CSS and JavaScript for navigation and page interactions.

## Technologies

- PHP and PDO
- MySQL/MariaDB
- HTML, CSS, and vanilla JavaScript
- Apache `.htaccess` rules
- XAMPP and phpMyAdmin for local development

## Project structure

```text
portfolio/
├── index.php
├── skills.php
├── projects.php
├── resume.php
├── contact.php
├── about.html
├── skills.html              # Older static page
├── projects.html            # Older static page
├── resume.html              # Redirects to resume.php
├── contact.html             # Older static page
├── site_settings.php
├── admin/                   # Login, dashboard, profile, settings, and CRUD pages
├── includes/                # Database, authentication, security, and upload helpers
├── css/style.css
├── js/script.js
├── images/                  # Site images
├── uploads/                  # Uploaded images and .htaccess protection
├── private_config/
│   ├── .htaccess
│   └── db_config.php        # Private; excluded from Git and deployment ZIP
├── database/portfolio.sql  # Table definitions and limited sample data
├── .htaccess
├── .gitignore
├── Dockerfile               # Not used by InfinityFree
├── .dockerignore            # Not used by InfinityFree
├── Caddyfile                # Not used by InfinityFree
└── README.md
```

The repository also has Docker/Caddy deployment files. They are not needed for InfinityFree, which runs the PHP site through its Apache hosting.

## Run locally with XAMPP

1. Install XAMPP and place the project folder at `D:\xampp\htdocs\portfolio` (or your XAMPP `htdocs` directory).
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open phpMyAdmin, create a database named `leonilo_portfolio`, select it, and import `database/portfolio.sql`.
4. Open `http://localhost/portfolio/` in your browser.

`includes/db.php` manages the PDO connection. For local requests to XAMPP, it keeps development defaults and can also read the `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASSWORD`, and `DB_PORT` environment variables. Do not put real credentials in this README or other tracked files.

### Database backup contents

The checked-in SQL file creates the portfolio tables. Its included data rows are for Experience and Achievements; it does not create an administrator account or populate the other public-content tables. Add or restore your profile, projects, skills, education, and settings data before expecting those sections to show your full content.

## Admin access

Open `http://localhost/portfolio/admin/login.php` locally, or visit `/admin/login.php` on the hosted site. The SQL backup intentionally has no administrator account. Create the first account privately using a password hash generated with PHP's `password_hash()` and insert it into the `admins` table. Do not publish the password or hash in GitHub, this README, or a public SQL export.

## Database configuration and InfinityFree deployment

InfinityFree does not provide PHP environment variables in the same way as many application platforms. When production environment variables are unavailable, `includes/db.php` loads `private_config/db_config.php`. That file is excluded from Git and the deployment ZIP, and `private_config/.htaccess` denies direct HTTP access to its folder.

For deployment:

1. Create a MySQL database in the InfinityFree control panel and import `database/portfolio.sql` through phpMyAdmin. Import any additional data export separately if you have one; the checked-in backup does not contain all live portfolio content.
2. Extract the application files directly into the domain's `htdocs` directory, preserving their folders. Do not upload `.git`, `.env` files, SQL exports, or the Docker/Caddy deployment files.
3. Create or upload `private_config/db_config.php` separately on the hosting account. Put the database values shown in the hosting panel there; do not add the file to GitHub or share its contents.
4. Upload the required images under `uploads/` and keep `uploads/.htaccess` in place. The site uses HTTPS links for the live deployment.
5. Test the public pages, `/admin/login.php`, image display, and contact form after deployment.

The deployment ZIP intentionally omits `private_config/db_config.php`; the application cannot connect to the hosted database until that private file is supplied on the server.
