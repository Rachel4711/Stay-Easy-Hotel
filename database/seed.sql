USE stay_easy_hotel;

INSERT INTO roles (name) VALUES
    ('customer'),
    ('admin')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO amenities (name) VALUES
    ('High-speed Wi-Fi'),
    ('Flat-screen TV'),
    ('Room management controls'),
    ('Electronic safe'),
    ('Tea and coffee facilities'),
    ('Hairdryer'),
    ('Rain shower'),
    ('Bathtub'),
    ('Mini bar'),
    ('Work desk')
ON DUPLICATE KEY UPDATE name = VALUES(name);

INSERT INTO room_types
    (name, slug, description, max_adults, max_children, bed_summary, size_sqm, base_price, image_path)
VALUES
    ('Easy Solo Retreat', 'easy-solo-retreat', 'A private and efficient room for solo travellers and business guests.', 1, 0, '1 single bed', 20.00, 150.00, 'assets/pic4.jpg'),
    ('Easy Comfort Room', 'easy-comfort-room', 'A comfortable room for couples and short getaways.', 2, 1, '1 queen-size bed', 28.00, 230.00, 'assets/pic5.jpg'),
    ('Easy Family Haven', 'easy-family-haven', 'A spacious room designed for families and small groups.', 3, 2, '1 queen-size bed and 1 single bed', 38.00, 350.00, 'assets/pic6.jpg'),
    ('Easy Premium Retreat', 'easy-premium-retreat', 'An enhanced room for long stays and premium experiences.', 2, 1, '1 king-size bed', 45.00, 480.00, 'assets/pic7.jpg')
ON DUPLICATE KEY UPDATE
    description = VALUES(description),
    max_adults = VALUES(max_adults),
    max_children = VALUES(max_children),
    bed_summary = VALUES(bed_summary),
    size_sqm = VALUES(size_sqm),
    base_price = VALUES(base_price),
    image_path = VALUES(image_path);

INSERT INTO rooms (room_type_id, room_number, floor)
SELECT rt.id, room_data.room_number, room_data.floor
FROM (
    SELECT 'easy-solo-retreat' AS slug, '101' AS room_number, 1 AS floor
    UNION ALL SELECT 'easy-solo-retreat', '102', 1
    UNION ALL SELECT 'easy-comfort-room', '201', 2
    UNION ALL SELECT 'easy-comfort-room', '202', 2
    UNION ALL SELECT 'easy-family-haven', '301', 3
    UNION ALL SELECT 'easy-family-haven', '302', 3
    UNION ALL SELECT 'easy-premium-retreat', '401', 4
    UNION ALL SELECT 'easy-premium-retreat', '402', 4
) AS room_data
INNER JOIN room_types rt ON rt.slug = room_data.slug
ON DUPLICATE KEY UPDATE
    room_type_id = VALUES(room_type_id),
    floor = VALUES(floor);

INSERT IGNORE INTO room_type_amenities (room_type_id, amenity_id)
SELECT rt.id, a.id
FROM room_types rt
CROSS JOIN amenities a
WHERE a.name IN ('High-speed Wi-Fi', 'Flat-screen TV', 'Electronic safe', 'Tea and coffee facilities', 'Hairdryer');

INSERT IGNORE INTO room_type_amenities (room_type_id, amenity_id)
SELECT rt.id, a.id
FROM room_types rt
CROSS JOIN amenities a
WHERE rt.slug = 'easy-premium-retreat'
  AND a.name IN ('Room management controls', 'Rain shower', 'Bathtub', 'Mini bar', 'Work desk');
