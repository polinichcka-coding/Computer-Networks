# Beauty Space Booking System

Simple PHP + MySQL project for XAMPP.

## Installation

1. Start Apache and MySQL in XAMPP.
2. Copy the `beauty-service` folder to `C:\xampp\htdocs\`.
3. Open phpMyAdmin and import `database.sql`.
4. Open `http://localhost/beauty-service/setup.php` once.
5. Delete `setup.php` after setup.
6. Open `http://localhost/beauty-service/`.

If MySQL root has a password, change `config/database.php`.

**Warning:** `database.sql` drops existing tables. Do not import it over an existing database with data you need to keep.

## Demo accounts

| Role | Email | Password |
| --- | --- | --- |
| Admin | admin@beauty.test | admin123 |
| Master | sofia@beauty.test | master123 |
| Client | anna@beauty.test | client123 |

## Features

- Client: register, log in, view services, book and cancel appointments.
- Master: accept, reject and complete appointments.
- Admin: manage users and services, assign services to masters, view bookings.

## Files to know for defence

- `config/database.php`: database connection.
- `includes/auth.php`: sessions, roles, form token.
- `login.php` and `register.php`: accounts.
- `client/book.php`: create booking (`INSERT`).
- `client/bookings.php`: show bookings (`SELECT`).
- `client/cancel_booking.php`: cancel (`UPDATE`).
- `master/update_booking.php`: change booking status (`UPDATE`).
- `admin/services.php`: add, edit and delete services.
- `assets/css/style.css`: simple styling.

Note: booking conflicts are checked for the same master and start time. This student project does not implement full overlap detection for services with different durations.
