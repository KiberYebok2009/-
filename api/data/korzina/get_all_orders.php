<?php
header('Content-Type: application/json');

include_once '../../config/database.php';

$database = new Database();
$db = $database->getConnection();

$query = "SELECT orders.*, users.firstname, users.lastname FROM orders JOIN users ON orders.user_id = users.id ORDER BY orders.order_date DESC";
$stmt = $db->prepare($query);
$stmt->execute();

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($orders);
?>