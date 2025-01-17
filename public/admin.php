<?php
    require_once __DIR__."/../src/helpers/verify_session.php";
    session_start();
    if(isset($_SESSION['jwt'])){
        $user = verify_session($_SESSION['jwt']);
        if($user){
            if(!$user->is_admin()){
                header('Location: student/index.php');
            }else{
                header('Location: admin/index.php');
            }
        }else{
            session_destroy();
        }
    }
    
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión Administrador</title>
    <link rel="icon" href="imgs/iconoprincipal.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/header_footer.css">
    <link rel="stylesheet" href="css/acuse.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/redieccionar.js"></script>
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand d-flex" href="#">
                    <img src="imgs/ipn_logo.png" alt="logo Escom" title="logo ESCOM" class="d-inline-block"  onclick="window.open('https://www.ipn.mx', '_blank');">
                    <span class="d-flex">&nbsp;&nbsp;Sistema de Gestión de Casilleros ESCOM (SIGCA)&nbsp;&nbsp;</span>
                    <img src="imgs/escom_logo.png" alt="logo Escom" title="logo ESCOM" class="d-inline-block"  onclick="window.open('https://www.escom.ipn.mx', '_blank');">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" onclick="send_to_index()">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" onclick="send_to_register()">Registro</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" onclick="send_to_acuse()">Estudiante</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" onclick="send_to_admin()">Admin</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <section>
            <form class="form-signin" id="form_admin" name="form_admin" method="POST">
                <img class="mb-4" src="./imgs/administracion.png">
                <h2 class="mb-3">Inicia Sesión admin</h2>
                <div class="form-floating">
                    <input type="email" class="form-control" id="email" name="email" placeholder="Ingresa tu Email">
                    <label for="curp">Email</label>
                </div>
                <br>
                <div class="form-floating">
                    <input type="password" class="form-control" id="Password" name="Password" placeholder="Contraseña">
                    <label for="Password">Contraseña</label>
                </div>
                <br>
                <button class="btn btn-outline-light w-100 py-2" type="button" onclick="validar_formulario_admin()">Ingresar</button>
            </form>        
            <div id="loader">

            </div>    
        </section>
    </main>
    <footer class="text-center text-lg-start">
        <div class="container p-4">
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5>Acerca del Proyecto</h5>
                    <p>Este sistema de gestión de casilleros está diseñado para mejorar la experiencia de los estudiantes en la ESCOM, facilitando la gestión de los casilleros en la universidad. Este proyecto se realizo para la materia de Tecnologías
 para el Tecnologías para el desarrollo de aplicaciones Web</p>
                    <p>Desarrollado por: El equipo de SIGCA </p>
                </div>
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5>Contacto</h5>
                    <p>Si tienes alguna pregunta o inquietud, no dudes en ponerte en contacto con nosotros:</p>
                    <p>Email: sigca2024@gmail.com</p>
                    <p>Teléfono: +52 55 2523 6282</p>
                </div>
            </div>
        </div>
        <div class="text-center p-3" style="background-color: rgba(0, 0, 0, 0.05);">
            &#169;​ 2024 Equipo 2
        </div>
    </footer>
    <script src="js/admin.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>