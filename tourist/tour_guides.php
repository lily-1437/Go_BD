<?php
// tourist/tour_guides.php
// BROWSE TOUR GUIDES
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';
requireLogin();

$guides = $conn->query(
    "SELECT s.service_id, s.service_name, s.price, s.service_description,
            sp.company_name,
            tg.availability, tg.phone_number
     FROM SERVICE s
     JOIN SERVICE_PROVIDER sp ON s.provider_id = sp.provider_id
     JOIN TOUR_GUIDE tg ON sp.provider_id = tg.provider_id
     WHERE s.availability = 1
     ORDER BY sp.company_name ASC"
);

$pageTitle = 'Tour Guides';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<h1>Verified Tour Guides</h1>
<div class="card-grid">
    <?php while ($g = $guides->fetch_assoc()): ?>
    <div class="card">
        <h3><?= htmlspecialchars($g['company_name']) ?></h3>
        <p>Available: <?= $g['availability'] ? 'Yes' : 'No' ?></p>
        <p>Phone: <?= htmlspecialchars($g['phone_number']) ?></p>
        <p>Rate: ৳<?= number_format($g['price']) ?>/day</p>
        <p><?= htmlspecialchars($g['service_description']) ?></p>
        <?php if ($g['availability']): ?>
        <a href="booking.php?service_id=<?= $g['service_id'] ?>">Book Guide</a>
        <?php else: ?>
        <span>Currently Unavailable</span>
        <?php endif; ?>
    </div>
    <?php endwhile; ?>
</div>

<?php include '../includes/footer.php'; ?>
