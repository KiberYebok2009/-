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
            $user->firstname = !empty($_POST['firstname']) ? $_POST['firstname'] : $user->firstname;
            $user->lastname = !empty($_POST['lastname']) ? $_POST['lastname'] : $user->lastname;
    
            // Check if a new profile photo was uploaded
            if (isset($_FILES['profile_photo']) && is_uploaded_file($_FILES['profile_photo']['tmp_name'])) {
                $profile_photo = $_FILES['profile_photo'];
                $target_directory = "images/server/";
                $target_file = $target_directory . basename($profile_photo["name"]);
                $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $new_filename = $target_directory . uniqid() . '.' . $file_type;
            
                // Attempt to upload the file
                if (move_uploaded_file($profile_photo["tmp_name"], $new_filename)) {
                    // Set the new profile photo path
                    $user->profile_photo = 'api/' . $new_filename;
                } else {
                    // Set response code to 400 as the file upload failed
                    http_response_code(400);
                    // Show error message
                    echo json_encode(array("message" => "Unable to upload profile photo."));
                    exit();
                }
            }
                
            // update the user record
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
                       "profile_photo" => $user->profile_photo // Включите путь к изображению профиля
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