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
        header('Location: ../student/');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estudiante</title>
    <link rel="icon" href="../imgs/iconoprincipal.png" type="image/png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/header_footer.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="js/redieccionar.js"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="./css/estudiante.css">
    <link rel="stylesheet" href="./css/register.css">
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
                            <a class="nav-link" onclick="logout()">
                                Salir
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>
    <main>
        <div class="main-estudiante">
            <div class="container mt-5">
                <h2 class="mb-4">Estudiantes</h2>
                <input
                    type="text"
                    id="filterInput"
                    class="form-control mb-3"
                    placeholder="Buscar en la tabla..."
                >
                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#registerModal" onclick="reset_form()">Agregar Estudiante</button>
                <br>
                <br>
                <div class="alert alert-warning" role="alert">
                    <span style="font-weight: bold;">Nota:</span> Para ver más detalles de un estudiante o editarlo, haga clic en la fila estudiante.
                </div>
                <table class="table table-bordered table-striped">
                    <thead class="table-primary">
                        <tr>
                            <th>Boleta</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Status solicitud</th>
                            <th>Casillero Asignado</th>
                        </tr>
                    </thead>
                    <tbody id="tableBody">
                        
                    </tbody>
                </table>
                <br>
            </div>
            
            <!-- Modal Ver o Editar-->
            <div class="modal fade" id="infoModal" tabindex="-1" aria-labelledby="infoModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="infoModalLabel">Información del Estudiante</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form name="updateForm" id="updateForm" method="POST" enctype="multipart/form-data">
                                <input type="hidden" id="id_update" name="id_update">
                                <!-- Número de Locker -->
                                <div class="mb-3">
                                    <label for="numeroLocker" class="form-label">Número de locker (Asignar o Cambiar)</label>
                                    <input type="number" class="form-control input_completo" id="numeroLocker_update" name="numeroLocker_update" maxlength="10" readonly="true">
                                </div>
                                <!-- Nombres -->
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombres</label>
                                    <div>
                                        <div class="form-check form-check-inline medio_input">
                                            <input type="text" class="form-control" id="primer_nombre_update" name="primer_nombre_update" placeholder="Primer nombre" readonly>
                                        </div>
                                        <div class="form-check form-check-inline medio_input">
                                            <input type="text" class="form-control" id="segundo_nombre_update" name="segundo_nombre_update" placeholder="Segundo nombre" readonly>
                                        </div>
                                    </div>
                                </div>
                                <!-- Apellidos -->
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Apellidos</label>
                                    <div>
                                        <div class="form-check form-check-inline medio_input">
                                            <input type="text" class="form-control" id="primer_apellido_update" name="primer_apellido_update" placeholder="Primer Apellido" readonly>
                                        </div>
                                        <div class="form-check form-check-inline medio_input">
                                            <input type="text" class="form-control" id="segundo_apellido_update" name="segundo_apellido_update" placeholder="Segundo Apellido" readonly>
                                        </div>
                                    </div>
                                </div>
            
                                <!-- Teléfono -->
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Datos de Contacto</label>
                                    <div>
                                        <div class="form-check form-check-inline medio_input">
                                            <label for="telefono" class="form-label">Teléfono</label>
                                            <input type="number" class="form-control" id="telefono_update" name="telefono_update" placeholder="10 dígitos" maxlength="10" readonly>
                                        </div>
                                        <div class="form-check form-check-inline medio_input">
                                            <label for="correo" class="form-label">Correo Electrónico (institucional)</label>
                                            <input type="email" class="form-control" id="correo_update" name="correo_update" placeholder="usuario@alumno.ipn.mx" readonly>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="boleta" class="form-label">Número de Boleta</label>
                                    <input type="number" class="form-control input_completo" id="boleta_update" name="boleta_update" pattern="\d{10}" placeholder="10 dígitos" readonly>
                                </div>
                                <!-- Estatura -->
                                <div class="mb-3">
                                    <label for="estatura" class="form-label">¿Cuál es tu estatura? (en cm)</label>
                                    <input type="number" class="form-control input_completo" id="estatura_update" name="estatura_update" maxlength="3" readonly>
                                </div>

                                <!-- Archivos -->
                                <div id="editArchivos" style="display: None;">
                                    <div class="mb-3">
                                        <label for="credencial" class="form-label">Credencial del IPN vigente (PDF, Si no se quiere editar no subir nada)</label>
                                        <div class="input-group custom-file-button input_completo">
                                            <label class="input-group-text" for="credencial_update">
                                                <img src="../imgs/credencial.png" alt="pdf icon" class="pdf_icon">
                                                &nbsp;&nbsp;Selecciona tu Credencial
                                            </label>
                                            <input type="file" class="form-control" id="credencial_update" name="credencial_update" accept=".pdf" >
                                        </div>
                                    </div>
                
                                    <!-- Horario de clases -->
                                    <div class="mb-3">
                                        <label for="horario" class="form-label">Horario de clases (PDF, Si no se quiere editar no subir nada)</label>
                                        <div class="input-group custom-file-button input_completo">
                                            <input type="file" class="form-control" id="horario_update" name="horario_update" accept=".pdf" >
                                            <label class="input-group-text" for="horario_update">
                                                <img src="../imgs/horario.png" alt="pdf icon" class="pdf_icon">
                                                &nbsp;&nbsp;Selecciona tu Horario
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div id="verArchivos" style="display: flex;" class="botones_ver_doc">
                                    <button type="button" class="btn btn-outline-success" id="verCredencial">Ver credencial</button>
                                    <button type="button" class="btn btn-outline-info"  id="verHorario">Ver horario</button>
                                </div>

                                <div class="mb-3">
                                    <label for="curp" class="form-label">CURP</label>
                                    <input type="text" class="form-control input_completo" id="curp_update" name="curp_update" accept=".pdf" readonly>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-danger" onclick="delete_user()" >
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                    <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                </svg>
                                Eliminar
                            </button>
                            <button type="button" class="btn btn-primary" id="Editar_btn" onclick="editar_estudiante()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-square" viewBox="0 0 16 16">
                                <path d="M15.502 1.94a.5.5 0 0 1 0 .706L14.459 3.69l-2-2L13.502.646a.5.5 0 0 1 .707 0l1.293 1.293zm-1.75 2.456-2-2L4.939 9.21a.5.5 0 0 0-.121.196l-.805 2.414a.25.25 0 0 0 .316.316l2.414-.805a.5.5 0 0 0 .196-.12l6.813-6.814z"/>
                                <path fill-rule="evenodd" d="M1 13.5A1.5 1.5 0 0 0 2.5 15h11a1.5 1.5 0 0 0 1.5-1.5v-6a.5.5 0 0 0-1 0v6a.5.5 0 0 1-.5.5h-11a.5.5 0 0 1-.5-.5v-11a.5.5 0 0 1 .5-.5H9a.5.5 0 0 0 0-1H2.5A1.5 1.5 0 0 0 1 2.5z"/>
                            </svg>
                                Editar
                            </button>
                            <button type="button" class="btn btn-success" id="Save_btn" style="display: none;" onclick="validateForm_update()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-box-arrow-down" viewBox="0 0 16 16">
                                    <path fill-rule="evenodd" d="M3.5 10a.5.5 0 0 1-.5-.5v-8a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 .5.5v8a.5.5 0 0 1-.5.5h-2a.5.5 0 0 0 0 1h2A1.5 1.5 0 0 0 14 9.5v-8A1.5 1.5 0 0 0 12.5 0h-9A1.5 1.5 0 0 0 2 1.5v8A1.5 1.5 0 0 0 3.5 11h2a.5.5 0 0 0 0-1z"/>
                                    <path fill-rule="evenodd" d="M7.646 15.854a.5.5 0 0 0 .708 0l3-3a.5.5 0 0 0-.708-.708L8.5 14.293V5.5a.5.5 0 0 0-1 0v8.793l-2.146-2.147a.5.5 0 0 0-.708.708z"/>
                                </svg>
                                Guardar Cambios
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Agregar-->
            <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="extraLargeModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="infoModalLabel">Registra un Estudiante</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form name="registerForm" id="registerForm" method="POST" enctype="multipart/form-data">

                                <div class="mb-3">
                                    <label for="tipoSolicitud" class="form-label">Tipo de solicitud</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="tipoSolicitud" id="primeraVez" value="primeraVez" onchange="mostrarnucas(false)" checked>
                                            <label class="form-check-label" for="primeraVez">Primera vez</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="tipoSolicitud" id="renovacion" value="renovacion" onchange="mostrarnucas(true)">
                                            <label class="form-check-label" for="renovacion">Renovación</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Número de Locker -->
                                <div class="mb-3" id="lockerField" style="display: none;">
                                    <label for="numeroLocker" class="form-label">Número de locker asignado en el semestre anterior</label>
                                    <input type="number" class="form-control input_completo" id="numeroLocker" name="numeroLocker" maxlength="10" >
                                </div>
                                <!-- Nombres -->
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Nombres</label>
                                    <div>
                                        <div class="form-check form-check-inline medio_input">
                                            <input type="text" class="form-control" id="primer_nombre" name="primer_nombre" placeholder="Primer nombre" >
                                        </div>
                                        <div class="form-check form-check-inline medio_input">
                                            <input type="text" class="form-control" id="segundo_nombre" name="segundo_nombre" placeholder="Segundo nombre">
                                        </div>
                                    </div>
                                </div>
                                <!-- Apellidos -->
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Apellidos</label>
                                    <div>
                                        <div class="form-check form-check-inline medio_input">
                                            <input type="text" class="form-control" id="primer_apellido" name="primer_apellido" placeholder="Primer Apellido" >
                                        </div>
                                        <div class="form-check form-check-inline medio_input">
                                            <input type="text" class="form-control" id="segundo_apellido" name="segundo_apellido" placeholder="Segundo Apellido">
                                        </div>
                                    </div>
                                </div>
            
                                <!-- Teléfono -->
                                <div class="mb-3">
                                    <label for="nombre" class="form-label">Datos de Contacto</label>
                                    <div>
                                        <div class="form-check form-check-inline medio_input">
                                            <label for="telefono" class="form-label">Teléfono</label>
                                            <input type="number" class="form-control" id="telefono" name="telefono" placeholder="10 dígitos" maxlength="10">
                                        </div>
                                        <div class="form-check form-check-inline medio_input">
                                            <label for="correo" class="form-label">Correo Electrónico (institucional)</label>
                                            <input type="email" class="form-control" id="correo" name="correo" placeholder="usuario@alumno.ipn.mx">
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="boleta" class="form-label">Número de Boleta</label>
                                    <input type="number" class="form-control input_completo" id="boleta" name="boleta" pattern="\d{10}" placeholder="10 dígitos" >
                                </div>
                                <!-- Estatura -->
                                <div class="mb-3">
                                    <label for="estatura" class="form-label">¿Cuál es tu estatura? (en cm)</label>
                                    <input type="number" class="form-control input_completo" id="estatura" name="estatura" maxlength="3" >
                                </div>

                                <!-- Archivos -->
                                <div class="mb-3">
                                    <label for="credencial" class="form-label">Credencial del IPN vigente (PDF)</label>
                                    <div class="input-group custom-file-button input_completo">
                                        <label class="input-group-text" for="credencial">
                                            <img src="../imgs/credencial.png" alt="pdf icon" class="pdf_icon">
                                            &nbsp;&nbsp;Selecciona tu Credencial
                                        </label>
                                        <input type="file" class="form-control" id="credencial" name="credencial" accept=".pdf" >
                                    </div>
                                </div>
            
                                <!-- Horario de clases -->
                                <div class="mb-3">
                                    <label for="horario" class="form-label">Horario de clases (PDF)</label>
                                    <div class="input-group custom-file-button input_completo">
                                        <input type="file" class="form-control" id="horario" name="horario" accept=".pdf" >
                                        <label class="input-group-text" for="horario">
                                            <img src="../imgs/horario.png" alt="pdf icon" class="pdf_icon">
                                            &nbsp;&nbsp;Selecciona tu Horario
                                        </label>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="curp" class="form-label">CURP</label>
                                    <input type="text" class="form-control input_completo" id="curp" name="curp" accept=".pdf" >
                                </div>
                                
                                <!-- Captcha -->
                                <div class="d-flex justify-content-between">
                                    <div class="g-recaptcha" data-sitekey="6LdtuasqAAAAAGkmQC8GOTORwT9avOz7X9dwcyvR" style="margin: auto;"></div>
                                </div>
                            </form>
                            <div id="loader" name="loader">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                            <button type="button" class="btn btn-primary" onclick="validateForm()">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-floppy-fill" viewBox="0 0 16 16">
                                <path d="M0 1.5A1.5 1.5 0 0 1 1.5 0H3v5.5A1.5 1.5 0 0 0 4.5 7h7A1.5 1.5 0 0 0 13 5.5V0h.086a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5H14v-5.5A1.5 1.5 0 0 0 12.5 9h-9A1.5 1.5 0 0 0 2 10.5V16h-.5A1.5 1.5 0 0 1 0 14.5z"/>
                                <path d="M3 16h10v-5.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5zm9-16H4v5.5a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5zM9 1h2v4H9z"/>
                            </svg>
                                Crear
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="loader"></div>
        <script src="js/estudiante.js">
        </script>
        <script src="js/register_student.js">
        </script>
    </main>
    <br>
    <footer class="text-center text-lg-start">
        <div class="container p-4">
            <div class="row">
                <div class="col-lg-6 col-md-12 mb-4 mb-md-0">
                    <h5>Acerca del Proyecto</h5>
                    <p>Este sistema de gestión de casilleros está diseñado para mejorar la experiencia de los estudiantes en la ESCOM, facilitando la gestión de los casilleros en la universidad. Este proyecto se realizo para la materia de Tecnologías
 para el desarrollo web</p>
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