<?php

    require_once __DIR__ ."./../helpers/gen_id.php";
    require_once __DIR__ ."./../helpers/code_generator.php";
    require_once __DIR__ ."./../helpers/response_model.php";
    require_once __DIR__ ."./../helpers/constants.php";
    require_once __DIR__ ."./../helpers/date_manage.php";
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
        
        // obtener los datos del formulario
        $id_user = $_POST['id_update'] ?? null;
        $primer_nombre = $_POST['primer_nombre_update'] ?? null;
        $segundo_nombre = $_POST['segundo_nombre_update'] ?? "";
        $primer_apellido = $_POST['primer_apellido_update'] ?? null;
        $segundo_apellido = $_POST['segundo_apellido_update'] ?? null;
        $telefono = $_POST['telefono_update'] ?? null;
        $boleta = $_POST['boleta_update'] ?? null;
        $altura = $_POST['estatura_update'] ?? null;
        $curp = $_POST['curp_update'] ?? null;
        $locker = $_POST['numeroLocker_update'] ?? "NA";
        $email = $_POST['correo_update'] ?? null;

        if (
            !$primer_nombre || !$primer_apellido ||
            !$segundo_apellido|| !$telefono || !$email ||
            !$boleta || !$altura || !$curp ||
            !$id_user
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
                    "password" => $password
                ]
            );
            http_response_code(200);
            echo json_encode($res->toArray(),JSON_UNESCAPED_UNICODE);
            exit;
        }

        $user = $user_repository->findByID($id_user);
        if(!$user){
            $res = new Response_model(
                "Usuario no encontrado",
                1,
                false,
            );
            http_response_code(200);
            echo json_encode($res->toArray(), JSON_UNESCAPED_UNICODE);
            exit;
        }
        $student_obj= $student_repository->find_student_by_user_id($user->getId());
        $student_obj->set_requests($student_repository->find_all_request_by_user_id($user->getId()));
        $user->set_student($student_obj);

        // Verificar si el email ya está registrado
        if($user->getEmail()!=$email && $user_repository->findByEmail($email)){
            $res = new Response_model(
                "El correo ya está registrado",
                1,
                false
            );
            http_response_code(200);
            echo json_encode($res->toArray(), JSON_UNESCAPED_UNICODE);
            exit;
        }

        if($user->get_student()->getBoleta() != $boleta && $student_repository->find_student_by_boleta($boleta)){
            $res = new Response_model(
                "La boleta ya está registrada",
                1,
                false
            );
            http_response_code(200);
            echo json_encode($res->toArray(), JSON_UNESCAPED_UNICODE);
            exit;
        }
        $locker = $locker == "" ? "NA" : $locker;
        //VERIFICAR SI EL CASILLERO YA ESTA ASIGNADO
        $exist = $student_repository->find_request_by_casillero_and_periodo($locker, DEFAULT_PERIODO);
        if( $locker != "NA" && $user->get_student()->get_requests()[0]-> getCasillero() != $locker &&
            $exist != null && !$exist->isDelayed()
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

        $uploadDir = __DIR__ . URL_UPLOAD;
        
        // Crear carpeta si no existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $credencialfileName = "";
        if(isset($_FILES['credencial_update']) && $_FILES['credencial_update']['size'] > 0){
            unlink($uploadDir . $student_obj->getCredencial_url());
            $credencialfileName = save_file($_FILES['credencial_update'], $uploadDir);
        }

        $horariofileName = "";
        if(isset($_FILES["horario_update"]) && $_FILES['horario_update']['size'] > 0){
            unlink($uploadDir . $horariofileName);
            $horariofileName = save_file($_FILES['horario_update'], $uploadDir);
        }
        if($exist){
            $exist->set_status(0);
            $exist->set_casillero(0);
            $student_repository->update_request($exist);
        }

        
        $user -> setEmail($email);
        $student_actualizado = $user->get_student();
        $student_actualizado->setBoleta($boleta);
        $student_actualizado->setTelefono($telefono);
        $student_actualizado->setCurp($curp);
        $student_actualizado->setFirstname($primer_nombre);
        $student_actualizado->setSecondname($segundo_nombre);
        $student_actualizado->setFirstsurname($primer_apellido);
        $student_actualizado->setSecondsurname($segundo_apellido);
        $student_actualizado->setHeight($altura);
        if($credencialfileName!=""){
            $student_actualizado->setcredencial_url($credencialfileName);
        }
        if($horariofileName!= ""){
            $student_actualizado->setHorario_url($horariofileName);
        }

        $is_asigned = false;
        $request_actualizada = $student_actualizado->get_requests()[0];
        if($request_actualizada->getCasillero() == 0 && $locker != "NA"){
            $request_actualizada->set_status(1);
            $request_actualizada->setUntilAt(addMinutesToDate(getCurrentUTC(), 1440));
            $is_asigned = true;
        }
        if($request_actualizada->getCasillero() != 0 && $locker == "NA"){
            $request_actualizada->set_status(0);
            $request_actualizada->setUntilAt(until: addMinutesToDate(getCurrentUTC(), 2880));
        }
        $locker = (int)$locker;
        $request_actualizada->set_casillero($locker);

        $user_repository->update_user($user);
        $student_repository->update_student($student_actualizado, $id_user);
        $student_repository->update_request($request_actualizada);

        $message = "Solicitud Actualizada correctamente";

        if($is_asigned){
            $email_service ->sendCasilleroAsignado_email(
                $user->getEmail(), $request_actualizada->getCasillero()
            );
        }

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