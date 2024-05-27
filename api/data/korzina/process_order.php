<?php
    header('Content-Type: application/json');
    
    include_once '../../config/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    $data = json_decode(file_get_contents("php://input"));
    
    if (empty($data->user_id)) {
        echo json_encode(array("error" => "Ошибка: Не передан ID пользователя."));
        exit;
    }
    
    // Добавляем новые параметры
    $trade_link = isset($data->trade_link) ? $data->trade_link : "";
    $payment_method = isset($data->payment_method) ? $data->payment_method : "";

    
    $user_id = $data->user_id;
    $total = 0;
    
    try {
        $db->beginTransaction();
    
        // Получаем товары из корзины пользователя и рассчитываем общую стоимость
        $query = "SELECT cart_items.product_id, cart_items.quantity, products.price FROM cart_items JOIN products ON cart_items.product_id = products.id WHERE cart_items.user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        foreach ($cart_items as $item) {
            $total += $item['price'] * $item['quantity'];
        }
    
        // Создаем заказ с рассчитанной общей стоимостью и новыми данными
        $query = "INSERT INTO orders (user_id, total, order_date, status, trade_link, payment_method) VALUES (:user_id, :total, NOW(), 'Обработка', :trade_link, :payment_method)";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->bindParam(':total', $total);
        $stmt->bindParam(':trade_link', $trade_link);
        $stmt->bindParam(':payment_method', $payment_method);
        $stmt->execute();
        $order_id = $db->lastInsertId();
    
        // Добавляем товары в order_items
        foreach ($cart_items as $item) {
            $query = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
            $stmt = $db->prepare($query);
            $stmt->bindParam(':order_id', $order_id);
            $stmt->bindParam(':product_id', $item['product_id']);
            $stmt->bindParam(':quantity', $item['quantity']);
            $stmt->bindParam(':price', $item['price']);
            $stmt->execute();
        }
    
        // Очищаем корзину пользователя
        $query = "DELETE FROM cart_items WHERE user_id = :user_id";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
    
        $db->commit();
    
        echo json_encode(array("message" => "Заказ успешно оформлен."));
    } catch (Exception $e) {
        $db->rollBack();
        echo json_encode(array("error" => "Ошибка при оформлении заказа: " . $e->getMessage()));
    }
?>