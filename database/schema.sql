-- Stay Easy Hotel booking database
-- Target: MySQL 8.0+ / MariaDB 10.5+

CREATE DATABASE IF NOT EXISTS stay_easy_hotel
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE stay_easy_hotel;

CREATE TABLE roles (
    id TINYINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(30) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_roles_name (name)
) ENGINE=InnoDB;

CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    role_id TINYINT UNSIGNED NOT NULL,
    first_name VARCHAR(80) NOT NULL,
    last_name VARCHAR(80) NOT NULL,
    email VARCHAR(254) NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(30) NULL,
    status ENUM('active', 'disabled') NOT NULL DEFAULT 'active',
    email_verified_at DATETIME NULL,
    last_login_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_email (email),
    KEY idx_users_role (role_id),
    CONSTRAINT fk_users_role
        FOREIGN KEY (role_id) REFERENCES roles(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE room_types (
    id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(120) NOT NULL,
    description TEXT NOT NULL,
    max_adults TINYINT UNSIGNED NOT NULL DEFAULT 1,
    max_children TINYINT UNSIGNED NOT NULL DEFAULT 0,
    bed_summary VARCHAR(150) NOT NULL,
    size_sqm DECIMAL(6,2) NULL,
    base_price DECIMAL(10,2) NOT NULL,
    image_path VARCHAR(255) NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_room_types_name (name),
    UNIQUE KEY uq_room_types_slug (slug),
    CONSTRAINT chk_room_types_capacity CHECK (max_adults >= 1),
    CONSTRAINT chk_room_types_price CHECK (base_price >= 0)
) ENGINE=InnoDB;

CREATE TABLE rooms (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    room_type_id SMALLINT UNSIGNED NOT NULL,
    room_number VARCHAR(20) NOT NULL,
    floor SMALLINT NULL,
    status ENUM('available', 'maintenance', 'inactive') NOT NULL DEFAULT 'available',
    notes VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_rooms_number (room_number),
    KEY idx_rooms_type_status (room_type_id, status),
    CONSTRAINT fk_rooms_room_type
        FOREIGN KEY (room_type_id) REFERENCES room_types(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE amenities (
    id SMALLINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    icon VARCHAR(100) NULL,
    UNIQUE KEY uq_amenities_name (name)
) ENGINE=InnoDB;

CREATE TABLE room_type_amenities (
    room_type_id SMALLINT UNSIGNED NOT NULL,
    amenity_id SMALLINT UNSIGNED NOT NULL,
    PRIMARY KEY (room_type_id, amenity_id),
    CONSTRAINT fk_room_type_amenities_type
        FOREIGN KEY (room_type_id) REFERENCES room_types(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_room_type_amenities_amenity
        FOREIGN KEY (amenity_id) REFERENCES amenities(id)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE bookings (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_reference VARCHAR(20) NOT NULL,
    user_id BIGINT UNSIGNED NULL,
    guest_first_name VARCHAR(80) NOT NULL,
    guest_last_name VARCHAR(80) NOT NULL,
    guest_email VARCHAR(254) NOT NULL,
    guest_phone VARCHAR(30) NULL,
    check_in_date DATE NOT NULL,
    check_out_date DATE NOT NULL,
    adults SMALLINT UNSIGNED NOT NULL DEFAULT 1,
    children SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    status ENUM('pending', 'confirmed', 'checked_in', 'checked_out', 'cancelled', 'no_show') NOT NULL DEFAULT 'pending',
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    tax_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    currency CHAR(3) NOT NULL DEFAULT 'MYR',
    special_requests TEXT NULL,
    cancelled_at DATETIME NULL,
    cancellation_reason VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_bookings_reference (booking_reference),
    KEY idx_bookings_user (user_id),
    KEY idx_bookings_dates_status (check_in_date, check_out_date, status),
    KEY idx_bookings_guest_email (guest_email),
    CONSTRAINT fk_bookings_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT chk_bookings_dates CHECK (check_out_date > check_in_date),
    CONSTRAINT chk_bookings_guests CHECK (adults >= 1),
    CONSTRAINT chk_bookings_amounts CHECK (subtotal >= 0 AND tax_amount >= 0 AND total_amount >= 0)
) ENGINE=InnoDB;

CREATE TABLE booking_rooms (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    room_id INT UNSIGNED NOT NULL,
    nightly_rate DECIMAL(10,2) NOT NULL,
    adults TINYINT UNSIGNED NOT NULL DEFAULT 1,
    children TINYINT UNSIGNED NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_booking_room (booking_id, room_id),
    KEY idx_booking_rooms_room_booking (room_id, booking_id),
    CONSTRAINT fk_booking_rooms_booking
        FOREIGN KEY (booking_id) REFERENCES bookings(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_booking_rooms_room
        FOREIGN KEY (room_id) REFERENCES rooms(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_booking_rooms_rate CHECK (nightly_rate >= 0),
    CONSTRAINT chk_booking_rooms_adults CHECK (adults >= 1)
) ENGINE=InnoDB;

CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    booking_id BIGINT UNSIGNED NOT NULL,
    provider VARCHAR(50) NULL,
    provider_reference VARCHAR(150) NULL,
    amount DECIMAL(10,2) NOT NULL,
    currency CHAR(3) NOT NULL DEFAULT 'MYR',
    method ENUM('cash', 'card', 'bank_transfer', 'online') NOT NULL,
    status ENUM('pending', 'paid', 'failed', 'refunded', 'partially_refunded') NOT NULL DEFAULT 'pending',
    paid_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_payments_booking_status (booking_id, status),
    UNIQUE KEY uq_payments_provider_reference (provider, provider_reference),
    CONSTRAINT fk_payments_booking
        FOREIGN KEY (booking_id) REFERENCES bookings(id)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT chk_payments_amount CHECK (amount > 0)
) ENGINE=InnoDB;

CREATE TABLE contact_messages (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    name VARCHAR(160) NOT NULL,
    email VARCHAR(254) NOT NULL,
    subject VARCHAR(180) NULL,
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied', 'archived') NOT NULL DEFAULT 'new',
    replied_by BIGINT UNSIGNED NULL,
    replied_at DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_contact_messages_status_created (status, created_at),
    KEY idx_contact_messages_user (user_id),
    CONSTRAINT fk_contact_messages_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL,
    CONSTRAINT fk_contact_messages_replied_by
        FOREIGN KEY (replied_by) REFERENCES users(id)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

-- A room is available when this query returns it. Run the final availability
-- check and booking inserts inside one database transaction to avoid races.
CREATE OR REPLACE VIEW active_booking_rooms AS
SELECT
    br.room_id,
    b.id AS booking_id,
    b.check_in_date,
    b.check_out_date,
    b.status
FROM booking_rooms br
INNER JOIN bookings b ON b.id = br.booking_id
WHERE b.status IN ('pending', 'confirmed', 'checked_in');
