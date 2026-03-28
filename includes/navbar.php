<?php
// includes/navbar.php
// ─── HOW TO CUSTOMIZE ───────────────────────────────────────
// - Change brand name: edit "Go BD" below
// - Add/remove nav links in the respective login state blocks
// - Add class="active" to the current page link manually
//   e.g. <a href="..." class="active">Places</a>
// ────────────────────────────────────────────────────────────
// Requires auth.php to be included before this file.
?>
<header>
    <nav class="navbar">
        <a href="../public/index.php" class="navbar-brand">🌿 Go BD</a>

        <div class="navbar-links">
            <a href="../public/places.php">Places</a>
            <a href="../public/events.php">Events</a>

            <?php if (isLoggedIn()): ?>
                <a href="../tourist/hotels.php">Hotels</a>
                <a href="../tourist/restaurants.php">Restaurants</a>
                <a href="../tourist/transport.php">Transport</a>
                <a href="../tourist/tour_guides.php">Guides</a>
                <a href="../tourist/trip_planner.php">Trip Planner</a>
            <?php endif; ?>

            <?php if (isAdmin()): ?>
                <a href="admin/dashboard.php">Admin Panel</a>
            <?php endif; ?>
        </div>

        <div class="navbar-right">
            <?php if (isLoggedIn()): ?>
                <a href="../tourist/my_bookings.php" class="btn-nav-outline">My Bookings</a>
                <a href="../tourist/profile.php" class="btn-nav-outline"><?= htmlspecialchars($_SESSION['tourist_name'] ?? 'Profile') ?></a>
                <a href="../public/logout.php" class="btn-nav-solid">Logout</a>
            <?php elseif (isAdmin()): ?>
                <a href="../admin/dashboard.php" class="btn-nav-outline"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></a>
                <a href="../public/logout.php" class="btn-nav-solid">Logout</a>
            <?php else: ?>
                <a href="../public/login.php" class="btn-nav-outline">Login</a>
                <a href="../public/register.php" class="btn-nav-solid">Register</a>
            <?php endif; ?>
        </div>
    </nav>
</header>
