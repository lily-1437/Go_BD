<?php
// tourist/dashboard.php
// TOURIST DASHBOARD — personalized overview
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';
requireLogin();

$tid = getCurrentTouristId();
$bookings = getBookingsByTourist($conn, $tid);
$booking_count = $bookings->num_rows;

$tourist = $conn->query("SELECT p.name, t.traveller_type, t.nationality 
                          FROM PERSON p JOIN TOURIST t ON p.id = t.id 
                          WHERE p.id = $tid")->fetch_assoc();

$pageTitle = 'Dashboard';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<h1>Welcome, <?= htmlspecialchars($tourist['name']) ?>!</h1>
<p>Traveller Type: <?= htmlspecialchars($tourist['traveller_type']) ?> | 
   Nationality: <?= htmlspecialchars($tourist['nationality']) ?></p>

<div class="dashboard-stats">
    <div class="stat-card">
        <h3><?= $booking_count ?></h3>
        <p>Total Bookings</p>
    </div>
</div>

<h2>Quick Links</h2>
<nav class="quick-links">
    <a href="hotels.php">Browse Hotels</a>
    <a href="restaurants.php">Find Restaurants</a>
    <a href="transport.php">Book Transport</a>
    <a href="tour_guides.php">Hire a Guide</a>
    <a href="trip_planner.php">Plan a Trip</a>
    <a href="my_bookings.php">My Bookings</a>
</nav>

<?php include '../includes/footer.php'; ?>
