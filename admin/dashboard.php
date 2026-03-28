<?php
// admin/dashboard.php
// ADMIN DASHBOARD — system overview stats
require_once '../includes/auth.php';
require_once '../config/db.php';
requireAdmin();

$stats = [
    'tourists'  => $conn->query("SELECT COUNT(*) AS c FROM TOURIST")->fetch_assoc()['c'],
    'places'    => $conn->query("SELECT COUNT(*) AS c FROM TOURIST_PLACE")->fetch_assoc()['c'],
    'bookings'  => $conn->query("SELECT COUNT(*) AS c FROM BOOKING")->fetch_assoc()['c'],
    'services'  => $conn->query("SELECT COUNT(*) AS c FROM SERVICE WHERE availability=1")->fetch_assoc()['c'],
    'events'    => $conn->query("SELECT COUNT(*) AS c FROM EVENT WHERE event_date >= CURDATE()")->fetch_assoc()['c'],
    'reviews'   => $conn->query("SELECT COUNT(*) AS c FROM REVIEW")->fetch_assoc()['c'],
];

$pageTitle = 'Admin Dashboard';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<h1>Admin Dashboard</h1>
<div class="dashboard-stats">
    <?php foreach ($stats as $label => $value): ?>
    <div class="stat-card">
        <h3><?= $value ?></h3>
        <p><?= ucfirst($label) ?></p>
    </div>
    <?php endforeach; ?>
</div>

<h2>Manage</h2>
<nav class="admin-links">
    <a href="manage_places.php">Tourist Places</a>
    <a href="manage_services.php">Services</a>
    <a href="manage_events.php">Events</a>
    <a href="manage_bookings.php">Bookings</a>
    <a href="manage_users.php">Users</a>
    <a href="manage_reviews.php">Reviews</a>
</nav>

<?php include '../includes/footer.php'; ?>
