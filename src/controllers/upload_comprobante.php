<?php

    require_once __DIR__ ."./../helpers/gen_id.php";
    require_once __DIR__ ."./../helpers/date_manage.php";
    require_once __DIR__ ."./../helpers/response_model.php";
    require_once __DIR__ ."./../repositories/student_repository.php";
    require_once __DIR__ ."./../models/request_model.php";
    require_once __DIR__."/../helpers/verify_session.php";
    require_once __DIR__ ."./../services/save_file.php";
    require_once __DIR__ ."./../services/create_pdf.php";

    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        session_start();
        if (!isset($_SESSION['jwt'])) {
            $res = new Response_model(
                "No autorizado",
                4,
                false
            );
            http_response_code(200);
            echo json_encode($res->toArray());
            exit();
        }
        $user = verify_session($_SESSION['jwt']);
        if (!$user || $user->is_admin()) {
            $res = new Response_model(
                "No autorizado",
                2,
                false
            );
            http_response_code(200);
            echo json_encode($res->toArray());
            exit();
        }
        $database = new Database();
        $student_repository = new StudentRepository($database);
        
        if (!isset($_FILES['comprobante']) || $_FILES['comprobante']['error'] != UPLOAD_ERR_OK) {
            $res = new Response_model(
                "No se ha cargado la credencial",
                1,
                false
            );
            http_response_code(200);
            echo json_encode($res->toArray(), JSON_UNESCAPED_UNICODE);
            exit;
        }
        $uploadDir = __DIR__ . URL_UPLOAD;
        
        // Crear carpeta si no existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $student = $student_repository->find_student_by_user_id($user->getId());
        $requests = $student_repository->find_all_request_by_user_id($user->getId());
        $request = $requests[0];

        if($request->getStatus() != 1) {
            $res = new Response_model(
                "No se puede subir el comprobante",
                1,
                false
            );
            http_response_code(200);
            echo json_encode($res->toArray(), JSON_UNESCAPED_UNICODE);
            exit();
        }
        $comprobanteFile_name = save_file($_FILES['comprobante'], $uploadDir);
        if($request->getUrlPaymentDocument()!= ""){
            unlink($uploadDir.$request->getUrlPaymentDocument());
        }
        $request->set_url_payment_document($comprobanteFile_name);
        
        $acuserDir = __DIR__ . URL_ACUSES;
        if (!is_dir($acuserDir)) {
            mkdir($acuserDir, 0755, true);
        }
        
        $acuse_id = generateID(FILE_PREFIX);
        generarAcuse($acuserDir.$acuse_id.".pdf", $student->getBoleta(),$student->getCompleteName(), $request->getCasillero());
        $request->setUrlAcuse($acuse_id.".pdf");
        $request->set_status(2);

        $student_repository->update_request($request);

        $res = new Response_model(
            "Comprobante subido",
            0,
            true
        );
        http_response_code(200);
        echo json_encode($res->toArray());
    }else{
        http_response_code(405); // Método no permitido
        echo json_encode(["message" => "Método no permitido."], JSON_UNESCAPED_UNICODE);
    }

?>