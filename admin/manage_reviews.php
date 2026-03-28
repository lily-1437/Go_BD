<?php
// admin/manage_reviews.php
// MODERATE REVIEWS — view and delete
require_once '../includes/auth.php';
require_once '../config/db.php';
requireAdmin();

$msg = '';
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM REVIEW WHERE review_id = $id");
    $msg = "Review deleted.";
}

$reviews = $conn->query(
    "SELECT r.review_id, r.rating, r.comment, r.review_date,
            p.name AS tourist_name,
            tp.name AS place_name
     FROM REVIEW r
     JOIN PERSON p ON r.tourist_id = p.id
     LEFT JOIN TOURIST_PLACE tp ON r.tp_id = tp.place_id
     ORDER BY r.review_date DESC"
);

$pageTitle = 'Manage Reviews';
include '../includes/header.php';
include '../includes/navbar.php';
?>

<h1>Manage Reviews</h1>
<?php if ($msg): ?><p class="success"><?= $msg ?></p><?php endif; ?>

<table>
    <thead><tr><th>Tourist</th><th>Place</th><th>Rating</th><th>Comment</th><th>Date</th><th>Action</th></tr></thead>
    <tbody>
    <?php while ($r = $reviews->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($r['tourist_name']) ?></td>
        <td><?= htmlspecialchars($r['place_name'] ?? 'Event') ?></td>
        <td><?= $r['rating'] ?>/5</td>
        <td><?= htmlspecialchars($r['comment']) ?></td>
        <td><?= $r['review_date'] ?></td>
        <td><a href="?delete=<?= $r['review_id'] ?>" onclick="return confirm('Delete this review?')">Delete</a></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>

<?php include '../includes/footer.php'; ?>
