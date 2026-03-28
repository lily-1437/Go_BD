<?php
// tourist/booking.php
// BOOKING FORM — used for all service types
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';
requireLogin();

$service_id = (int)($_GET['service_id'] ?? 0);
$tid = getCurrentTouristId();
$error = '';
$success = '';

// Fetch service info
$service = $conn->query(
    "SELECT s.*, sp.company_name FROM SERVICE s 
     JOIN SERVICE_PROVIDER sp ON s.provider_id = sp.provider_id
     WHERE s.service_id = $service_id"
)->fetch_assoc();

if (!$service) { header("Location: ../public/places.php"); exit(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_date   = clean($conn, $_POST['booking_date']);
    $payment_info   = clean($conn, $_POST['payment_info']);

    $conn->query(
        "INSERT INTO BOOKING (tourist_id, service_id, booking_date, status, payment_info)
         VALUES ($tid, $service_id, '$booking_date', 'pending', '$payment_info')"
    );
    $success = "Booking confirmed! <a href='my_bookings.php'>View my bookings</a>";
}

$pageTitle = 'Book ' . $service['service_name'];
include '../includes/header.php';
include '../includes/navbar.php';
?>

<h1>Book: <?= htmlspecialchars($service['service_name']) ?></h1>
<p>Provider: <?= htmlspecialchars($service['company_name']) ?></p>
<p>Price: ৳<?= number_format($service['price']) ?></p>
<p><?= htmlspecialchars($service['service_description']) ?></p>

<?php if ($success): ?><p class="success"><?= $success ?></p>
<?php else: ?>
<form method="POST">
    <label>Booking Date <input type="date" name="booking_date" min="<?= date('Y-m-d') ?>" required></label>
    <label>Payment Method
        <select name="payment_info">
            <option value="cash">Cash</option>
            <option value="bkash">bKash</option>
            <option value="nagad">Nagad</option>
            <option value="card">Card</option>
        </select>
    </label>
    <button type="submit">Confirm Booking</button>
</form>
<?php endif; ?>

<?php include '../includes/footer.php'; ?>
