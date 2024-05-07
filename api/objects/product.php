<?php
    class Product {
        // Подключение к базе данных и имя таблицы
        private $conn;
        private $table_name = "products";
    
        // Свойства объекта
        public $id;
        public $name;
        public $description;
        public $price;
        public $category_id;
        public $created;
        public $path_photo; // Добавлено новое свойство для пути к фото продукта
    
        // Конструктор для соединения с базой данных
        public function __construct($db) {
            $this->conn = $db;
        }
    
        // Метод для чтения продуктов. Если передан ID, возвращает один продукт.
        public function getProducts($id = null, $minPrice = null, $maxPrice = null, $categoryId = null) {
            // Основа запроса
            $query = "SELECT id, name, description, price, path_photo, category_id FROM " . $this->table_name;
    
            // Массив условий для WHERE
            $conditions = [];
            $params = [];
    
            // Добавление условий в массив
            if ($id) {
                $conditions[] = "id = :id";
                $params[':id'] = $id;
            }
            if ($minPrice !== null) {
                $conditions[] = "price >= :minPrice";
                $params[':minPrice'] = $minPrice;
            }
            if ($maxPrice !== null) {
                $conditions[] = "price <= :maxPrice";
                $params[':maxPrice'] = $maxPrice;
            }
            if ($categoryId !== null) {
                $conditions[] = "category_id = :categoryId";
                $params[':categoryId'] = $categoryId;
            }
    
            // Если есть условия, добавляем их в запрос
            if (count($conditions) > 0) {
                $query .= " WHERE " . implode(' AND ', $conditions);
            }
    
            // Добавляем сортировку
            if (!$id) {
                $query .= " ORDER BY created DESC";
            }
    
            // Подготовка запроса
            $stmt = $this->conn->prepare($query);
    
            // Привязка параметров
            foreach ($params as $key => $value) {
                $stmt->bindParam($key, $value);
            }
    
            // Выполнение запроса
            $stmt->execute();
    
            // Если запросился один продукт, возвращаем его
            if ($id) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                return $row ? $row : null;
            } else {
                // Иначе возвращаем массив всех продуктов
                $products_arr = array();
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    array_push($products_arr, $row);
                }
                return $products_arr;
            }
        }
    
        // Метод для чтения одного продукта по ID
        public function readOne() {
            // Запрос к базе данных для получения одного продукта
            $query = "SELECT id, name, description, price, path_photo, category_id FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
    
            // Подготовка запроса
            $stmt = $this->conn->prepare($query);
    
            // Привязка ID продукта к запросу
            $stmt->bindParam(1, $this->id);
    
            // Выполнение запроса
            $stmt->execute();
    
            // Получение данных о продукте
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
    
            // Установка значений свойств объекта
            $this->name = $row['name'];
            $this->description = $row['description'];
            $this->price = $row['price'];
            $this->path_photo = $row['path_photo'];
            $this->category_id = $row['category_id'];
        }
    
        // Метод для создания нового товара
        public function create() {
            $query = "INSERT INTO products (name, description, price, path_photo, category_id)
                SELECT :name, CONCAT('Коллекция: ', c.name), :price, :path_photo, :category_id
                FROM categories c
                WHERE c.id = :category_id;";
                
        
            $stmt = $this->conn->prepare($query);
        
            // очистка и привязка значений
            $stmt->bindParam(':name', htmlspecialchars(strip_tags($this->name)));
            $stmt->bindParam(':price', htmlspecialchars(strip_tags($this->price)));
            $stmt->bindParam(':path_photo', htmlspecialchars(strip_tags($this->path_photo)));
            $stmt->bindParam(':category_id', htmlspecialchars(strip_tags($this->category_id)));
        
            // выполнение запроса
            if ($stmt->execute()) {
                return true;
            }
        
            return false;
        }
        
        // Метод для обновления одного продукта по ID
        public function update_product() {
            $query = "UPDATE {$this->table_name}
                      SET
                          name = :name,
                          price = :price
                          WHERE id = :id";
        
            $stmt = $this->conn->prepare($query);
        
            // очистка
            $this->name=htmlspecialchars(strip_tags($this->name));
            $this->price=htmlspecialchars(strip_tags($this->price));
            $this->id=htmlspecialchars(strip_tags($this->id));
        
            // привязка значений
            $stmt->bindParam(':name', $this->name);
            $stmt->bindParam(':price', $this->price);
            $stmt->bindParam(':id', $this->id);
        
            // выполнение запроса
            if ($stmt->execute()) {
                return true;
            }
        
            return false;
        }
        
        // Метод для удаления одного продукта по ID
        public function delete() {
            // Запрос на удаление
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
    
            // Подготовка запроса
            $stmt = $this->conn->prepare($query);
    
            // Очистка и привязка id продукта
            $this->id = htmlspecialchars(strip_tags($this->id));
            $stmt->bindParam(':id', $this->id);
    
            // Выполнение запроса
            if ($stmt->execute()) {
                return true;
            }
    
            return false;
        }
        // Другие методы...
    }
?>