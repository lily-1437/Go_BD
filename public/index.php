<?php
// public/index.php — HOME / LANDING PAGE
require_once '../includes/auth.php';
require_once '../config/db.php';
require_once '../includes/functions.php';

$pageTitle = 'Home';
include '../includes/header.php';
include '../includes/navbar.php';

$featured_places = $conn->query(
    "SELECT * FROM TOURIST_PLACE ORDER BY RAND() LIMIT 3"
);
$upcoming_events = $conn->query(
    "SELECT e.*, tp.name AS place_name FROM EVENT e
     JOIN TOURIST_PLACE tp ON e.place_id = tp.place_id
     WHERE e.event_date >= CURDATE()
     ORDER BY e.event_date ASC LIMIT 3"
);
$stats = [
    'places'   => $conn->query("SELECT COUNT(*) AS c FROM TOURIST_PLACE")->fetch_assoc()['c'],
    'hotels'   => $conn->query("SELECT COUNT(*) AS c FROM HOTEL")->fetch_assoc()['c'],
    'guides'   => $conn->query("SELECT COUNT(*) AS c FROM TOUR_GUIDE")->fetch_assoc()['c'],
    'tourists' => $conn->query("SELECT COUNT(*) AS c FROM TOURIST")->fetch_assoc()['c'],
];
?>

<!-- HERO -->
<section class="home-hero">
    <div class="home-hero__bg-shapes">
        <span class="shape shape-1"></span>
        <span class="shape shape-2"></span>
        <span class="shape shape-3"></span>
    </div>
    <div class="home-hero__content">
        <span class="home-hero__tag">🌿 Bangladesh Tourism</span>
        <h1 class="home-hero__title">
            Discover the<br>
            <em>Soul of Chittagong</em>
        </h1>
        <p class="home-hero__sub">
            Your smart travel companion — find places, book services, and
            plan unforgettable trips across Bangladesh's most vibrant city.
        </p>
        <div class="home-hero__actions">
            <a href="../public/places.php"   class="btn btn-accent btn-lg">Explore Places</a>
            <a href="../public/register.php" class="btn btn-hero-outline btn-lg">Get Started Free</a>
        </div>
    </div>
    <div class="home-hero__mosaic" aria-hidden="true">
        <div class="mosaic-cell mosaic-a">🏔️</div>
        <div class="mosaic-cell mosaic-b">🕌</div>
        <div class="mosaic-cell mosaic-c">🌊</div>
        <div class="mosaic-cell mosaic-d">🌿</div>
    </div>
</section>

<!-- STATS STRIP -->
<section class="home-stats">
    <div class="home-stats__inner">
        <div class="home-stat">
            <span class="home-stat__num"><?= $stats['places'] ?>+</span>
            <span class="home-stat__label">Tourist Places</span>
        </div>
        <div class="home-stat__divider"></div>
        <div class="home-stat">
            <span class="home-stat__num"><?= $stats['hotels'] ?>+</span>
            <span class="home-stat__label">Hotels Listed</span>
        </div>
        <div class="home-stat__divider"></div>
        <div class="home-stat">
            <span class="home-stat__num"><?= $stats['guides'] ?>+</span>
            <span class="home-stat__label">Verified Guides</span>
        </div>
        <div class="home-stat__divider"></div>
        <div class="home-stat">
            <span class="home-stat__num"><?= $stats['tourists'] ?>+</span>
            <span class="home-stat__label">Happy Travellers</span>
        </div>
    </div>
</section>

<!-- FEATURED PLACES -->
<section class="home-section">
    <div class="home-section__header">
        <div>
            <span class="section-eyebrow">Top Destinations</span>
            <h2>Featured Places</h2>
        </div>
        <a href="../public/places.php" class="btn btn-outline btn-sm">View All →</a>
    </div>
    <div class="card-grid">
        <?php while ($p = $featured_places->fetch_assoc()): ?>
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
                <p class="card-meta">📍 <?= htmlspecialchars($p['city']) ?></p>
                <h3 class="card-title"><?= htmlspecialchars($p['name']) ?></h3>
                <p class="card-text"><?= htmlspecialchars($p['place_description']) ?></p>
            </div>
            <div class="card-footer">
                <a href="place_detail.php?id=<?= $p['place_id'] ?>" class="btn btn-primary btn-sm">
                    View Details
                </a>
            </div>
        </article>
        <?php endwhile; ?>
    </div>
</section>

<!-- HOW IT WORKS -->
<section class="home-how">
    <div class="home-section__header">
        <div>
            <span class="section-eyebrow">Simple &amp; Smart</span>
            <h2>How Go BD Works</h2>
        </div>
    </div>
    <div class="how-steps">
        <div class="how-step">
            <div class="how-step__icon">🔍</div>
            <h4>1. Explore</h4>
            <p>Browse tourist places, hotels, restaurants, and events — all in one place.</p>
        </div>
        <div class="how-step__arrow">→</div>
        <div class="how-step">
            <div class="how-step__icon">🗺️</div>
            <h4>2. Plan</h4>
            <p>Use the Trip Planner to build a personalised itinerary based on your travel type.</p>
        </div>
        <div class="how-step__arrow">→</div>
        <div class="how-step">
            <div class="how-step__icon">✅</div>
            <h4>3. Book</h4>
            <p>Book hotels, guides, and transport directly — securely, in minutes.</p>
        </div>
    </div>
</section>

<!-- UPCOMING EVENTS -->
<section class="home-section home-section--tinted">
    <div class="home-section__header">
        <div>
            <span class="section-eyebrow">Don't Miss Out</span>
            <h2>Upcoming Events</h2>
        </div>
        <a href="../public/events.php" class="btn btn-outline btn-sm">All Events →</a>
    </div>
    <div class="events-list">
        <?php while ($ev = $upcoming_events->fetch_assoc()): ?>
        <div class="event-row">
            <div class="event-row__date">
                <span class="event-row__day"><?= date('d', strtotime($ev['event_date'])) ?></span>
                <span class="event-row__month"><?= date('M', strtotime($ev['event_date'])) ?></span>
            </div>
            <div class="event-row__info">
                <h4><?= htmlspecialchars($ev['name']) ?></h4>
                <p>📍 <?= htmlspecialchars($ev['place_name']) ?></p>
            </div>
            <a href="../public/events.php" class="btn btn-ghost btn-sm">Details</a>
        </div>
        <?php endwhile; ?>
        <?php if (!$upcoming_events->num_rows): ?>
        <p class="text-muted text-center" style="padding:2rem 0">No upcoming events right now. Check back soon!</p>
        <?php endif; ?>
    </div>
</section>

<!-- CTA BANNER -->
<section class="home-cta">
    <div class="home-cta__inner">
        <h2>Ready to explore Bangladesh?</h2>
        <p>Create your free account and start planning your perfect trip today.</p>
        <div class="home-hero__actions">
            <a href="../public/register.php" class="btn btn-accent btn-lg">Create Free Account</a>
            <a href="../public/login.php"    class="btn btn-hero-outline btn-lg">Already a member? Login</a>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
