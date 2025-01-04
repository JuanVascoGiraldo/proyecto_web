<?php

    require_once __DIR__ ."./../helpers/gen_id.php";
    require_once __DIR__ ."./../helpers/response_model.php";
    require_once __DIR__ ."./../helpers/constants.php";
    require_once __DIR__ ."./../models/session.php";
    require_once __DIR__ ."./../models/user.php";
    require_once __DIR__ ."./../repositories/user_repository.php";
    require_once __DIR__ ."./../helpers/jwt.php";

    header('Content-Type: application/json');


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $boleta = $_POST['boleta'] ?? null;
        $password = $_POST['password'] ?? null;

        if ($boleta === null || $password === null) {
            $response = new Response_model(
                "Faltan datos",
                "1",
                false
            );
            http_response_code(200);
            echo json_encode($response->toArray());
            exit();
        }

        $database = new Database();
        $user_repository = new UserRepository($database);
        $user = $user_repository->findByUsername($boleta);
        if ($user === null) {
            $response = new Response_model(
                "Usuario no encontrado",
                "1",
                false
            );
            http_response_code(200);
            echo json_encode($response->toArray());
            exit();
        }
        if(!$user->checkPassword($password) || $user->is_admin()){
            $response = new Response_model(
                "Contraseña incorrecta",
                "1",
                false
            );
            http_response_code(200);
            echo json_encode($response->toArray());
            exit();
        }

        $new_session = new Session(
            generateID(SESSION_PREFIX), getCurrentUTC(), addMinutesToDate(getCurrentUTC(), 60)
        );

        $user->add_Session($new_session);
        $user_repository->save_sessions($user, $new_session);
        $user_repository->update_all_user_sessions($user);
        $data = [
            "id" => $user->getId(),
        ];
        $jwt = generateJWT($data);
        session_start();
        $_SESSION['jwt'] = $jwt;
        $response = new Response_model(
            "Sesión iniciada",
            "0",
            true
        );

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