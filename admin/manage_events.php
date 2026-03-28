<?php
// admin/manage_events.php
// CRUD for EVENT
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';
requireAdmin();

$msg = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM EVENT WHERE event_id = $id");
    $msg = "Event deleted.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = clean($conn, $_POST['name']);
    $place_id = (int)$_POST['place_id'];
    $date     = clean($conn, $_POST['event_date']);
    $desc     = clean($conn, $_POST['description']);
    $conn->query("INSERT INTO EVENT (name, place_id, event_date, description) VALUES ('$name',$place_id,'$date','$desc')");
    $msg = "Event added.";
}

$events = $conn->query("SELECT e.*, tp.name AS place_name FROM EVENT e JOIN TOURIST_PLACE tp ON e.place_id = tp.place_id ORDER BY e.event_date DESC");
$places = $conn->query("SELECT place_id, name FROM TOURIST_PLACE ORDER BY name");

$pageTitle = 'Manage Events';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<h1>Manage Events</h1>
<?php if ($msg): ?><p class="success"><?= $msg ?></p><?php endif; ?>

<h2>Add Event</h2>
<form method="POST">
    <label>Event Name <input type="text" name="name" required></label>
    <label>Place
        <select name="place_id">
            <?php while ($p = $places->fetch_assoc()): ?>
            <option value="<?= $p['place_id'] ?>"><?= htmlspecialchars($p['name']) ?></option>
            <?php endwhile; ?>
        </select>
    </label>
    <label>Date <input type="date" name="event_date" required></label>
    <label>Description <textarea name="description" rows="3"></textarea></label>
    <button type="submit">Add Event</button>
</form>

<h2>All Events</h2>
<table>
    <thead><tr><th>Name</th><th>Place</th><th>Date</th><th>Action</th></tr></thead>
    <tbody>
    <?php while ($e = $events->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($e['name']) ?></td>
        <td><?= htmlspecialchars($e['place_name']) ?></td>
        <td><?= $e['event_date'] ?></td>
        <td><a href="?delete=<?= $e['event_id'] ?>" onclick="return confirm('Delete?')">Delete</a></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
