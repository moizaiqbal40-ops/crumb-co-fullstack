<?php
/**
 * GET /api/products.php
 * Returns all available products as JSON. Used by index.php on page load,
 * and can also be called directly (e.g. from a future mobile app) — a real
 * REST-style read endpoint on top of the same database.
 */

header("Content-Type: application/json");
require_once __DIR__ . "/../config/db.php";

$stmt = $pdo->query(
    "SELECT id, name, category, tag, price, description,
            color1, color2, size, image
     FROM products
     WHERE is_available = 1
     ORDER BY id ASC"
);
$products = $stmt->fetchAll();

// Reshape to match the JS PRODUCTS array field names (c1, c2) the frontend expects.
$formatted = array_map(function ($p) {
    return [
        "id" => (int) $p["id"],
        "name" => $p["name"],
        "category" => $p["category"],
        "tag" => $p["tag"],
        "price" => (int) $p["price"],
        "desc" => $p["description"],
        "c1" => $p["color1"],
        "c2" => $p["color2"],
        "size" => $p["size"],
        "image" => $p["image"],
    ];
}, $products);

echo json_encode($formatted);
