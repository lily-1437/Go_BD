<?php
// tourist/reviews.php
// LEAVE A REVIEW for a place or event
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';
requireLogin();

$tid      = getCurrentTouristId();
$place_id = (int)($_GET['place_id'] ?? 0);
$event_id = (int)($_GET['event_id'] ?? 0);
$success  = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating   = (int)$_POST['rating'];
    $comment  = clean($conn, $_POST['comment']);
    $pid = $place_id ?: 'NULL';
    $eid = $event_id ?: 'NULL';
    $conn->query(
        "INSERT INTO REVIEW (tourist_id, tp_id, event_id, rating, comment, review_date)
         VALUES ($tid, $pid, $eid, $rating, '$comment', CURDATE())"
    );
    $success = "Review submitted. Thank you!";
}

$pageTitle = 'Leave a Review';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<h1>Leave a Review</h1>
<?php if ($success): ?><p class="success"><?= $success ?></p><?php endif; ?>

<form method="POST">
    <label>Rating
        <select name="rating">
            <?php for ($i = 5; $i >= 1; $i--): ?>
            <option value="<?= $i ?>"><?= $i ?> Star<?= $i > 1 ? 's' : '' ?></option>
            <?php endfor; ?>
        </select>
    </label>
    <label>Comment <textarea name="comment" rows="4" required></textarea></label>
    <button type="submit">Submit Review</button>
</form>

<?php include '../includes/footer.php'; ?>
