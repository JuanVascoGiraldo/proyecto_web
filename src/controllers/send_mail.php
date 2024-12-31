<?php
    require_once __DIR__ ."./../helpers/uuid.php";
    require_once __DIR__ ."./../helpers/code_generator.php";
    require_once __DIR__ ."./../helpers/response_model.php";
    require_once __DIR__ ."./../models/verification.php";
    require_once __DIR__ ."./../repositories/verification_repository.php";
    require_once __DIR__ ."./../services/send_mail_service.php";
    require_once __DIR__ ."./../services/database.php";

    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json; charset=UTF-8");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $database = new Database() ;
        $verification_repository = new VerificationRepository($database);
        $send_mail_service = new SendMailService();
        
        $data = json_decode(file_get_contents("php://input"), true);
    
        // Accede al dato del email
        $email = $data['email'];
        
        $exist_ve = $verification_repository->find_by_email($email);
        if($exist_ve){
            $response= new Response_model(
                "Hay un correo de verificacioón pendiente",
                "1",
                false
            );
            http_response_code(200);
            echo json_encode($response->toArray());
            exit();
        }

        $code = generateAlphanumericCode();
        $expiration_date = addMinutesToDate(getCurrentUTC(), 60);
        $id = generateUUIDv4();

        $verification = new Verification(
            $id, $code, $email,
            $expiration_date, 0
        );

        $send_mail_service->sendCodeVerification($email, $code);

        $save_ver = $verification_repository->add_verification($verification);
        if( $save_ver ){
            $response= new Response_model(
                "Se ha enviado un correo de verificación",
                "0",
                true
            );
            http_response_code(200);
            echo json_encode($response->toArray());
            exit();
        }

        $response = new Response_model(
            "Error",
            "1",
            true
        );

        http_response_code(200);
        echo json_encode($response->toArray());

    } else {
        http_response_code(405); // Método no permitido
        echo json_encode(["message" => "Método no permitido."]);
    }
?>