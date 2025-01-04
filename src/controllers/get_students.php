<?php
    require_once __DIR__.'/../models/student.php';
    require_once __DIR__."/../models/user.php";
    require_once __DIR__."/../models/request_model.php";
    require_once __DIR__.'/../helpers/response_model.php';
    require_once __DIR__."/../helpers/verify_session.php";
    require_once __DIR__."/../repositories/student_repository.php";
    require_once __DIR__."/../repositories/user_repository.php";

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
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
        $user_repository = new UserRepository($database);
        $student_repository = new StudentRepository($database);
        $students = $user_repository->get_all_students();
        foreach ($students as $student_i) {
            $student_obj= $student_repository->find_student_by_user_id($student_i->getId());
            $student_obj->set_requests($student_repository->find_all_request_by_user_id($student_i->getId()));
            $student_i->set_student($student_obj);
        }
        $data_response = [];
        foreach ($students as $student_i) {
            $data_response[] = $student_i->toArray_to_admin();
        }
        $res = new Response_model(
            "Estudiantes",
            0,
            true,
            $data_response
        );
        http_response_code(200);
        echo json_encode($res->toArray());
    }else{
        $response = new Response_model(
            "Metodo no permitido",
            "1",
            false
        );
        http_response_code(405);
        echo json_encode($response->toArray());
    }
?>