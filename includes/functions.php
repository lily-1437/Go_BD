<?php
// includes/functions.php
// Reusable query helpers used across pages.

// Sanitize user input
function clean($conn, $input) {
    return $conn->real_escape_string(htmlspecialchars(strip_tags(trim($input))));
}

// Get all tourist places (with optional category filter)
function getTouristPlaces($conn, $category = null) {
    $sql = "SELECT * FROM TOURIST_PLACE";
    if ($category) {
        $cat = $conn->real_escape_string($category);
        $sql .= " WHERE category = '$cat'";
    }
    $sql .= " ORDER BY name ASC";
    return $conn->query($sql);
}

// Get single place by ID
function getPlaceById($conn, $place_id) {
    $id = (int)$place_id;
    $result = $conn->query("SELECT * FROM TOURIST_PLACE WHERE place_id = $id");
    return $result->fetch_assoc();
}

// Get services by type (HOTEL, RESTAURANT, TRANSPORT, TOUR_GUIDE)
function getServicesByType($conn, $type) {
    $t = $conn->real_escape_string($type);
    return $conn->query(
        "SELECT s.*, sp.company_name, sp.location_city
         FROM SERVICE s
         JOIN SERVICE_PROVIDER sp ON s.provider_id = sp.provider_id
         WHERE sp.type = '$t' AND s.availability = 1
         ORDER BY s.price ASC"
    );
}

// Get upcoming events
function getUpcomingEvents($conn) {
    return $conn->query(
        "SELECT e.*, tp.name AS place_name
         FROM EVENT e
         JOIN TOURIST_PLACE tp ON e.place_id = tp.place_id
         WHERE e.event_date >= CURDATE()
         ORDER BY e.event_date ASC"
    );
}

// Get reviews for a place
function getReviewsForPlace($conn, $place_id) {
    $id = (int)$place_id;
    return $conn->query(
        "SELECT r.*, p.name AS tourist_name
         FROM REVIEW r
         JOIN PERSON p ON r.tourist_id = p.id
         WHERE r.tp_id = $id
         ORDER BY r.review_date DESC"
    );
}

// Get bookings for a tourist
function getBookingsByTourist($conn, $tourist_id) {
    $id = (int)$tourist_id;
    return $conn->query(
        "SELECT b.*, s.service_name, s.price
         FROM BOOKING b
         JOIN SERVICE s ON b.service_id = s.service_id
         WHERE b.tourist_id = $id
         ORDER BY b.booking_date DESC"
    );
}
