# Gatherly — Event Registration System

A production-style MVP built with plain PHP, MySQL, HTML, CSS and JavaScript.

## Features
- Public event discovery
- Event detail pages
- User/admin authentication
- Admin dashboard
- Event creation
- Registration limits
- Participant list
- Unique ticket codes
- Registration status
- Light/Dark theme persisted with localStorage
- Responsive UI
- Accessible form feedback
- Reduced-motion support

## Requirements
- PHP 8.0+
- MySQL 5.7+/MariaDB
- Apache (XAMPP/WAMP/LAMP works)

## Setup
1. Copy the `event_registration_system` folder into `htdocs` if using XAMPP.
2. Start Apache and MySQL.
3. Open phpMyAdmin.
4. Import `database.sql`.
5. Check `config.php` and update the MySQL credentials if needed.
6. Open:
   `http://localhost/event_registration_system/`

## Demo admin
Email: `admin@gatherly.test`
Password: `admin123`

### If you already imported the old database
Re-import `database.sql`, or run:
```sql
UPDATE users SET password='$2y$12$Ffkx8oD5eCzmw187jncMbujF2PZsQU8aFWzu5TdSpYRzTLkfrc8di', role='admin' WHERE email='admin@gatherly.test';
```
Then try the demo login again.

## Main routes
- `index.php` — public event discovery
- `event.php?id=1` — event details
- `login.php` — authentication
- `admin.php` — admin dashboard
- `create_event.php` — create events
- `participants.php?id=1` — participant list

## Notes
The MVP uses secure PDO prepared statements and password hashing. Ticket codes are generated server-side. For production deployment, add CSRF protection, HTTPS, rate limiting, email verification/password reset, stronger authorization policies, and QR generation using a maintained QR library.

## User registration
Visitors can create their own user account at `register.php`. After signup, they are automatically signed in and can register for an event. The event detail page also provides a direct “Create account” path that returns the user to the selected event.

## My registrations
Logged-in users can open `my_registrations.php` from the header to see their event registrations and ticket codes.
