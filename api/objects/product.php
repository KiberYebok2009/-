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
        public function getProducts($id = null) {
            if ($id) {
                // Запрос на получение одного продукта по ID
                $query = "SELECT id, name, description, price, path_photo, category_id FROM " . $this->table_name . " WHERE id = :id LIMIT 0,1";
            } else {
                // Запрос на получение всех продуктов
                $query = "SELECT id, name, description, price, path_photo, category_id FROM " . $this->table_name . " ORDER BY created DESC";
            }
    
            // Подготовка запроса
            $stmt = $this->conn->prepare($query);
    
            // Привязка параметра ID, если он был передан
            if ($id) {
                $stmt->bindParam(':id', $id);
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
    
        // Метод для создания продукта
        function create() {
            // Запрос SQL для вставки записи
            $query = "INSERT INTO " . $this->table_name . "
                    SET
                        name=:name, description=:description, price=:price, category_id=:category_id, created=:created, path_photo=:path_photo";
    
            // Подготовка запроса
            $stmt = $this->conn->prepare($query);
    
            // Очистка и привязка данных
            $this->name = htmlspecialchars(strip_tags($this->name));
            $this->description = htmlspecialchars(strip_tags($this->description));
            $this->price = htmlspecialchars(strip_tags($this->price));
            $this->category_id = htmlspecialchars(strip_tags($this->category_id));
            $this->created = htmlspecialchars(strip_tags($this->created));
            $this->path_photo = htmlspecialchars(strip_tags($this->path_photo)); // Очистка нового поля path_photo
    
            // Привязка значений
            $stmt->bindParam(":name", $this->name);
            $stmt->bindParam(":description", $this->description);
            $stmt->bindParam(":price", $this->price);
            $stmt->bindParam(":category_id", $this->category_id);
            $stmt->bindParam(":created", $this->created);
            $stmt->bindParam(":path_photo", $this->path_photo); // Привязка нового поля path_photo
    
            // Выполнение запроса
            if($stmt->execute()) {
                return true;
            }
    
            return false;
        }
    
        // Методы update и delete могут быть реализованы аналогично, включая обработку поля path_photo
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
        // Другие методы...
    }
?>