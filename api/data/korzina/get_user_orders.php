<?php
    header('Content-Type: application/json');
    
    include_once '../../config/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    $data = json_decode(file_get_contents("php://input"));
    
    if (empty($data->user_id)) {
        echo json_encode(array("error" => "Ошибка: Не переданы необходимые данные."));
        exit;
    }
    
    $user_id = $data->user_id;
    
    $query = "SELECT order_id, total, order_date, status FROM orders WHERE user_id = :user_id ORDER BY order_date DESC";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->execute();
    
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($orders);
?>