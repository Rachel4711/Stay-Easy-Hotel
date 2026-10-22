# Stay Easy Hotel database

The database uses MySQL/MariaDB with InnoDB, UTF-8 (`utf8mb4`), foreign keys, validation constraints, and indexes for the booking workflows.

## Install with WAMP/phpMyAdmin

1. Start WAMP and ensure MySQL/MariaDB is running.
2. Open phpMyAdmin at `http://localhost/phpmyadmin`.
3. Choose **Import** and import `database/schema.sql`.
4. Import `database/seed.sql` after the schema completes.
5. Copy `config/database.example.php` to `config/database.php`.
6. Change the username or password in `config/database.php` if your WAMP database credentials differ.

`config/database.php` is ignored by Git so local credentials are not committed.

## Main relationships

- A role has many users.
- A room type has many physical rooms and many amenities.
- A user can have many bookings; guest bookings can have a null `user_id`.
- A booking can reserve one or more physical rooms through `booking_rooms`.
- A booking can have multiple payment attempts or refunds.
- Contact messages may be associated with a signed-in user and an administrator reply.

Guest name, email, and phone are copied onto each booking deliberately. This preserves the original booking contact details if a user later edits their profile.

## Availability rule

Date ranges use hotel-style half-open intervals: check-in is inclusive and check-out is exclusive. Two stays overlap when:

```sql
existing.check_in_date < :requested_check_out
AND existing.check_out_date > :requested_check_in
```

Only bookings with `pending`, `confirmed`, or `checked_in` status block a room. The `active_booking_rooms` view exposes those stays.

Before creating a booking, the application must start a transaction, lock candidate rooms with `SELECT ... FOR UPDATE`, repeat the overlap check, then insert the booking and its `booking_rooms` records. This prevents two customers from reserving the same room concurrently.

## Security notes

- Store passwords using PHP `password_hash()`; never store plain-text passwords.
- Use the PDO connection in `includes/database.php` and prepared statements for all user input.
- The seed file intentionally does not create an administrator password.
- Create the first administrator through a controlled PHP script during the authentication step.
