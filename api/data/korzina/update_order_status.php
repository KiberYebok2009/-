<?php
    header('Content-Type: application/json');
    
    include_once '../../config/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    $data = json_decode(file_get_contents("php://input"));
    
    if (empty($data->order_id) || empty($data->status)) {
        echo json_encode(array("error" => "Ошибка: Не переданы необходимые данные."));
        exit;
    }
    
    $order_id = $data->order_id;
    $status = $data->status;
    
    $query = "UPDATE orders SET status = :status WHERE order_id = :order_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':order_id', $order_id);
    
    if($stmt->execute()) {
        echo json_encode(array("message" => "Статус заказа обновлен."));
    } else {
        echo json_encode(array("error" => "Ошибка при обновлении статуса заказа."));
    }
?>