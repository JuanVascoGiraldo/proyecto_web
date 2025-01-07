let lockers_BD = {};
let loading = "<script>"+
    "Swal.fire({"+
            "title: 'Recuperando Datos',"+
            "html: 'Espera un momento ...',"+
            "timer: 500,"+
            "allowOutsideClick: false,"+
            "allowEscapeKey: false,"+
            "timerProgressBar: true,"+
            "didOpen: () => {"+
            "Swal.showLoading()"+
            "}"+
        "})"+
    "</script>";

let loading2 = "<script>"+
    "Swal.fire({"+
            "title: 'Recuperando Datos',"+
            "html: 'Espera un momento ...',"+
            "timer: 10000,"+
            "allowOutsideClick: false,"+
            "allowEscapeKey: false,"+
            "timerProgressBar: true,"+
            "didOpen: () => {"+
            "Swal.showLoading()"+
            "}"+
        "})"+
    "</script>";


$(document).ready(function() {
    const url = 'http://localhost/Proyecto_Final/src/controllers/get_all_lockers.php';
    $("#loader").html(loading);
    $.ajax({
        url: url,
        type: "GET",
        success: (response) => {
            const lockers = JSON.parse(response);
            $("#loader").html("");
            if(!lockers.success){
                Swal.fire({
                    icon: "error",
                    title: "No se pudo obtener la información",
                    text: lockers.message
                });
                return;
            }
            lockers.data.forEach((locker) => {
                lockers_BD[locker.casillero.toString()] = locker;
            });
            showPage(1);
        },
        error: () => {
            $("#loader").html("");
            Swal.fire({
                icon: "error",
                title: "No se pudo obtener la información",
                text: "Inténtalo de nuevo o contacta a soporte",
            });
        }
    });

});

let estado = {
    "0": "<span class='pendiente_span'>Pendiente</span>",
    "1": "<span class='aprobado_span'>Aceptado</span>",
    "2": '<span class="terminado_span">Terminada</span>',
    "3": '<span class="rechazado_span">Rechazada</span>',
    "4": '<span class="rechazado_span">Vencida</span>',
}

// Generar casilleros (96 en total)
const lockers = Array.from({ length:100 }, (_, i) => `${i + 1}`);

const lockersPerPage = 25; // 6x4 por página
const container = document.getElementById('lockers-container');

function showPage(pageNumber) {
    container.innerHTML = ''; // Limpiar contenido
    const start = (pageNumber - 1) * lockersPerPage;
    const end = start + lockersPerPage;

  // Mostrar los casilleros correspondientes a la página
    lockers.slice(start, end).forEach((locker, i) => {
        const div = document.createElement('div');
        let estado = 'disponible';
        let locker_BD = lockers_BD[locker];
        if(locker_BD){
            if(locker_BD.is_delayed){
                estado = 'vencido';
            }
            if(locker_BD.status === 1  && !locker_BD.is_delayed){
                estado = 'pendiente';
            }
            if(locker_BD.status === 2){
                estado = 'ocupado';
            }
        }

        // if ((i+1) % 3 === 0) div.className = "locker vencido";
        // else if ((i+1) % 2 === 0) div.className = "locker ocupado";
        // else if ((i+1) % 7 === 0) div.className = "locker pendiente";
        
        div.className = "locker " + estado;
        div.textContent = locker;
        div.setAttribute("data-bs-toggle", "modal");
        div.setAttribute("data-bs-target", "#infoModal");
        div.onclick = mostar.bind(null, locker);
        container.appendChild(div);
    });
}

// Mostrar la primera página al cargar

function asignar_casillero(casillero){
    const url = 'http://localhost/Proyecto_Final/src/controllers/asign_locker.php';
            let formData = new FormData();
            formData.append('locker', casillero);
            $("#loader").html(loading2);
            $.ajax({
                url: url,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: (response) => {
                    if(!response.success){
                        $("#loader").html("");
                        Swal.fire({
                            icon: "error",
                            title: "No se pudo enviar la información",
                            text: response.message
                        });
                    }else{
                            location.reload();
                    }
                },
                error: () => {
                    $("#loader").html("");
                    Swal.fire({
                        icon: "error",
                        title: "No se pudo enviar la información",
                        text: "Inténtalo de nuevo o contacta a soporte",
                    });
                }
            });
}

function mostar(i) {
    document.getElementById('infoModalLabel').innerHTML = `Informacion del Casillero ${i}`;
    const button_a = document.getElementById('button_asignar');
    if (!lockers_BD[i]) {
        button_a.style.display = 'block';
        button_a.onclick = asignar_casillero.bind(null, i);
        document.getElementById('casillero').innerHTML =`
            <p>Estado: Disponible</p>
            <p>Fecha de vencimiento: N/A</p>
            <p>Boleta: N/A</p>
            <p>Nombre: N/A</p>
        `
        return;
    }
    button_a.style.display = 'none';
    let locker_BD = lockers_BD[i];
    document.getElementById('casillero').innerHTML =`
        <p>Estado: ${locker_BD.is_delayed ? estado["4"] : estado[locker_BD.status.toString()]}</p>
        <p>Fecha de vencimiento: ${locker_BD.status === 1 ? locker_BD.until_at : 'N/A'}</p>
        <p>Boleta: ${ locker_BD.student.boleta}</p>
        <p>Nombre: ${ locker_BD.student.name}</p>
        <a href="./estudiantes.php?search=${locker_BD.student.boleta}" class="btn btn-primary" target="_blank">Ver Estudiante</a>
    `
}