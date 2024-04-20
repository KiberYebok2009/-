<?php
    // required headers
    header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/");
    header("Content-Type: application/json; charset=UTF-8");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Max-Age: 3600");
    header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
    
    // files needed for decoding jwt
    require "../../libs/vendor/autoload.php";
    include_once '../../config/core.php';
    include_once '../../libs/vendor/firebase/php-jwt/src/BeforeValidException.php';
    include_once '../../libs/vendor/firebase/php-jwt/src/ExpiredException.php';
    include_once '../../libs/vendor/firebase/php-jwt/src/SignatureInvalidException.php';
    include_once '../../libs/vendor/firebase/php-jwt/src/JWT.php';
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    
    // get posted data
    $data = json_decode(file_get_contents("php://input"));
    
    // get jwt
    $jwt = isset($data->jwt) ? $data->jwt : "";
    
    // if jwt is not empty
    if ($jwt) {
        try {
            // decode jwt
            $key = "9970";
            $decoded = JWT::decode($jwt, new Key($key, 'HS256'));
    
            // set response code
            http_response_code(200);
    
            // show user details (assuming $decoded->data contains the user data)
            echo json_encode(array(
                "message" => "Access granted.",
                "data" => array(
                    "email" => $decoded->data->email,
                    "firstname" => $decoded->data->firstname,
                    "lastname" => $decoded->data->lastname,
                    "role" => $decoded->data->role,
                    "profile_photo" => $decoded->data->profile_photo
                    
                   
                )
            ));
        } catch (Exception $e) {
            // set response code
            http_response_code(401);
    
            // tell the user access denied & show error message
            echo json_encode(array(
                "message" => "Access denied.",
                "error" => $e->getMessage()
            ));
        }
    } else {
        // set response code
        http_response_code(401);
    
        // tell the user access denied
        echo json_encode(array("message" => "Access denied."));
    }
?>
    
    
    
    
    
    
    
    