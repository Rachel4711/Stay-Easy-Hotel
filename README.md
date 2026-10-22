# Stay Easy Hotel Booking System

Stay Easy is a responsive hotel website and booking system built with PHP and MySQL. Customers can create an account, search room availability, make a reservation, and review their bookings. Administrators have a protected dashboard for monitoring bookings, customers, and room operations.

## Features

### Public website

- Responsive home, rooms, dining, neighbourhood, gallery, about, and contact pages
- Shared navigation and footer components
- Responsive image carousel gallery
- Room search by check-in date, check-out date, and guest count

### Customer features

- Account registration and login
- Secure password hashing and session-based authentication
- Live room availability search
- Booking creation and confirmation
- Personal dashboard showing upcoming, completed, and cancelled stays
- Booking details with reference number, dates, room, guests, price, and status

### Administrator features

- Role-protected administrator area
- Overview statistics and recent bookings
- Search and filter bookings
- View booking and guest details
- Update booking status
- View the room inventory and change room operational status
- View registered customers and their booking totals

## Technology stack

- **Frontend:** HTML5, CSS3, responsive layouts, and vanilla JavaScript
- **Backend:** PHP with server-rendered pages
- **Database:** MySQL 8.0+ or MariaDB 10.5+
- **Database access:** PDO with prepared statements and native parameter binding
- **Authentication:** PHP sessions, `password_hash()`, and `password_verify()`
- **Security:** CSRF tokens, output escaping, prepared SQL statements, role checks, and transactional room allocation
- **Recommended local environment:** WampServer on Windows

No package manager or third-party PHP framework is required.

## Requirements

- Apache web server
- PHP 8.0 or newer with the PDO MySQL extension enabled
- MySQL 8.0+ or MariaDB 10.5+
- WampServer, XAMPP, or an equivalent PHP/MySQL environment

## Installation with WampServer

1. Place the project in the WampServer web directory:

   ```text
   C:\wamp64\www\Hotel_Booking_System
   ```

2. Start Apache and MySQL from WampServer. Both services should show as running.

3. Open phpMyAdmin, normally available at:

   ```text
   http://localhost/phpmyadmin/
   ```

4. Import the database files in this order:

   1. `database/schema.sql`
   2. `database/seed.sql`

   `schema.sql` creates the `stay_easy_hotel` database and its tables. `seed.sql` adds the customer and administrator roles, room types, amenities, and sample rooms.

5. Create `config/database.php` if it does not already exist:

   ```php
   <?php

   return [
       'host' => '127.0.0.1',
       'port' => 3306,
       'database' => 'stay_easy_hotel',
       'username' => 'root',
       'password' => '',
       'charset' => 'utf8mb4',
   ];
   ```

   Change the username, password, port, or database name when your MySQL installation uses different values. This file is ignored by Git so that local database credentials are not committed.

6. Open the website:

   ```text
   http://localhost/Hotel_Booking_System/HomePage.php
   ```

## Creating an administrator account

Customer accounts are created through the registration page. Administrator accounts are deliberately created from the command line so visitors cannot register themselves as administrators.

From the project directory, run:

```powershell
php scripts\create_admin.php
```

Enter the requested first name, last name, email address, and password. Then sign in at:

```text
http://localhost/Hotel_Booking_System/login.php
```

The application automatically sends an administrator to `admin/dashboard.php` after a successful login.

## How to use the system

### Customer workflow

1. Select **Sign Up** and create a customer account.
2. Search for a room using valid future check-in and check-out dates.
3. Choose an available room type and review the reservation.
4. Confirm the booking to receive a booking reference.
5. Open **My Account** to view upcoming stays and booking history.

### Administrator workflow

1. Log in with an account created by `scripts/create_admin.php`.
2. Review hotel totals and recent reservations on the overview page.
3. Use **Bookings** to search, filter, inspect, and update reservations.
4. Use **Rooms** to mark rooms as available, under maintenance, or inactive.
5. Use **Customers** to review registered customer accounts and booking totals.

## Project structure

```text
Hotel_Booking_System/
|-- admin/                    Administrator dashboard and protected pages
|-- assets/                   Website and room images
|-- config/                   Local database configuration
|-- database/
|   |-- schema.sql            Database structure
|   `-- seed.sql              Initial roles, rooms, and amenities
|-- includes/
|   |-- database.php          Shared PDO database connection
|   |-- footer.php            Shared public footer
|   |-- header.php            Shared public navigation
|   `-- session.php           Secure session setup
|-- scripts/
|   `-- create_admin.php      Command-line administrator creator
|-- account.php               Customer dashboard
|-- booking.php               Booking review and creation
|-- booking_confirmation.php  Reservation confirmation/details
|-- login.php                 Authentication page
|-- register.php              Customer registration
|-- search.php                Availability search
|-- StayEasyStyle.css         Shared responsive styling
`-- HomePage.php              Main entry page
```

## Database overview

The database contains tables for:

- Roles and users
- Room types, individual rooms, and amenities
- Bookings and assigned rooms
- Payments
- Contact messages

The availability logic treats `pending`, `confirmed`, and `checked_in` reservations as active. The final availability check and booking inserts run inside a database transaction, with the selected room locked to reduce double-booking risk.

## Security notes

- Passwords are stored as hashes rather than plain text.
- SQL queries use PDO prepared statements.
- Booking and administrator update forms use CSRF tokens.
- Administrator pages verify the current user's role against the database.
- Dynamic page output is escaped before being added to HTML.
- `config/database.php` is excluded from Git and should remain private.

For a public deployment, enable HTTPS, use a dedicated database account with limited permissions, disable detailed PHP errors, and keep PHP/MySQL updated.

## Current limitations

- The payment table is prepared in the schema, but no payment gateway is connected yet.
- The contact form validates submitted data but does not yet store or email messages.
- Administrators can update booking states, but automated email notifications are not implemented.
- Password reset and email verification are not implemented.
- Room images and content are managed directly in the project/database rather than through an admin content editor.

## Basic verification

To check the syntax of one PHP file:

```powershell
php -l HomePage.php
```

To check every PHP file from PowerShell:

```powershell
Get-ChildItem -Recurse -Filter *.php | ForEach-Object { php -l $_.FullName }
```

After installation, manually verify registration, login, room search, booking confirmation, customer booking history, administrator authorization, and booking/room status updates.

## License

This project was created for educational use. Add a licence file before redistributing it under specific licensing terms.
