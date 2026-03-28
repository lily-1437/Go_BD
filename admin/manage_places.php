<?php
// admin/manage_places.php
// CRUD for TOURIST_PLACE
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';
requireAdmin();

$msg = '';

// DELETE
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM TOURIST_PLACE WHERE place_id = $id");
    $msg = "Place deleted.";
}

// INSERT / UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = clean($conn, $_POST['name']);
    $city  = clean($conn, $_POST['city']);
    $cat   = clean($conn, $_POST['category']);
    $desc  = clean($conn, $_POST['description']);
    $id    = (int)($_POST['place_id'] ?? 0);

    if ($id) {
        $conn->query("UPDATE TOURIST_PLACE SET name='$name', city='$city', category='$cat', place_description='$desc' WHERE place_id=$id");
        $msg = "Place updated.";
    } else {
        $conn->query("INSERT INTO TOURIST_PLACE (name, city, category, place_description) VALUES ('$name','$city','$cat','$desc')");
        $msg = "Place added.";
    }
}

$places = $conn->query("SELECT * FROM TOURIST_PLACE ORDER BY name ASC");

$pageTitle = 'Manage Places';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<h1>Manage Tourist Places</h1>
<?php if ($msg): ?><p class="success"><?= $msg ?></p><?php endif; ?>

<h2>Add New Place</h2>
<form method="POST">
    <input type="hidden" name="place_id" value="0">
    <label>Name        <input type="text" name="name" required></label>
    <label>City        <input type="text" name="city" value="Chittagong"></label>
    <label>Category    <input type="text" name="category" required></label>
    <label>Description <textarea name="description" rows="3"></textarea></label>
    <button type="submit">Add Place</button>
</form>

<h2>All Places</h2>
<table>
    <thead><tr><th>ID</th><th>Name</th><th>City</th><th>Category</th><th>Actions</th></tr></thead>
    <tbody>
    <?php while ($p = $places->fetch_assoc()): ?>
    <tr>
        <td><?= $p['place_id'] ?></td>
        <td><?= htmlspecialchars($p['name']) ?></td>
        <td><?= htmlspecialchars($p['city']) ?></td>
        <td><?= htmlspecialchars($p['category']) ?></td>
        <td>
            <a href="?delete=<?= $p['place_id'] ?>" onclick="return confirm('Delete this place?')">Delete</a>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
