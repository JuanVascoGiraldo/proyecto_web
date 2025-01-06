<?php
    require_once __DIR__."/../../src/helpers/verify_session.php";
    require_once __DIR__."/../../src/helpers/date_manage.php";
    require_once __DIR__."/../../src/repositories/student_repository.php";

    session_start();
    if(!isset($_SESSION['jwt'])){
        header('Location: ../index.html');
    }
    $user = verify_session($_SESSION['jwt']);
    if(!$user){
        session_destroy();
        header('Location: ../index.html');
    }
    if($user->is_admin()){
        header('Location: ../admin/');
    }
    $database = new Database();
    $student_repository = new StudentRepository($database);
    $student = $student_repository->find_student_by_user_id($user->getId());
    $requests = $student_repository->find_all_request_by_user_id($user->getId());
    $ultimate_request = $requests[0];
    $is_delayed = false;
    if($ultimate_request->getStatus() == 1 && isMoreThan24Hours($ultimate_request->getUntilAt())){
        $is_delayed = true;
    }
    $status = "";
    switch($ultimate_request->getStatus()){
        case 0:
            $status = "<span class='pendiente'>Pendiente</span>";
            break;
        case 1:
            if($is_delayed){
                $status = "<span class='rechazado'>Vencida</span>";
            }else{
                $status = "<span class='aprobado'>Aceptado</span>";
            }
            break;
        case 2:
            $status = "<span class='terminado'>Terminada</span>";
            break;
        case 3:
            $status = "<span class='rechazado'>Rechazada</span>";
            break;
        default:
            $status = "<span class='rechazado'>Rechazada</span>";
            break;
    }
    $can_upload_payment = false;
    if($ultimate_request->getStatus() == 1 && !$is_delayed && $ultimate_request->getUrlPaymentDocument()==""){
        $can_upload_payment = true;
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio Administrador</title>
    <link rel="icon" href="../imgs/iconoprincipal.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/header_footer.css">
    <link rel="stylesheet" href="./css/estudiante.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                            <a class="nav-link" onclick="location.reload()">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" onclick=" window.location.href = 'http://localhost/Proyecto_Final/src/controllers/logout.php';">Salir</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <div class="container" style="background-color: white; width: 70%;">
            <div class="row">
                <div class="col-12">
                    <h1 class="text-center">Bienvenido Estudiante</h1>
                </div>
            </div>
            <?php 
                if($is_delayed){
                    ?>
                    <div class="alert alert-danger" role="alert">
                        <h4 class="alert-heading">¡Tu solicitud ha vencido!</h4>
                        <p>Tu casillero ya ha sido reasignado. Escribe al soporte para saber si se te puede asignar uno nuevo</p>
                    </div>
                    <?php
                }
            ?>
            <div class="row"  style="display: flex; justify-content: center;">
                <div class="card" style="width: 18rem;">
                    <img src="./imgs/casilleros.png" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title">Estatus de Solicitud</h5>
                        <p class="card-text"><?php echo $status; ?></p>
                    </div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Dia solicitado: <?php echo $ultimate_request->getCreatedAt()->format("Y-m-d H:i:s") ; ?></li>
                        <li class="list-group-item">Casillero Asignado: <?php echo $ultimate_request->getCasillero() == 0 ? "---": $ultimate_request->getCasillero();?> </li>
                        <li class="list-group-item">Perido Escolar: <?php echo $ultimate_request->getPeriodo() ?></li>
                    </ul>
                    <div class="card-body">
                        <?php
                            if($can_upload_payment){
                                ?>
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#comprobanteModal">Subir Comprobante</button>
                        <?php
                            }
                        ?>
                        <?php
                            if($ultimate_request->getStatus() == 2){
                        ?>
                                <a href="http://localhost/Proyecto_Final/src/acuses/<?php echo $ultimate_request->getUrlAcuse(); ?>" download="<?php echo $student->getBoleta()?>.pdf" class="btn btn-success">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cloud-download-fill" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M8 0a5.53 5.53 0 0 0-3.594 1.342c-.766.66-1.321 1.52-1.464 2.383C1.266 4.095 0 5.555 0 7.318 0 9.366 1.708 11 3.781 11H7.5V5.5a.5.5 0 0 1 1 0V11h4.188C14.502 11 16 9.57 16 7.773c0-1.636-1.242-2.969-2.834-3.194C12.923 1.999 10.69 0 8 0m-.354 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V11h-1v3.293l-2.146-2.147a.5.5 0 0 0-.708.708z"></path></svg>
                                    Descargar Acuse
                                </a>
                        <?php
                            }
                        ?>
                    </div>
                </div>
            </div>
            <br>
        </div>
        <br>
        <div id="loader"></div>
        <!-- Modal aceptar terminos -->
        <?php
            if(!$ultimate_request->getIsAcepted()){
                ?>
        <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="infoModalLabel">Acuerdo de Responsabilidades en el uso del casillero</h5>
                    </div>
                    <div class="modal-body">
                        <form>
                            <!--Mostrar pdf-->
                            <div>
                                <iframe id="pdf" src="./pdf/conformidadUsoLocker_25_1.pdf" style="width: 100%; height: 500px;"></iframe>
                            </div>
                            <div>
                                <label for="aceptar">Estoy Conforme Para el Uso de Locker</label>
                                <input type="checkbox" id="aceptar" name="aceptar" value="aceptar">
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" id="Save_btn" onclick="acept_term('<?php echo $ultimate_request->getId(); ?>');">Seguir</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
            }
        ?>

        <!-- Modal subir comprobante -->
        <?php
            if($can_upload_payment){
                ?>
        <div class="modal fade" id="comprobanteModal" tabindex="-1" aria-labelledby="comprobanteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="infoModalLabel">Sube tu comprobante de pago</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="form_payment" name="form_payment">
                            <div class="mb-3">
                                <label for="horario" class="form-label">Sube tu comprobante de Pago (PDF)</label>
                                <div class="input-group custom-file-button input_completo">
                                    <input type="file" class="form-control" id="comprobante" name="comprobante" accept=".pdf" >
                                    <label class="input-group-text" for="comprobante">
                                        <img src="./imgs/comprobado.png" alt="pdf icon" class="pdf_icon">
                                        &nbsp;&nbsp;Selecciona tu Comprobante
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary" id="Editar_btn" onclick="validate_send_payment_url()">Subir</button>
                    </div>
                </div>
            </div>
        </div>
        <?php
            }
        ?>
        
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <script src="./js/estudiante.js"> </script>
        <script>
            function openModal() {
                const modalElement = document.getElementById('infoModal');
                const modalInstance = new bootstrap.Modal(modalElement);
                modalInstance.show();
            }

            <?php
                if(!$ultimate_request->getIsAcepted()){
                    echo "openModal();";
                }
            ?>
        </script>
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
</body>
</html>