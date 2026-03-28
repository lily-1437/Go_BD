<?php
// public/places.php — TOURIST PLACES LISTING PAGE
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';

$pageTitle = 'Tourist Places';

// Filters from URL
$category = $_GET['category'] ?? '';
$search   = $_GET['search']   ?? '';

// Build query
$where = [];
if ($category) $where[] = "category = '" . $conn->real_escape_string($category) . "'";
if ($search)   $where[] = "(name LIKE '%" . $conn->real_escape_string($search) . "%' OR place_description LIKE '%" . $conn->real_escape_string($search) . "%')";
$sql = "SELECT * FROM TOURIST_PLACE" . ($where ? " WHERE " . implode(' AND ', $where) : "") . " ORDER BY name ASC";
$places = $conn->query($sql);

// All unique categories for filter pills
$cats_result = $conn->query("SELECT DISTINCT category FROM TOURIST_PLACE ORDER BY category");
$categories  = [];
while ($c = $cats_result->fetch_assoc()) $categories[] = $c['category'];

include '../includes/header.php';
include '../includes/navbar.php';
?>

<!-- PAGE HEADER -->
<div class="inner-page-header">
    <div class="inner-page-header__content">
        <span class="section-eyebrow">Chittagong & Beyond</span>
        <h1>Tourist Places</h1>
        <p>Discover <?= $places->num_rows ?> amazing destinations waiting for you.</p>
    </div>
</div>

<!-- FILTERS BAR -->
<div class="filters-bar">
    <form class="filters-form" method="GET">
        <!-- Search -->
        <div class="search-wrap">
            <span class="search-icon">🔍</span>
            <input type="text" name="search" placeholder="Search places…"
                   value="<?= htmlspecialchars($search) ?>">
        </div>

        <!-- Category pills -->
        <div class="filter-pills">
            <a href="places.php"
               class="filter-pill <?= !$category ? 'active' : '' ?>">All</a>
            <?php foreach ($categories as $cat): ?>
            <a href="places.php?category=<?= urlencode($cat) ?><?= $search ? '&search='.urlencode($search) : '' ?>"
               class="filter-pill <?= $category === $cat ? 'active' : '' ?>">
                <?= htmlspecialchars(ucfirst($cat)) ?>
            </a>
            <?php endforeach; ?>
        </div>

        <button type="submit" class="btn btn-primary btn-sm">Search</button>
        <?php if ($search || $category): ?>
        <a href="places.php" class="btn btn-ghost btn-sm">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- PLACES GRID -->
<div class="page-content">
    <?php if ($places->num_rows === 0): ?>
    <div class="empty-state">
        <div class="empty-state__icon">🗺️</div>
        <h3>No places found</h3>
        <p>Try a different search or category filter.</p>
        <a href="places.php" class="btn btn-primary">View All Places</a>
    </div>
    <?php else: ?>
    <div class="card-grid">
        <?php while ($p = $places->fetch_assoc()): ?>
        <article class="card">
            <div class="card-img-wrap">
                <?php if ($p['photo']): ?>
                    <img class="card-img" src="/assets/images/<?= htmlspecialchars($p['photo']) ?>"
                         alt="<?= htmlspecialchars($p['name']) ?>">
                <?php else: ?>
                    <div class="card-img-placeholder">🏞️</div>
                <?php endif; ?>
                <span class="card-category-badge"><?= htmlspecialchars($p['category']) ?></span>
            </div>
            <div class="card-body">
                <p class="card-meta">📍 <?= htmlspecialchars($p['city'] ?? 'Chittagong') ?>, <?= htmlspecialchars($p['area'] ?? '') ?></p>
                <h3 class="card-title"><?= htmlspecialchars($p['name']) ?></h3>
                <p class="card-text"><?= htmlspecialchars($p['place_description']) ?></p>
            </div>
            <div class="card-footer">
                <span class="text-xs text-muted">📮 <?= htmlspecialchars($p['postal_code'] ?? '') ?></span>
                <a href="place_detail.php?id=<?= $p['place_id'] ?>"
                   class="btn btn-primary btn-sm">View Details</a>
            </div>
        </article>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
