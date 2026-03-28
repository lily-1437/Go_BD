<?php
// tourist/transport.php
// BROWSE TRANSPORT SERVICES
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';
requireLogin();

$transports = $conn->query(
    "SELECT s.service_id, s.service_name, s.price, s.service_description,
            sp.company_name,
            t.vehicle_type, t.route_info
     FROM SERVICE s
     JOIN SERVICE_PROVIDER sp ON s.provider_id = sp.provider_id
     JOIN TRANSPORT t ON sp.provider_id = t.provider_id
     WHERE s.availability = 1
     ORDER BY s.price ASC"
);

$pageTitle = 'Transport';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<h1>Transport Services</h1>
<div class="card-grid">
    <?php while ($t = $transports->fetch_assoc()): ?>
    <div class="card">
        <h3><?= htmlspecialchars($t['company_name']) ?></h3>
        <p>Vehicle: <?= htmlspecialchars($t['vehicle_type']) ?></p>
        <p>Route: <?= htmlspecialchars($t['route_info']) ?></p>
        <p>Price: ৳<?= number_format($t['price']) ?></p>
        <a href="booking.php?service_id=<?= $t['service_id'] ?>">Book Now</a>
    </div>
    <?php endwhile; ?>
</div>

<?php include '../includes/footer.php'; ?>
