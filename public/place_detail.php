<?php
// public/place_detail.php — SINGLE PLACE DETAIL PAGE
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';

$place_id = (int)($_GET['id'] ?? 0);
$place = getPlaceById($conn, $place_id);
if (!$place) { header("Location: places.php"); exit(); }

$pageTitle = $place['name'];

// Events at this place
$events = $conn->query(
    "SELECT * FROM EVENT WHERE place_id = $place_id AND event_date >= CURDATE() ORDER BY event_date ASC"
);

// Reviews
$reviews = $conn->query(
    "SELECT r.*, p.name AS tourist_name
     FROM REVIEW r JOIN PERSON p ON r.tourist_id = p.id
     WHERE r.tp_id = $place_id ORDER BY r.review_date DESC"
);
$avg_rating = $conn->query("SELECT AVG(rating) AS avg FROM REVIEW WHERE tp_id = $place_id")->fetch_assoc()['avg'];

// Nearby hotels
$hotels = $conn->query(
    "SELECT s.service_id, s.service_name, s.price, sp.company_name, h.category
     FROM SERVICE s JOIN SERVICE_PROVIDER sp ON s.provider_id = sp.provider_id
     JOIN HOTEL h ON sp.provider_id = h.provider_id
     WHERE s.availability = 1 LIMIT 3"
);

// Nearby restaurants
$restaurants = $conn->query(
    "SELECT s.service_id, s.service_name, s.price, sp.company_name, r.meal_type
     FROM SERVICE s JOIN SERVICE_PROVIDER sp ON s.provider_id = sp.provider_id
     JOIN RESTAURANT r ON sp.provider_id = r.provider_id
     WHERE s.availability = 1 LIMIT 3"
);

// Nearby transport
$transport = $conn->query(
    "SELECT s.service_id, s.service_name, s.price, sp.company_name, t.vehicle_type
     FROM SERVICE s JOIN SERVICE_PROVIDER sp ON s.provider_id = sp.provider_id
     JOIN TRANSPORT t ON sp.provider_id = t.provider_id
     WHERE s.availability = 1 LIMIT 3"
);

include '../includes/header.php';
include '../includes/navbar.php';
?>

<!-- BREADCRUMB -->
<div class="breadcrumb-bar">
    <div class="breadcrumb-bar__inner">
        <a href="index.php">Home</a>
        <span>›</span>
        <a href="places.php">Places</a>
        <span>›</span>
        <span><?= htmlspecialchars($place['name']) ?></span>
    </div>
</div>

<!-- PLACE HERO -->
<div class="detail-hero">
    <div class="detail-hero__img-wrap">
        <?php if ($place['photo']): ?>
            <img src="/assets/images/<?= htmlspecialchars($place['photo']) ?>"
                 alt="<?= htmlspecialchars($place['name']) ?>" class="detail-hero__img">
        <?php else: ?>
            <div class="detail-hero__img-placeholder">🏞️</div>
        <?php endif; ?>
    </div>
    <div class="detail-hero__meta">
        <span class="card-category-badge"><?= htmlspecialchars($place['category']) ?></span>
        <h1 class="detail-hero__title"><?= htmlspecialchars($place['name']) ?></h1>
        <div class="detail-hero__info-row">
            <span>📍 <?= htmlspecialchars(($place['street'] ?? '') . ', ' . ($place['area'] ?? '') . ', ' . ($place['city'] ?? 'Chittagong')) ?></span>
            <?php if ($avg_rating): ?>
            <span class="rating-stars">
                ⭐ <?= number_format($avg_rating, 1) ?> / 5
                <span class="text-xs text-muted">(<?= $reviews->num_rows ?> reviews)</span>
            </span>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- MAIN CONTENT -->
<div class="detail-layout">
    <div class="detail-main">

        <!-- Description -->
        <section class="detail-section">
            <h2>About this Place</h2>
            <div class="divider-thick"></div>
            <p class="detail-description"><?= nl2br(htmlspecialchars($place['place_description'])) ?></p>
        </section>

        <!-- GPS / Address -->
        <section class="detail-section">
            <h2>Location</h2>
            <div class="divider-thick"></div>
            <div class="location-card">
                <div class="location-card__row"><span>🏙️ City</span><strong><?= htmlspecialchars($place['city'] ?? 'Chittagong') ?></strong></div>
                <div class="location-card__row"><span>📍 Area</span><strong><?= htmlspecialchars($place['area'] ?? '—') ?></strong></div>
                <div class="location-card__row"><span>🏘️ Street</span><strong><?= htmlspecialchars($place['street'] ?? '—') ?></strong></div>
                <div class="location-card__row"><span>📮 Postal</span><strong><?= htmlspecialchars($place['postal_code'] ?? '—') ?></strong></div>
            </div>
        </section>

        <!-- Upcoming Events -->
        <?php if ($events->num_rows > 0): ?>
        <section class="detail-section">
            <h2>Upcoming Events Here</h2>
            <div class="divider-thick"></div>
            <div class="events-list">
                <?php while ($ev = $events->fetch_assoc()): ?>
                <div class="event-row">
                    <div class="event-row__date">
                        <span class="event-row__day"><?= date('d', strtotime($ev['event_date'])) ?></span>
                        <span class="event-row__month"><?= date('M', strtotime($ev['event_date'])) ?></span>
                    </div>
                    <div class="event-row__info">
                        <h4><?= htmlspecialchars($ev['name']) ?></h4>
                        <p><?= htmlspecialchars($ev['description']) ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
        </section>
        <?php endif; ?>

        <!-- Reviews -->
        <section class="detail-section">
            <div class="detail-section__head">
                <h2>Visitor Reviews</h2>
                <?php if (isLoggedIn()): ?>
                <a href="../tourist/reviews.php?place_id=<?= $place_id ?>" class="btn btn-accent btn-sm">+ Write a Review</a>
                <?php else: ?>
                <a href="login.php" class="btn btn-outline btn-sm">Login to Review</a>
                <?php endif; ?>
            </div>
            <div class="divider-thick"></div>

            <?php if ($reviews->num_rows === 0): ?>
            <div class="empty-state" style="padding:2rem 0">
                <p>No reviews yet. Be the first to share your experience!</p>
            </div>
            <?php else: ?>
            <div class="reviews-list">
                <?php while ($rev = $reviews->fetch_assoc()): ?>
                <div class="review-card">
                    <div class="review-card__header">
                        <div class="review-card__avatar"><?= strtoupper(substr($rev['tourist_name'], 0, 1)) ?></div>
                        <div>
                            <strong><?= htmlspecialchars($rev['tourist_name']) ?></strong>
                            <div class="review-card__rating">
                                <?= str_repeat('⭐', (int)$rev['rating']) ?>
                                <span class="text-xs text-muted"><?= $rev['review_date'] ?></span>
                            </div>
                        </div>
                    </div>
                    <p><?= htmlspecialchars($rev['comment']) ?></p>
                </div>
                <?php endwhile; ?>
            </div>
            <?php endif; ?>
        </section>

    </div>

    <!-- SIDEBAR -->
    <aside class="detail-sidebar">

        <!-- Book CTA -->
        <?php if (isLoggedIn()): ?>
        <div class="detail-cta-card">
            <h3>Plan a trip here</h3>
            <p>Book hotels, guides, and transport all in one place.</p>
            <a href="../tourist/trip_planner.php?place_id=<?= $place_id ?>" class="btn btn-primary btn-full">
                🗺️ Plan This Trip
            </a>
        </div>
        <?php else: ?>
        <div class="detail-cta-card">
            <h3>Want to visit?</h3>
            <p>Register for free to book services and plan your trip.</p>
            <a href="register.php" class="btn btn-accent btn-full">Create Free Account</a>
            <a href="login.php"    class="btn btn-ghost btn-full" style="margin-top:.5rem">Already a member</a>
        </div>
        <?php endif; ?>

        <!-- Nearby Hotels -->
        <div class="nearby-section">
            <h3>Nearby Hotels</h3>
            <?php while ($h = $hotels->fetch_assoc()): ?>
            <div class="nearby-item">
                <div class="nearby-item__info">
                    <strong><?= htmlspecialchars($h['company_name']) ?></strong>
                    <span><?= htmlspecialchars($h['category']) ?></span>
                </div>
                <div class="nearby-item__price">৳<?= number_format($h['price']) ?></div>
            </div>
            <?php endwhile; ?>
            <a href="../tourist/hotels.php" class="btn btn-ghost btn-sm btn-full" style="margin-top:.75rem">View All Hotels →</a>
        </div>

        <!-- Nearby Restaurants -->
        <div class="nearby-section">
            <h3>Restaurants</h3>
            <?php while ($r = $restaurants->fetch_assoc()): ?>
            <div class="nearby-item">
                <div class="nearby-item__info">
                    <strong><?= htmlspecialchars($r['company_name']) ?></strong>
                    <span><?= htmlspecialchars($r['meal_type']) ?></span>
                </div>
                <div class="nearby-item__price">৳<?= number_format($r['price']) ?></div>
            </div>
            <?php endwhile; ?>
            <a href="../tourist/restaurants.php" class="btn btn-ghost btn-sm btn-full" style="margin-top:.75rem">View All →</a>
        </div>

        <!-- Transport -->
        <div class="nearby-section">
            <h3>Transport</h3>
            <?php while ($t = $transport->fetch_assoc()): ?>
            <div class="nearby-item">
                <div class="nearby-item__info">
                    <strong><?= htmlspecialchars($t['company_name']) ?></strong>
                    <span><?= htmlspecialchars($t['vehicle_type']) ?></span>
                </div>
                <div class="nearby-item__price">৳<?= number_format($t['price']) ?></div>
            </div>
            <?php endwhile; ?>
            <a href="../tourist/transport.php" class="btn btn-ghost btn-sm btn-full" style="margin-top:.75rem">View All →</a>
        </div>

    </aside>
</div>

<?php include '../includes/footer.php'; ?>
