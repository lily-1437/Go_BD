<?php
// tourist/restaurants.php
// BROWSE RESTAURANTS
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';
requireLogin();

$filter_diet = $_GET['diet'] ?? null;
$sql = "SELECT s.service_id, s.service_name, s.price, s.service_description,
               sp.company_name, sp.location_city,
               r.meal_type, r.feature, r.dietary_restriction
        FROM SERVICE s
        JOIN SERVICE_PROVIDER sp ON s.provider_id = sp.provider_id
        JOIN RESTAURANT r ON sp.provider_id = r.provider_id
        WHERE s.availability = 1";
if ($filter_diet) {
    $d = $conn->real_escape_string($filter_diet);
    $sql .= " AND r.dietary_restriction = '$d'";
}
$sql .= " ORDER BY sp.company_name ASC";
$restaurants = $conn->query($sql);

$pageTitle = 'Restaurants';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<h1>Restaurants in Chittagong</h1>
<form method="GET">
    <select name="diet" onchange="this.form.submit()">
        <option value="">All Dietary Options</option>
        <option value="halal" <?= $filter_diet=='halal'?'selected':'' ?>>Halal</option>
        <option value="vegetarian" <?= $filter_diet=='vegetarian'?'selected':'' ?>>Vegetarian</option>
        <option value="vegan" <?= $filter_diet=='vegan'?'selected':'' ?>>Vegan</option>
    </select>
</form>

<div class="card-grid">
    <?php while ($r = $restaurants->fetch_assoc()): ?>
    <div class="card">
        <h3><?= htmlspecialchars($r['company_name']) ?></h3>
        <p>Meal Type: <?= htmlspecialchars($r['meal_type']) ?></p>
        <p>Dietary: <?= htmlspecialchars($r['dietary_restriction']) ?></p>
        <p>Price: ৳<?= number_format($r['price']) ?></p>
        <a href="booking.php?service_id=<?= $r['service_id'] ?>">Reserve a Table</a>
    </div>
    <?php endwhile; ?>
</div>

<?php include '../includes/footer.php'; ?>
