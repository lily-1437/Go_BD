<?php
// admin/manage_services.php
// VIEW & TOGGLE availability of all services (Hotels, Restaurants, Transport, Tour Guides)
require_once '../includes/auth.php';
require_once '../config/db.php';
requireAdmin();

if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $conn->query("UPDATE SERVICE SET availability = NOT availability WHERE service_id = $id");
}

$services = $conn->query(
    "SELECT s.service_id, s.service_name, s.price, s.availability, sp.company_name, sp.type
     FROM SERVICE s JOIN SERVICE_PROVIDER sp ON s.provider_id = sp.provider_id
     ORDER BY sp.type, sp.company_name"
);

$pageTitle = 'Manage Services';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<h1>Manage Services</h1>
<table>
    <thead><tr><th>ID</th><th>Provider</th><th>Type</th><th>Service</th><th>Price</th><th>Available</th><th>Action</th></tr></thead>
    <tbody>
    <?php while ($s = $services->fetch_assoc()): ?>
    <tr>
        <td><?= $s['service_id'] ?></td>
        <td><?= htmlspecialchars($s['company_name']) ?></td>
        <td><?= htmlspecialchars($s['type']) ?></td>
        <td><?= htmlspecialchars($s['service_name']) ?></td>
        <td>৳<?= number_format($s['price']) ?></td>
        <td><?= $s['availability'] ? '✅' : '❌' ?></td>
        <td><a href="?toggle=<?= $s['service_id'] ?>">Toggle</a></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
