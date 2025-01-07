<?php
    require_once __DIR__ ."./../helpers/gen_id.php";
    require_once __DIR__ ."./../helpers/date_manage.php";
    require_once __DIR__ ."./../helpers/response_model.php";
    require_once __DIR__ ."./../repositories/student_repository.php";
    require_once __DIR__ ."./../models/request_model.php";
    require_once __DIR__ ."./../models/student.php";
    require_once __DIR__."/../helpers/verify_session.php";
    require_once __DIR__ ."./../services/save_file.php";
    require_once __DIR__ ."./../services/create_pdf.php";

    if($_SERVER['REQUEST_METHOD'] === 'GET'){
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
        $database = new Database();
        $student_repository = new StudentRepository($database);
        $lockers = $student_repository->get_all_active_request_by_periodo(DEFAULT_PERIODO);
        $data_response = [];
        foreach ($lockers as $locker) {
            $locker_array = $locker->toArrayAdmin();
            $locker_student = $student_repository->find_student_by_user_id($locker->get_user_id());
            if($locker_student === null){
                $res = new Response_model(
                    "Error",
                    1,
                    false
                );
                http_response_code(200);
                echo json_encode($res->toArray());
                exit();
            }
            $locker_array['student'] = [
                "id" => $locker->get_user_id(),
                "name" => $locker_student->getCompleteName(),
                "boleta" => $locker_student->getBoleta()
            ];
            $data_response[] = $locker_array;
        }

        $res = new Response_model(
            "Lockers",
            2,
            True,
            $data_response
        );
        http_response_code(200);
        echo json_encode($res->toArray());
    }else{
        http_response_code(405); // Método no permitido
        echo json_encode(["message" => "Método no permitido."], JSON_UNESCAPED_UNICODE);
    }
?>