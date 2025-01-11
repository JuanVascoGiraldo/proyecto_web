<?php
    require_once __DIR__ ."./../helpers/gen_id.php";
    require_once __DIR__ ."./../helpers/code_generator.php";
    require_once __DIR__ ."./../helpers/response_model.php";
    require_once __DIR__ ."./../helpers/constants.php";
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
        
        $verification = $verification_repository->find_by_email($email);
        $message = "";
        $new = false;
        if(!$verification){
            $code = generateAlphanumericCode();
            $expiration_date = addMinutesToDate(getCurrentUTC(), 60);
            $id = generateID(VERIFICATION_PREFIX);

            $verification = new Verification(
                $id, $code, $email,
                $expiration_date, 0
            );
            $message = "Se ha enviado un correo de verificación, revisa tu bandeja de entrada o spam en tu correo institucional";
            $new = true;
        }
        else{
            $message = "Se ha reenviado un correo de verificación, revisa tu bandeja de entrada o spam en tu correo institucional";
        }

        
        $send_mail_service->sendCodeVerification($email, $verification->getCode());
        if($new){
            $verification_repository->add_verification($verification);
        }

        $response= new Response_model(
            $message,
            "0",
            true
        );

        http_response_code(200);
        echo json_encode($response->toArray());

    } else {
        http_response_code(405); // Método no permitido
        echo json_encode(["message" => "Método no permitido."]);
    }
?>