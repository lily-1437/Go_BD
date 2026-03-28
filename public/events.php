<?php
// public/events.php — EVENTS LISTING PAGE
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';

$pageTitle = 'Events';

$month  = $_GET['month'] ?? '';
$search = $_GET['search'] ?? '';

$where = ["e.event_date >= CURDATE()"];
if ($month)  $where[] = "DATE_FORMAT(e.event_date, '%Y-%m') = '" . $conn->real_escape_string($month) . "'";
if ($search) $where[] = "e.name LIKE '%" . $conn->real_escape_string($search) . "%'";

$events = $conn->query(
    "SELECT e.*, tp.name AS place_name, tp.place_id
     FROM EVENT e JOIN TOURIST_PLACE tp ON e.place_id = tp.place_id
     WHERE " . implode(' AND ', $where) . "
     ORDER BY e.event_date ASC"
);

// Month options
$months = $conn->query(
    "SELECT DISTINCT DATE_FORMAT(event_date, '%Y-%m') AS ym,
            DATE_FORMAT(event_date, '%M %Y') AS label
     FROM EVENT WHERE event_date >= CURDATE() ORDER BY ym ASC"
);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<!-- PAGE HEADER -->
<div class="inner-page-header inner-page-header--events">
    <div class="inner-page-header__content">
        <span class="section-eyebrow">What's On</span>
        <h1>Upcoming Events</h1>
        <p>Festivals, cultural shows, and local experiences across Chittagong.</p>
    </div>
</div>

<!-- FILTERS -->
<div class="filters-bar">
    <form class="filters-form" method="GET">
        <div class="search-wrap">
            <span class="search-icon">🔍</span>
            <input type="text" name="search" placeholder="Search events…"
                   value="<?= htmlspecialchars($search) ?>">
        </div>
        <select name="month" onchange="this.form.submit()">
            <option value="">All Upcoming</option>
            <?php while ($m = $months->fetch_assoc()): ?>
            <option value="<?= $m['ym'] ?>" <?= $month === $m['ym'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($m['label']) ?>
            </option>
            <?php endwhile; ?>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">Filter</button>
        <?php if ($search || $month): ?>
        <a href="events.php" class="btn btn-ghost btn-sm">Clear</a>
        <?php endif; ?>
    </form>
</div>

<!-- EVENTS GRID -->
<div class="page-content">
    <?php if ($events->num_rows === 0): ?>
    <div class="empty-state">
        <div class="empty-state__icon">📅</div>
        <h3>No events found</h3>
        <p>Try a different month or check back soon for new events.</p>
        <a href="events.php" class="btn btn-primary">View All Events</a>
    </div>
    <?php else: ?>
    <div class="events-grid">
        <?php
        $current_month = '';
        while ($ev = $events->fetch_assoc()):
            $ev_month = date('F Y', strtotime($ev['event_date']));
            if ($ev_month !== $current_month):
                $current_month = $ev_month;
        ?>
        <div class="events-month-label"><?= $ev_month ?></div>
        <?php endif; ?>

        <article class="event-card-big">
            <div class="event-card-big__date">
                <span class="event-card-big__day"><?= date('d', strtotime($ev['event_date'])) ?></span>
                <span class="event-card-big__month"><?= date('M', strtotime($ev['event_date'])) ?></span>
                <span class="event-card-big__year"><?= date('Y', strtotime($ev['event_date'])) ?></span>
            </div>
            <div class="event-card-big__body">
                <h3><?= htmlspecialchars($ev['name']) ?></h3>
                <p class="event-card-big__place">
                    📍 <a href="place_detail.php?id=<?= $ev['place_id'] ?>">
                        <?= htmlspecialchars($ev['place_name']) ?>
                    </a>
                </p>
                <p class="event-card-big__desc"><?= htmlspecialchars($ev['description']) ?></p>
            </div>
            <a href="place_detail.php?id=<?= $ev['place_id'] ?>" class="btn btn-outline btn-sm event-card-big__cta">
                View Place
            </a>
        </article>
        <?php endwhile; ?>
    </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
