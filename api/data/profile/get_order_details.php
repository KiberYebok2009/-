<?php
    header('Content-Type: application/json');
    
    include_once '../../config/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    $data = json_decode(file_get_contents("php://input"));
    
    if (empty($data->order_id)) {
        echo json_encode(array("error" => "Ошибка: Не передан ID заказа."));
        exit;
    }
    
    $order_id = $data->order_id;
    
    $query = "SELECT order_items.*, products.name FROM order_items JOIN products ON order_items.product_id = products.id WHERE order_items.order_id = :order_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':order_id', $order_id);
    $stmt->execute();
    
    $order_details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($order_details);
?>