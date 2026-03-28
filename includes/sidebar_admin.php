<?php
// includes/sidebar_admin.php
// ─── HOW TO CUSTOMIZE ───────────────────────────────────────
// Add class="active" to the current page link:
//   <a href="dashboard.php" class="active">Dashboard</a>
// Add new sections by copying the section-label + links pattern.
// ────────────────────────────────────────────────────────────
?>
<aside class="sidebar">
    <span class="sidebar-section-label">Overview</span>
    <a href="../admin/dashboard.php">📊 Dashboard</a>

    <span class="sidebar-section-label">Content</span>
    <a href="../admin/manage_places.php">📍 Tourist Places</a>
    <a href="../admin/manage_events.php">📅 Events</a>

    <span class="sidebar-section-label">Services</span>
    <a href="../admin/manage_services.php">🏨 All Services</a>

    <span class="sidebar-section-label">Users</span>
    <a href="../admin/manage_users.php">👤 Tourists</a>
    <a href="../admin/manage_bookings.php">📋 Bookings</a>
    <a href="../admin/manage_reviews.php">⭐ Reviews</a>

    <span class="sidebar-section-label">Account</span>
    <a href="../public/logout.php">🚪 Logout</a>
</aside>
