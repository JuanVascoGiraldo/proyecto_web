<?php

    require_once __DIR__ ."./../helpers/response_model.php";
    require_once __DIR__ ."./../repositories/user_repository.php";
    require_once __DIR__ ."./../repositories/student_repository.php";
    require_once __DIR__."/../helpers/verify_session.php";

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

        $id = $_POST['id'] ?? null;

        if (!$id) {
            $res = new Response_model(
                'No se ha proporcionado el id del estudiante',
                3,
                false
                );
            http_response_code(200);
            echo json_encode($res->toArray());
            exit();
        }

        if(!$user_repository->findById($id)){
            $res = new Response_model(
                'No se ha encontrado el estudiante',
                3,
                false
            );
            http_response_code(200);
            echo json_encode($res->toArray());
            exit();
        }

        $student_repository->delete_student_by_user_id($id);
        $user_repository->delete_user_by_id($id);

        $res = new Response_model(
            'Estudiante eliminado',
            3,
            true
            );
            http_response_code(200);
            echo json_encode($res->toArray());
    }else {
        http_response_code(405); // Método no permitido
        echo json_encode(["message" => "Método no permitido."], JSON_UNESCAPED_UNICODE);
    }


?>