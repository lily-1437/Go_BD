<?php
// tourist/profile.php
// TOURIST PROFILE — view and update personal info
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';
requireLogin();

$tid = getCurrentTouristId();
$success = '';

$tourist = $conn->query(
    "SELECT p.name, p.email, t.nationality, t.traveller_type
     FROM PERSON p JOIN TOURIST t ON p.id = t.id WHERE p.id = $tid"
)->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name        = clean($conn, $_POST['name']);
    $nationality = clean($conn, $_POST['nationality']);
    $trav_type   = clean($conn, $_POST['traveller_type']);

    $conn->query("UPDATE PERSON SET name = '$name' WHERE id = $tid");
    $conn->query("UPDATE TOURIST SET nationality = '$nationality', traveller_type = '$trav_type' WHERE id = $tid");
    $success = "Profile updated.";
    $tourist['name'] = $name;
    $tourist['nationality'] = $nationality;
    $tourist['traveller_type'] = $trav_type;
}

$pageTitle = 'My Profile';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<h1>My Profile</h1>
<?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>

<form method="POST">
    <label>Full Name <input type="text" name="name" value="<?= htmlspecialchars($tourist['name']) ?>" required></label>
    <label>Email <input type="email" value="<?= htmlspecialchars($tourist['email']) ?>" disabled></label>
    <label>Nationality <input type="text" name="nationality" value="<?= htmlspecialchars($tourist['nationality']) ?>" required></label>
    <label>Traveller Type
        <select name="traveller_type">
            <option value="family"      <?= $tourist['traveller_type']=='family'?'selected':'' ?>>Family</option>
            <option value="solo"        <?= $tourist['traveller_type']=='solo'?'selected':'' ?>>Solo</option>
            <option value="female_solo" <?= $tourist['traveller_type']=='female_solo'?'selected':'' ?>>Female Solo</option>
        </select>
    </label>
    <button type="submit">Save Changes</button>
</form>

<?php include '../includes/footer.php'; ?>
