<?php
    require_once __DIR__.'constants.php';
    require_once __DIR__.'../services/database.php';
    require_once __DIR__.'../models/user.php';
    require_once __DIR__.'../repositories/user_repository.php';
    require_once __DIR__.'/jwt.php';

    function verify_session($jwt): ?User{
        try{
            $database = new Database();
            $user_repository = new UserRepository($database);
            $data = getJWTData($jwt);
            return $user_repository->find_user_by_session_id($data['id']);
        }catch(Exception $e){
            error_log($e->getMessage());
            return null;
        }
    }

?>