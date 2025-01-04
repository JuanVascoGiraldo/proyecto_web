<?php

    require_once __DIR__ ."./../helpers/gen_id.php";
    require_once __DIR__ ."./../helpers/code_generator.php";
    require_once __DIR__ ."./../helpers/response_model.php";
    require_once __DIR__ ."./../helpers/constants.php";
    require_once __DIR__ ."./../helpers/date_manage.php";
    require_once __DIR__ ."./../services/recapcha.php";
    require_once __DIR__ ."./../services/save_file.php";
    require_once __DIR__ ."./../repositories/user_repository.php";
    require_once __DIR__ ."./../repositories/student_repository.php";
    require_once __DIR__ ."./../models/user.php";
    require_once __DIR__ ."./../models/student.php";
    require_once __DIR__ ."./../models/request_model.php";
    require_once __DIR__."/../helpers/verify_session.php";
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
        $re_validator = new RecaptchaValidator();
        
        // obtener los datos del formulario
        $recapcha_token = $_POST['g-recaptcha-response'] ?? null;
        $primer_nombre = $_POST['primer_nombre'] ?? null;
        $segundo_nombre = $_POST['segundo_nombre'] ?? "";
        $primer_apellido = $_POST['primer_apellido'] ?? null;
        $segundo_apellido = $_POST['segundo_apellido'] ?? null;
        $telefono = $_POST['telefono'] ?? null;
        $boleta = $_POST['boleta'] ?? null;
        $altura = $_POST['estatura'] ?? null;
        $curp = $_POST['curp'] ?? null;
        $password = $_POST['password'] ?? null;
        $locker = $_POST['numeroLocker'] ?? "NA";
        $tipoSolicitud = $_POST['tipoSolicitud'] ?? null;
        $email = $_POST['correo'] ?? null;

        if (!isset($_FILES['credencial']) || $_FILES['credencial']['error'] != UPLOAD_ERR_OK) {
            $res = new Response_model(
                "No se ha cargado la credencial",
                1,
                false
            );
            http_response_code(200);
            echo json_encode($res->toArray(), JSON_UNESCAPED_UNICODE);
            exit;
        }

        if (!isset($_FILES['horario']) || $_FILES['horario']['error'] != UPLOAD_ERR_OK) {
            $res = new Response_model(
                "No se ha cargado el horario",
                1,
                false
            );
            http_response_code(200);
            echo json_encode($res->toArray(), JSON_UNESCAPED_UNICODE);
            exit;
        }
        if (
            !$primer_nombre || !$primer_apellido ||
            !$segundo_apellido|| !$telefono || !$email ||
            !$boleta || !$altura || !$curp || !$password ||
            !$tipoSolicitud
        ) {

            $res = new Response_model(
                "Todos los campos son obligatorios",
                1,
                false,
                [
                    "primer_nombre" => $primer_nombre,
                    "primer_apellido" => $primer_apellido,
                    "segundo_apellido" => $segundo_apellido,
                    "telefono" => $telefono,
                    "email" => $email,
                    "boleta" => $boleta,
                    "altura" => $altura,
                    "curp" => $curp,
                    "password" => $password,
                    "tipoSolicitud" => $tipoSolicitud,
                    "verification_code" => $verification_code
                ]
            );
            http_response_code(200);
            echo json_encode($res->toArray(),JSON_UNESCAPED_UNICODE);
            exit;
        }
        $is_renovacion = false;
        if($tipoSolicitud === "renovacion"){
            $is_renovacion = true;
        }
        
        // Verificar si el email ya está registrado
        if($user_repository->findByEmail($email)){
            $res = new Response_model(
                "El correo ya está registrado",
                1,
                false
            );
            http_response_code(200);
            echo json_encode($res->toArray(), JSON_UNESCAPED_UNICODE);
            exit;
        }

        if($student_repository->find_student_by_boleta($boleta)){
            $res = new Response_model(
                "La boleta ya está registrada",
                1,
                false
            );
            http_response_code(200);
            echo json_encode($res->toArray(), JSON_UNESCAPED_UNICODE);
            exit;
        }

        //VERIFICAR SI EL CASILLERO YA ESTA ASIGNADO
        if(
            $is_renovacion && 
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
        
        // Verificar si se envió el token de reCAPTCHA y la validacion
        if ($recapcha_token === null) {
            $res = new Response_model(
                "No se ha enviado el token de reCAPTCHA",
                1,
                false
            );
            http_response_code(200);
            echo json_encode($res, JSON_UNESCAPED_UNICODE);
            exit;
        }
        if(!$re_validator->validate($recapcha_token)){
            $res = new Response_model(
                "Token de reCAPTCHA inválido",
                2,                false
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
        
        // crear la url del archivo de la credencial
        $credencialfileName = save_file($_FILES['credencial'], $uploadDir);

        // crear la url del archivo del horario
        $horariofileName = save_file($_FILES['horario'], $uploadDir);

        $id_user = generateID(USER_PREFIX);
        
        $user = new User(
            $id_user, $boleta,
            password_hash($password, PASSWORD_DEFAULT),
            $email, 2, 1,
            getCurrentUTC(),
            getCurrentUTC()
        );
        $user_repository->save($user);
        $altura = (int)$altura;
        $student = new Student(
            $boleta, $telefono,
            $primer_nombre, $segundo_nombre,
            $primer_apellido, $segundo_apellido,
            $altura, $curp, $credencialfileName,
            $horariofileName
        );
        $student_repository->save_student_info($student, $id_user);

        $locker = (int)$locker;

        $request_casillero = new RequestModel(
            generateID(REQUEST_PREFIX),
            $locker,
            $is_renovacion ? 1 : 0,
            getCurrentUTC(),
            getCurrentUTC(),
            0,
            "",
            DEFAULT_PERIODO,
            addMinutesToDate(getCurrentUTC(), 2880)
        );
        $student_repository->save_request($request_casillero, $id_user);

        
        $message = "Solicitud guardada correctamente.";

        if($student_repository->how_request_by_periodo(DEFAULT_PERIODO) > MAX_REQUEST_BY_PERIOD){
            $message = "Cantidad de solicitudes Alcanzada";
        }

        $email_service ->confirm_register_email(
            $user->getEmail(), $is_renovacion, $request_casillero->getCasillero(), $request_casillero->getPeriodo()
        );
        $res = new Response_model(
            $message,
            4,
            true
            );
        http_response_code(200);
        echo json_encode($res->toArray(), JSON_UNESCAPED_UNICODE);
        exit;

    } else {
        http_response_code(405); // Método no permitido
        echo json_encode(["message" => "Método no permitido."], JSON_UNESCAPED_UNICODE);
    }

?>