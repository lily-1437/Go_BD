<?php
// tourist/trip_planner.php
// TRIP PLANNER — links a tourist to a place + service for a planned trip
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';
requireLogin();

$tid = getCurrentTouristId();
$places   = getTouristPlaces($conn);
$services = $conn->query("SELECT service_id, service_name FROM SERVICE WHERE availability = 1");
$success  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $place_id    = (int)$_POST['place_id'];
    $service_id  = (int)$_POST['service_id'];
    $type        = clean($conn, $_POST['type']);
    $description = clean($conn, $_POST['description']);
    $duration    = clean($conn, $_POST['trip_duration']);

    $conn->query(
        "INSERT INTO TRIP_PLANNER (tourist_id, place_id, service_id, type, trip_description, trip_duration)
         VALUES ($tid, $place_id, $service_id, '$type', '$description', '$duration')"
    );
    $success = "Trip plan saved!";
}

$pageTitle = 'Trip Planner';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<h1>Plan Your Trip</h1>
<?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>

<form method="POST">
    <label>Destination
        <select name="place_id" required>
            <?php while ($p = $places->fetch_assoc()): ?>
            <option value="<?= $p['place_id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </label>
    <label>Trip Type
        <select name="type">
            <option value="family">Family</option>
            <option value="solo">Solo</option>
            <option value="female_solo">Female Solo</option>
        </select>
    </label>
    <label>Duration (days) <input type="number" name="trip_duration" min="1" max="30" required></label>
    <label>Service to Book
        <select name="service_id">
            <?php while ($s = $services->fetch_assoc()): ?>
            <option value="<?= $s['service_id'] ?>"><?= htmlspecialchars($s['service_name']) ?></option>
            <?php endwhile; ?>
        </select>
    </label>
    <label>Notes / Description <textarea name="description" rows="3"></textarea></label>
    <button type="submit">Save Trip Plan</button>
</form>

<?php include '../includes/footer.php'; ?>
