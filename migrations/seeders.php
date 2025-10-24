<?php
require_once __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;

// Get PDO connection
$db = new Database();
$conn = $db->connect();

try {
    echo "<h3> Seeding database...</h3>";

    // ------------------------- USERS -------------------------
    $password = password_hash("admin123", PASSWORD_DEFAULT);

    $conn->exec("
        INSERT INTO users (name, email, password, role) VALUES
        ('Admin User', 'onyangojimmy2005@gmail.com', '$password', 'admin'),
        ('John Fundi', 'john@example.com', '$password', 'fundi'),
        ('Jane Resident', 'resident@example.com', '$password', 'resident')
    ");
    echo "Seeded 'users' table.<br>";

    // ------------------------- FUNDI PROFILES -------------------------
    $conn->exec("
        INSERT INTO fundi_profiles (user_id, skills, location, phone_number) VALUES
        (2, 'Plumbing, Electrical', 'Nairobi', '0712345678')
    ");
    echo "Seeded 'fundi_profiles' table.<br>";

    /// ------------------------- SERVICES -------------------------
$conn->exec("
    INSERT INTO services (name, description, price, status) VALUES
    ('Plumbing', 'Fixing pipes, leaks, taps, and water systems', 1500.00, 'active'),
    ('Electrical', 'Wiring, lighting, and electrical repairs', 2000.00, 'active'),
    ('Carpentry', 'Furniture repairs and woodwork', 1800.00, 'inactive')
");
echo "Seeded 'services' table.<br>";


    // ------------------------- FUNDI SERVICES -------------------------
    $conn->exec("
        INSERT INTO fundi_services (fundi_profile_id, service_id) VALUES
        (1, 1),
        (1, 2)
    ");
    echo "Seeded 'fundi_services' table.<br>";

    // ------------------------- BOOKINGS -------------------------
    $conn->exec("
        INSERT INTO bookings (resident_id, fundi_id, status) VALUES
        (3, 2, 'pending')
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

    echo "<br><strong> Seeding completed successfully.</strong>";

} catch (PDOException $e) {
    echo "Error seeding database: " . $e->getMessage();
}
