<?php
    require_once __DIR__."/../../src/helpers/verify_session.php";
    session_start();
    if(!isset($_SESSION['jwt'])){
        header('Location: ../index.html');
    }
    $user = verify_session($_SESSION['jwt']);
    if(!$user){
        session_destroy();
        header('Location: ../index.html');
    }
    if(!$user->is_admin()){
        header('Location: ../index.html');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casilleros</title>
    <link rel="icon" href="../imgs/iconoprincipal.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="css/casillero.css">
    <link rel="stylesheet" href="../css/header_footer.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/redieccionar.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg">
            <div class="container-fluid">
                <a class="navbar-brand d-flex" href="#">
                    <img src="../imgs/ipn_logo.png" alt="logo Escom" title="logo ESCOM" class="d-inline-block"  onclick="location.href='https://www.ipn.mx';">
                    <span class="d-flex">&nbsp;&nbsp;Sistema de Gestión de Casilleros ESCOM (SIGCA)&nbsp;&nbsp;</span>
                    <img src="../imgs/escom_logo.png" alt="logo Escom" title="logo ESCOM" class="d-inline-block"  onclick="location.href='https://www.escom.ipn.mx';">
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
                            <a class="nav-link" aria-current="page" onclick="send_to_estudiante()">Estudiantes</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" onclick="send_to_casillero()">Casilleros</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" onclick="logout()">Salir</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <br>
        <div class="main-casillero">
            <div class="container text-center mt-4">
                <h1 class="mb-4">Casilleros</h1>
                <div class="color-legend">
                    <div class="colors">
                        <span
                            style="
                                display: inline-block;
                                background-color: red;
                                width: 10px;
                                height: 10px;
                                border-radius: 50%;
                                margin-right: 5px;
                            "
                        ></span>
                        Vencido
                    </div>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="colors">
                        <span
                            style="
                                display: inline-block;
                                background-color: rgb(251, 255, 1);
                                width: 10px;
                                height: 10px;
                                border-radius: 50%;
                                margin-right: 5px;
                            "
                        ></span>
                        Pendiente
                    </div>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="colors">
                        <span
                            style="
                                display: inline-block;
                                background-color: rgb(18, 1, 255);
                                width: 10px;
                                height: 10px;
                                border-radius: 50%;
                                margin-right: 5px;
                            "
                        ></span>
                        Disponible
                    </div>
                    &nbsp;&nbsp;&nbsp;&nbsp;
                    <div class="colors">
                        <span
                            style="
                                display: inline-block;
                                background-color: gray;
                                width: 10px;
                                height: 10px;
                                border-radius: 50%;
                                margin-right: 5px;
                            "
                        ></span>
                        Ocupado
                    </div>
                </div>
                
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item"><a class="page-link" href="#" onclick="showPage(1)">1</a></li>
                        <li class="page-item"><a class="page-link" href="#" onclick="showPage(2)">2</a></li>
                        <li class="page-item"><a class="page-link" href="#" onclick="showPage(3)">3</a></li>
                        <li class="page-item"><a class="page-link" href="#" onclick="showPage(4)">4</a></li>
                    </ul>
                </nav>
                <div id="lockers-container" style="margin: auto; width: 95%;"></div>
                
                <!-- Paginación -->
                <nav class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item"><a class="page-link" href="#" onclick="showPage(1)">1</a></li>
                        <li class="page-item"><a class="page-link" href="#" onclick="showPage(2)">2</a></li>
                        <li class="page-item"><a class="page-link" href="#" onclick="showPage(3)">3</a></li>
                        <li class="page-item"><a class="page-link" href="#" onclick="showPage(4)">4</a></li>
                    </ul>
                </nav>
            </div>
        </div>
        <!-- Modal -->
        <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="infoModalLabel">Información del Casillero</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div id="casillero">
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Quitar</button>
                        <button type="button" class="btn btn-primary">Asignar</button>
                    </div>
                </div>
            </div>
        </div>
        <script src="js/casillero.js"></script>
    </main>
    <footer class="text-center text-lg-start">
        <div class="container p-4">
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5>Acerca del Proyecto</h5>
                    <p>Este sistema de gestión de casilleros está diseñado para mejorar la experiencia de los estudiantes en la ESCOM, facilitando la gestión de los casilleros en la universidad. Este proyecto se realizo para la materia de Tecnologias para el desarrollo web</p>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>