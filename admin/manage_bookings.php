<?php
// admin/manage_bookings.php
// VIEW ALL BOOKINGS — update status
require_once '../includes/auth.php';
require_once '../config/db.php';
requireAdmin();

if (isset($_GET['status']) && isset($_GET['id'])) {
    $id     = (int)$_GET['id'];
    $status = in_array($_GET['status'], ['pending','confirmed','cancelled']) ? $_GET['status'] : 'pending';
    $conn->query("UPDATE BOOKING SET status = '$status' WHERE booking_id = $id");
}

$bookings = $conn->query(
    "SELECT b.booking_id, b.booking_date, b.status, b.payment_info,
            p.name AS tourist_name, s.service_name, s.price
     FROM BOOKING b
     JOIN PERSON p ON b.tourist_id = p.id
     JOIN SERVICE s ON b.service_id = s.service_id
     ORDER BY b.booking_date DESC"
);

$pageTitle = 'Manage Bookings';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<h1>All Bookings</h1>
<table>
    <thead><tr><th>ID</th><th>Tourist</th><th>Service</th><th>Date</th><th>Price</th><th>Status</th><th>Payment</th><th>Actions</th></tr></thead>
    <tbody>
    <?php while ($b = $bookings->fetch_assoc()): ?>
    <tr>
        <td><?= $b['booking_id'] ?></td>
        <td><?= htmlspecialchars($b['tourist_name']) ?></td>
        <td><?= htmlspecialchars($b['service_name']) ?></td>
        <td><?= $b['booking_date'] ?></td>
        <td>৳<?= number_format($b['price']) ?></td>
        <td><?= $b['status'] ?></td>
        <td><?= htmlspecialchars($b['payment_info']) ?></td>
        <td>
            <a href="?id=<?= $b['booking_id'] ?>&status=confirmed">✅</a>
            <a href="?id=<?= $b['booking_id'] ?>&status=cancelled">❌</a>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
