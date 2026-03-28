<?php
// tourist/my_bookings.php
// MY BOOKINGS — history for logged-in tourist
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';
requireLogin();

$tid = getCurrentTouristId();
$bookings = getBookingsByTourist($conn, $tid);

$pageTitle = 'My Bookings';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<h1>My Bookings</h1>
<?php if ($bookings->num_rows === 0): ?>
    <p>You have no bookings yet. <a href="../public/places.php">Explore places</a></p>
<?php else: ?>
<table>
    <thead>
        <tr>
            <th>Service</th><th>Date</th><th>Price</th><th>Status</th><th>Payment</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($b = $bookings->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($b['service_name']) ?></td>
            <td><?= $b['booking_date'] ?></td>
            <td>৳<?= number_format($b['price']) ?></td>
            <td><?= htmlspecialchars($b['status']) ?></td>
            <td><?= htmlspecialchars($b['payment_info']) ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
