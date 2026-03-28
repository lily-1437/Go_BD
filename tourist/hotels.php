<?php
// tourist/hotels.php
// BROWSE HOTELS
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';
requireLogin();

$hotels = $conn->query(
    "SELECT s.service_id, s.service_name, s.price, s.service_description,
            sp.company_name, sp.location_city,
            h.category, h.traveler_choice
     FROM SERVICE s
     JOIN SERVICE_PROVIDER sp ON s.provider_id = sp.provider_id
     JOIN HOTEL h ON sp.provider_id = h.provider_id
     WHERE s.availability = 1
     ORDER BY s.price ASC"
);

$pageTitle = 'Hotels';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<h1>Hotels in Chittagong</h1>
<div class="card-grid">
    <?php while ($h = $hotels->fetch_assoc()): ?>
    <div class="card">
        <h3><?= htmlspecialchars($h['company_name']) ?></h3>
        <p>Category: <?= htmlspecialchars($h['category']) ?></p>
        <p>Best for: <?= htmlspecialchars($h['traveler_choice']) ?></p>
        <p>Price: ৳<?= number_format($h['price']) ?>/night</p>
        <p><?= htmlspecialchars($h['service_description']) ?></p>
        <a href="booking.php?service_id=<?= $h['service_id'] ?>">Book Now</a>
    </div>
    <?php endwhile; ?>
</div>

<?php include '../includes/footer.php'; ?>
