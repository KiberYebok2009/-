<?php
    header('Content-Type: application/json');
    
    include_once '../../config/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    $data = json_decode(file_get_contents("php://input"));
    
    if (empty($data->user_id) || empty($data->product_id)) {
        echo json_encode(array("error" => "Ошибка: Не переданы необходимые данные."));
        exit;
    }
    
    $user_id = $data->user_id;
    $product_id = $data->product_id;
    
    // Проверяем, есть ли уже такой товар в корзине пользователя
    $query = "SELECT * FROM cart_items WHERE user_id = :user_id AND product_id = :product_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':product_id', $product_id);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        // Товар уже есть в корзине, обновляем количество
        $query = "UPDATE cart_items SET quantity = quantity + 1 WHERE user_id = :user_id AND product_id = :product_id";
    } else {
        // Товара нет в корзине, добавляем новую запись
        $query = "INSERT INTO cart_items (user_id, product_id, quantity) VALUES (:user_id, :product_id, 1)";
    }
    
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $user_id);
    $stmt->bindParam(':product_id', $product_id);
    
    if($stmt->execute()) {
        echo json_encode(array("message" => "Товар обновлен в корзине."));
    } else {
        echo json_encode(array("error" => "Ошибка при обновлении корзины."));
    }
?>