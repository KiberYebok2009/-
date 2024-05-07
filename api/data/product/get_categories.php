<?php
    header('Content-Type: application/json');
    
    // Подключение к базе данных
    include_once '../../config/database.php';
    include_once '../../objects/category.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    $category = new Category($db);
    
    $stmt = $category->read(); // Предполагается, что у вас есть метод read в классе Category
    $categories_arr = array();
    
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        array_push($categories_arr, $row);
    }
    
    echo json_encode($categories_arr);
?>