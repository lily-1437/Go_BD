<?php
// admin/manage_users.php
// VIEW ALL REGISTERED TOURISTS
require_once '../includes/auth.php';
require_once '../config/db.php';
requireAdmin();

$users = $conn->query(
    "SELECT p.id, p.name, p.email, t.nationality, t.traveller_type
     FROM PERSON p JOIN TOURIST t ON p.id = t.id
     ORDER BY p.name ASC"
);

$pageTitle = 'Manage Users';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<h1>Registered Tourists</h1>
<table>
    <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Nationality</th><th>Type</th></tr></thead>
    <tbody>
    <?php while ($u = $users->fetch_assoc()): ?>
    <tr>
        <td><?= $u['id'] ?></td>
        <td><?= htmlspecialchars($u['name']) ?></td>
        <td><?= htmlspecialchars($u['email']) ?></td>
        <td><?= htmlspecialchars($u['nationality']) ?></td>
        <td><?= htmlspecialchars($u['traveller_type']) ?></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
