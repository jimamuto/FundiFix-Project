<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;

// Get PDO connection
$db = new Database();
$conn = $db->connect();

try {
    echo "<h3>Seeding database...</h3>";

    // ------------------------- USERS -------------------------
    $password = password_hash("admin123", PASSWORD_DEFAULT);

    $conn->exec("
        INSERT INTO users (name, email, password, role, is_verified) VALUES
        ('Admin User', 'onyangojimmy2005@gmail.com', '$password', 'admin', 1),
        ('John Fundi', 'onyij61@gmail.com', '$password', 'fundi', 1),
        ('Mike Electrician', 'mike@example.com', '$password', 'fundi', 1),
        ('Sarah Plumber', 'sarah@example.com', '$password', 'fundi', 1),
        ('David Carpenter', 'david@example.com', '$password', 'fundi', 1),
        ('Jane Resident', 'jim.amuto@strathmore.edu', '$password', 'resident', 1),
        ('Larissa Joseph', 'larissajoseph@gmail.com', '$password', 'fundi', 1)
    ");
    echo "Seeded 'users' table.<br>";

    // ------------------------- FUNDI PROFILES -------------------------
    $conn->exec("
        INSERT INTO fundi_profiles (user_id, skills, location, phone_number) VALUES
        (2, 'Plumbing, Electrical', 'Nairobi', '0712345678'),
        (3, 'Electrical, Wiring', 'Westlands', '0723456789'),
        (4, 'Plumbing, Pipe Repair', 'Karen', '0734567890'),
        (5, 'Carpentry, Furniture', 'Runda', '0745678901'),
        (7, 'Interior Design, Artwork', 'Karen', '0710101406')
    ");
    echo "Seeded 'fundi_profiles' table.<br>";

    // ------------------------- SERVICES -------------------------
    $conn->exec("
        INSERT INTO services (name, category, description, price, status) VALUES
        ('Plumbing', 'Home Repair', 'Fixing pipes, leaks, taps, and water systems', 1500.00, 'active'),
        ('Electrical', 'Home Repair', 'Wiring, lighting, and electrical repairs', 2000.00, 'active'),
        ('Carpentry', 'Woodwork', 'Furniture repairs and woodwork', 1800.00, 'inactive'),
        ('Interior Design', 'Creative Works', 'Interior design, artwork, and space arrangement', 2500.00, 'active')
    ");
    echo "Seeded 'services' table.<br>";

    // ------------------------- FUNDI SERVICES -------------------------
    $conn->exec("
        INSERT INTO fundi_services (fundi_profile_id, service_id) VALUES
        (1, 1), (1, 2),     -- John: Plumbing, Electrical
        (2, 2),              -- Mike: Electrical
        (3, 1),              -- Sarah: Plumbing  
        (4, 3),              -- David: Carpentry
        (5, 4)               -- Larissa: Interior Design
    ");
    echo "Seeded 'fundi_services' table.<br>";

    // ------------------------- BOOKINGS -------------------------
    $conn->exec("
        INSERT INTO bookings (resident_id, fundi_id, service_id, amount, status) VALUES
        (3, 2, 1, 1500.00, 'pending')
    ");
    echo "Seeded 'bookings' table.<br>";

    // ------------------------- REVIEWS -------------------------
    $conn->exec("
        INSERT INTO reviews (booking_id, resident_id, fundi_id, rating, comment) VALUES
        (1, 3, 2, 5, 'Great work! Very professional.')
    ");
    echo "Seeded 'reviews' table.<br>";

    // ------------------------- PAYMENTS -------------------------
    $conn->exec("
        INSERT INTO payments (booking_id, amount, status, transaction_id, payment_method) VALUES
        (1, 1500.00, 'completed', 'TXN123456789', 'M-Pesa')
    ");
    echo "Seeded 'payments' table.<br>";

        // ------------------------- INVENTORY -------------------------
    $conn->exec("
        INSERT INTO inventory (fundi_id, item_name, category, quantity, unit_price, description, status) VALUES
        (2, 'Pipe Wrench', 'Tools', 15, 850.00, 'Heavy-duty pipe wrench for plumbing work', 'available'),
        (2, 'PVC Pipes', 'Materials', 8, 350.00, '2-inch PVC pipes for water systems', 'low_stock'),
        (2, 'Pipe Sealant', 'Materials', 25, 120.00, 'Waterproof pipe sealant tape', 'available'),
        (2, 'Plunger', 'Tools', 5, 250.00, 'Standard plumbing plunger', 'available'),
        (3, 'Wire Strippers', 'Tools', 12, 450.00, 'Professional wire stripping tool', 'available'),
        (3, 'Electrical Tape', 'Materials', 3, 80.00, 'Black electrical insulation tape', 'low_stock'),
        (3, 'Circuit Tester', 'Tools', 6, 1200.00, 'Digital circuit and voltage tester', 'available'),
        (3, 'Light Bulbs', 'Materials', 0, 150.00, 'LED light bulbs various wattages', 'out_of_stock'),
        (4, 'Pipe Cutters', 'Tools', 10, 600.00, 'Steel pipe cutting tool', 'available'),
        (4, 'Teflon Tape', 'Materials', 18, 60.00, 'Thread seal tape for pipes', 'available'),
        (5, 'Hammer', 'Tools', 7, 300.00, 'Claw hammer for carpentry', 'available'),
        (5, 'Wood Screws', 'Materials', 2, 40.00, 'Assorted wood screws pack', 'low_stock'),
        (5, 'Saw', 'Tools', 4, 800.00, 'Hand saw for wood cutting', 'available'),
        (7, 'Paint Brushes', 'Tools', 20, 120.00, 'Various sized paint brushes', 'available'),
        (7, 'Wall Paint', 'Materials', 0, 1800.00, 'Interior wall paint white', 'out_of_stock'),
        (7, 'Color Samples', 'Materials', 15, 50.00, 'Paint color sample cards', 'available')
    ");
    echo "Seeded 'inventory' table.<br>";

    echo "<br><strong>Seeding completed successfully.</strong>";

} catch (PDOException $e) {
    echo "Error seeding database: " . $e->getMessage();
}
