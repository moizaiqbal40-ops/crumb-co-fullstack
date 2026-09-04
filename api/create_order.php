<?php
/**
 * POST /api/create_order.php
 * Accepts JSON: { items: [{id, name, price, qty}], customItems: [{base, decor, message, price}], total }
 * Saves the order and its items, returns { success: true, order_id }.
 */

header("Content-Type: application/json");
require_once __DIR__ . "/../config/db.php";

$input = json_decode(file_get_contents("php://input"), true);

if (!$input || (!isset($input["items"]) && !isset($input["customItems"]))) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "No items in order."]);
    exit;
}

$items = $input["items"] ?? [];
$customItems = $input["customItems"] ?? [];
$total = (int) ($input["total"] ?? 0);
$customerName = trim($input["customerName"] ?? "");
$customerPhone = trim($input["customerPhone"] ?? "");
$deliveryAddress = trim($input["deliveryAddress"] ?? "");
$paymentMethod = trim($input["paymentMethod"] ?? "cod");

$allowedMethods = ["cod", "jazzcash", "easypaisa", "card"];
if (!in_array($paymentMethod, $allowedMethods, true)) {
    $paymentMethod = "cod";
}

if (count($items) === 0 && count($customItems) === 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Basket is empty."]);
    exit;
}

if ($customerName === "" || $customerPhone === "" || $deliveryAddress === "") {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "Name, phone, and delivery address are required."]);
    exit;
}

try {
    $pdo->beginTransaction();

    $orderStmt = $pdo->prepare(
        "INSERT INTO orders (customer_name, customer_phone, delivery_address, payment_method, total, status)
         VALUES (?, ?, ?, ?, ?, 'pending')"
    );
    $orderStmt->execute([
        $customerName,
        $customerPhone,
        $deliveryAddress,
        $paymentMethod,
        $total,
    ]);
    $orderId = $pdo->lastInsertId();

    $itemStmt = $pdo->prepare(
        "INSERT INTO order_items (order_id, product_id, product_name, quantity, price, is_custom, custom_base, custom_decor, custom_message)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    foreach ($items as $item) {
        $itemStmt->execute([
            $orderId,
            (int) $item["id"],
            $item["name"],
            (int) $item["qty"],
            (int) $item["price"],
            0,
            null,
            null,
            null,
        ]);
    }

    foreach ($customItems as $custom) {
        $itemStmt->execute([
            $orderId,
            null,
            "Custom Cake",
            1,
            (int) $custom["price"],
            1,
            $custom["base"] ?? null,
            $custom["decor"] ?? null,
            $custom["message"] ?? null,
        ]);
    }

    $pdo->commit();

    echo json_encode(["success" => true, "order_id" => $orderId]);
} catch (Exception $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}
