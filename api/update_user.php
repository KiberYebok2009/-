<?php
    // required headers
    header("Access-Control-Allow-Origin: *");
    //header("Content-Type: multipart/form-data; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    // files for decoding jwt
    require "libs/vendor/autoload.php";
    include_once 'config/core.php';
    include_once 'libs/vendor/firebase/php-jwt/src/BeforeValidException.php';
    include_once 'libs/vendor/firebase/php-jwt/src/ExpiredException.php';
    include_once 'libs/vendor/firebase/php-jwt/src/SignatureInvalidException.php';
    include_once 'libs/vendor/firebase/php-jwt/src/JWT.php';
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    
    // database connection
    include_once 'config/database.php';
    include_once 'objects/user.php';
    
    // get database connection
    $database = new Database();
    $db = $database->getConnection();
    
    // instantiate user object
    $user = new User($db);
    
    // retrieve given jwt
    $data = json_decode(file_get_contents("php://input"));
    
    // get jwt
    $jwt = isset($_POST['jwt']) ? $_POST['jwt'] : "";
    
    if($jwt){
        try {
            // decode jwt
            $decoded = JWT::decode($jwt, new Key($key, 'HS256'), array('HS256'));
    
            // set user property values
            $user->id = $decoded->data->id;
            $user->firstname = !empty($_POST['firstname']) ? $_POST['firstname'] : $decoded->data->firstname;
            $user->lastname = !empty($_POST['lastname']) ? $_POST['lastname'] : $decoded->data->lastname;
            $user->profile_photo = $decoded->data->profile_photo; // Используем текущее фото, если новое не загружено

            // Check if a new profile photo was uploaded
            if (isset($_FILES['profile_photo']) && $_FILES['profile_photo']['error'] === UPLOAD_ERR_OK) {
                $profile_photo = $_FILES['profile_photo'];
                $target_directory = "images/user/";
                $target_file = $target_directory . basename($profile_photo["name"]);
                $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $new_filename = $target_directory . uniqid() . '.' . $file_type;
            
                // Пытаемся загрузить файл
                if (move_uploaded_file($profile_photo["tmp_name"], $new_filename)) {
                    // Устанавливаем новый путь к фото профиля
                    $user->profile_photo = 'api/' . $new_filename;
                } else {
                    // Устанавливаем код ответа 400, так как загрузка файла не удалась
                    http_response_code(400);
                    // Выводим сообщение об ошибке
                    echo json_encode(array("message" => "Unable to upload profile photo."));
                    exit();
                }
            }
                
            // update the user record
            if($user->update()){
                // regenerate jwt
                $token = array(
                   "iat" => $issued_at,
                   "exp" => $expiration_time,
                   "iss" => $issuer,
                   "data" => array(
                       "id" => $user->id,
                       "firstname" => $user->firstname,
                       "lastname" => $user->lastname,
                       "profile_photo" => $user->profile_photo // Используем обновленный путь к фото
                   )
                );
                $jwt = JWT::encode($token, $key, 'HS256');
            
                // set response code
                http_response_code(200);
            
                // response in json format
                echo json_encode(
                        array(
                            "message" => "User was updated.",
                            "jwt" => $jwt
                        )
                    );
            } else{
                // set response code
                http_response_code(401);
                // show error message
                echo json_encode(array("message" => "Unable to update user."));
            }
        } catch (Exception $e){
            // set response code
            http_response_code(401);
            // show error message
            echo json_encode(array(
                "message" => "Access denied.",
                "error" => $e->getMessage()
            ));
        }
    } else{
        // set response code
        http_response_code(401);
        // tell the user access denied
        echo json_encode(array("message" => "Access denied."));
    }
?>