<?php

    require_once __DIR__ ."./../helpers/response_model.php";
    require_once __DIR__ ."./../repositories/user_repository.php";
    require_once __DIR__ ."./../repositories/student_repository.php";
    require_once __DIR__."/../helpers/verify_session.php";
    require_once __DIR__ ."./../helpers/date_manage.php";
    require_once __DIR__ ."./../services/send_mail_service.php";



    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        if (!$user || !$user->is_admin()) {
            $res = new Response_model(
                "No autorizado",
                2,
                false
            );
            http_response_code(200);
            echo json_encode($res->toArray());
            exit();
        }
        // Inicializar servicios y repositorios
        $database = new Database();
        $user_repository = new UserRepository($database);
        $student_repository = new StudentRepository($database);
        $email_service = new SendMailService();

        $locker = $_POST['locker'] ?? null;
        if ($locker === null) {
            $res = new Response_model(
                'No se ha proporcionado el locker',
                3,
                false
            );
            http_response_code(200);
            echo json_encode($res->toArray());
            exit();
        }

        if(
            $student_repository->find_request_by_casillero_and_periodo($locker, DEFAULT_PERIODO)
        ){
            $res = new Response_model(
                "El casillero ya está asignado, si no es así, contacte a soporte",
                1,
                false
            );
            http_response_code(200);
            echo json_encode($res->toArray(),JSON_UNESCAPED_UNICODE);
            exit;
        }

        $requests = $student_repository->get_all_pending_reques_by_periodo(DEFAULT_PERIODO);
        if(count($requests) === 0){
            $res = new Response_model(
                "No hay solicitudes pendientes",
                3,
                false
                );
            http_response_code(200);
            echo json_encode($res->toArray(),JSON_UNESCAPED_UNICODE);
            exit();
        }
        $locker = (int) $locker;
        $asign_request = null;
        foreach ($requests as $request) {
            $student = $student_repository->find_student_by_user_id($request->get_user_id());
            if($student === null){
                $res = new Response_model(
                    "Error",
                    1,
                    false
                );
                http_response_code(200);
                echo json_encode($res->toArray(),JSON_UNESCAPED_UNICODE);
                exit();
            }
            if($locker <= 50 && $student->getHeight() <= 160){
                $asign_request = $request;
                break;
            }

            if($locker>50){
                $asign_request = $request;
                break;
            }
        }
        if($asign_request === null){
            $asign_request = $requests[0];
        }

        $asign_request->set_casillero($locker);
        $asign_request->set_status(1);
        $asign_request->setUntilAt(addMinutesToDate(getCurrentUTC(), 1440));
        $student_repository->update_request($asign_request);
        $user = $user_repository->findByID($asign_request->get_user_id());
        $email_service ->sendCasilleroAsignado_email(
            $user->getEmail(), $locker
        );
        $res = new Response_model(
            "Casillero asignado",
            2,
            true
        );
        http_response_code(200);
        echo json_encode($res->toArray(),JSON_UNESCAPED_UNICODE);
        
    }else {
        http_response_code(405); // Método no permitido
        echo json_encode(["message" => "Método no permitido."], JSON_UNESCAPED_UNICODE);
    }


?>