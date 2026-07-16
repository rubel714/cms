# CMS

A web-based Content / Client Management System with invoicing, billing and role-based access control. The project consists of a **React** single-page frontend and a **PHP + MySQL** REST API backend, designed to run under **XAMPP** (Apache + PHP 7.4 + MySQL).

---

## Features

- **Authentication & Authorization** — JWT-based login with role-to-menu permission mapping.
- **User Management** — user entry, user roles, teams, designations and departments.
- **Client Management** — clients, customer groups and business lines.
- **Invoicing & Billing** — invoice upload, invoice list, bill generation and payment receiving.
- **Dashboard** — summary view with charts (Highcharts).
- **Reporting** — PDF (TCPDF / FPDI) and Excel (PhpSpreadsheet) export.
- **Auditing** — audit logs and error logs.
- **Profile** — user profile management and password reset.

---

## Tech Stack

| Layer        | Technology |
|--------------|------------|
| Frontend     | React 16, React Router 5, Material-UI 4, Reactstrap, SCSS (node-sass), Highcharts, Axios |
| Backend      | PHP 7.4, PDO, JWT, TCPDF/FPDI, PhpSpreadsheet |
| Database     | MySQL / MariaDB |
| Server       | Apache (XAMPP) |

---

## Project Structure

```
cms/
├── backend/                # PHP REST API
│   └── source/
│       ├── api/            # API entry point and endpoint pages (api_pages/)
│       ├── classes/        # Database, JWT handler
│       ├── middlewares/    # Auth middleware
│       └── jwt/            # JWT library
│   ├── env.php             # DB credentials & app config (local)
│   ├── env_for_web.php     # DB credentials & app config (production)
│   └── report/             # PDF / Excel reporting libraries
├── Database/               # cms_db.zip — database dump
├── public/                 # CRA public assets (index.html, favicon, manifest)
├── src/                    # React source
│   ├── assets/             # CSS, SCSS, JS, fonts, images
│   ├── context/            # React context (user info)
│   └── views/              # Screens / pages (src/views/screens/*)
├── media/ , image/         # Static media
├── .env                    # Frontend environment variables
└── package.json
```

---

## Prerequisites

- [XAMPP](https://www.apachefriends.org/) with **PHP 7.4** and **MySQL**
- [Node.js](https://nodejs.org/) (with npm or yarn)
- A modern web browser

---

## Getting Started

### 1. Clone / place the project

Place the project inside your XAMPP `htdocs` directory so it is reachable at `http://localhost/cms`:

```
xampp/htdocs/cms
```

### 2. Set up the database

1. Start **Apache** and **MySQL** from the XAMPP control panel.
2. Open phpMyAdmin (`http://localhost/phpmyadmin`).
3. Create a database (default name: `cms_db1`).
4. Extract `Database/cms_db.zip` and import the SQL file into the database you created.

### 3. Configure the backend

Edit `backend/env.php` with your local database credentials:

```php
define("DB_NAME", "cms_db1");
define("DB_USER", "root");
define("DB_PASSWORD", "");
define("DB_SERVER", "localhost");
```

> Use `backend/env_for_web.php` for production/server configuration.

### 4. Configure the frontend

Environment variables live in `.env`:

```env
REACT_APP_BASE_NAME=/cms
REACT_APP_API_URL=http://localhost/cms/backend/
REACT_APP_FRONT_URL=http://localhost/cms/
REACT_APP_STORAGE_URL=http://localhost/storage/cms/
```

Adjust the URLs if your setup differs.

### 5. Install dependencies & run the frontend

```bash
npm install
npm start
```

The development server runs at `http://localhost:3000`.

---

## Available Scripts

| Command | Description |
|---------|-------------|
| `npm start` | Run the app in development mode |
| `npm run build` | Build the production bundle into `build/` |
| `npm test` | Run tests |
| `npm run compile-sass` | Compile SCSS to CSS |
| `npm run minify-sass` | Compile and minify SCSS |
| `npm run install:clean` | Clean reinstall of dependencies |

---

## Production Build

```bash
npm run build
```

The app is configured with `homepage: "/cms"`, so the build is served from the `/cms` path. Deploy the build output together with the `backend/` folder under Apache. The included `.htaccess` handles SPA routing (rewriting unknown routes to `index.html`) plus GZIP compression and caching.

---

## API Overview

The backend exposes a single entry point at `backend/source/api/index.php`. Requests are routed to the matching file in `backend/source/api/api_pages/` based on the URL path, and protected routes are validated by the JWT `Auth` middleware.

---

## Notes

- `node_modules` is git-ignored; run `npm install` after cloning.
- Do not commit real credentials in `env.php` / `.env`.
- The `loginonlyadmin` flag in `env.php` toggles maintenance mode (`0` = production, `1` = maintenance).
