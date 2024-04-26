<?php
    // Подключение к базе данных и другие необходимые файлы
    include_once 'config/database.php'; // Путь к вашему скрипту подключения к базе данных
    include_once 'objects/product.php'; // Путь к вашему скрипту класса Product
    
    // Получение подключения к базе данных
    $database = new Database();
    $db = $database->getConnection();
    
    // Создание объекта Product
    $product = new Product($db);
    
    // Убедитесь, что данные были отправлены методом POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Проверка наличия и обработка загруженного файла изображения
        if (isset($_FILES['path_photo'])) {
            $image_name = $_FILES['path_photo']['name'];
            $image_tmp = $_FILES['path_photo']['tmp_name'];
            $image_size = $_FILES['path_photo']['size'];
            $image_error = $_FILES['path_photo']['error'];
    
            // Проверка на ошибки и размер файла
            if ($image_error === 0 && $image_size <= 5000000) { // Допустимый размер файла, например, 5MB
                $image_ext = pathinfo($image_name, PATHINFO_EXTENSION);
                $image_ext_lc = strtolower($image_ext);
    
                // Допустимые форматы файлов
                $allowed_exts = array('jpg', 'jpeg', 'png', 'gif');
    
                if (in_array($image_ext_lc, $allowed_exts)) {
                    $new_image_name = uniqid('IMG-', true) . '.' . $image_ext_lc;
                    $image_upload_path = 'images/product/' . $new_image_name;
                    move_uploaded_file($image_tmp, $image_upload_path);
                    $product->path_photo = 'api/' . $image_upload_path;
                } else {
                    echo json_encode(array('message' => 'Недопустимый формат файла.'));
                    exit();
                }
            } else {
                echo json_encode(array('message' => 'Ошибка при загрузке файла.'));
                exit();
            }
        }
    
        // Получение данных из формы
        $product->name = $_POST['name'];
        $product->price = $_POST['price'];
        // $product->path_photo уже установлено при обработке файла
        $product->category_id = $_POST['category_id'];
    
        // Создание товара
        if ($product->create()) {
            // Установка кода ответа - 201 Created
            http_response_code(201);
            echo json_encode(array('message' => 'Товар был создан.'));
        } else {
            // Установка кода ответа - 503 Service Unavailable
            http_response_code(503);
            echo json_encode(array('message' => 'Невозможно создать товар.'));
        }
    } else {
        // Установка кода ответа - 405 Method Not Allowed
        http_response_code(405);
        echo json_encode(array('message' => 'Метод не разрешен.'));
    }
?>