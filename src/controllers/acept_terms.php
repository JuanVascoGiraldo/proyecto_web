<?php

    require_once __DIR__ . '/../models/user.php';

    require_once __DIR__ ."./../helpers/response_model.php";
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
        // Inicializar servicios y repositorios
        $database = new Database();
        $student_repository = new StudentRepository($database);

        $request_id = $_POST['request_id'] ?? null;

        if (!$request_id) {
            $res = new Response_model(
                'No se ha proporcionado el id de la solicitud',
                3,
                false
            );
            http_response_code(200);
            echo json_encode($res->toArray());
            exit();
        }
        $student_repository->acept_terms_and_conditions($user->getId(), $request_id);
        $res = new Response_model(
            'Solicitud aceptada',
            0,
            true
        );
        http_response_code(200);
        echo json_encode($res->toArray());

    }else {
        http_response_code(405); // Método no permitido
        echo json_encode(["message" => "Método no permitido."], JSON_UNESCAPED_UNICODE);
    }

?>