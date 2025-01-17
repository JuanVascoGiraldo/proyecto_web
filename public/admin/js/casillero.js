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
    const url = '../../src/controllers/get_all_lockers.php';
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
function answer_is_force(casillero) {
    if(casillero > 50){
        return asignar_casillero(casillero, 1);
    }
    Swal.fire({
        title: '¿Quieres asignarlo?',
        text: "¿Quieres asignar el casillero aunque la persona mida menos de 1.60?",
        icon: 'warning',
        showDenyButton: true,
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Si Asignar',
        cancelButtonText: 'Cancelar',
        denyButtonText: 'No Asignar'
    }).then((result) => {
        if (result.isConfirmed) {
            asignar_casillero(casillero, 1);
        }
        if (result.isDenied) {
            asignar_casillero(casillero, 0);
        }
    });
}

function asignar_casillero(casillero, force){
    const url = '../../src/controllers/asign_locker.php';
    let formData = new FormData();
    formData.append('locker', casillero);
    formData.append('force', force);
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
    if (!lockers_BD[i] ) {
        button_a.style.display = 'block';
        button_a.onclick = answer_is_force.bind(null, i);
        document.getElementById('casillero').innerHTML =`
            <p>Estado: Disponible</p>
            <p>Fecha de vencimiento: N/A</p>
            <p>Boleta: N/A</p>
            <p>Nombre: N/A</p>
        `
        return;
    }
    if(lockers_BD[i].is_delayed){
        button_a.style.display = 'block';
        button_a.onclick =answer_is_force.bind(null, i);
    }else{
        button_a.style.display = 'none';
    }


    let locker_BD = lockers_BD[i];
    document.getElementById('casillero').innerHTML =`
        <p>Estado: ${locker_BD.is_delayed ? estado["4"] : estado[locker_BD.status.toString()]}</p>
        <p>Fecha de vencimiento: ${locker_BD.status === 1 ? locker_BD.until_at : 'N/A'}</p>
        <p>Boleta: ${ locker_BD.student.boleta}</p>
        <p>Nombre: ${ locker_BD.student.name}</p>
        <a href="./estudiantes.php?search=${locker_BD.student.boleta}" class="btn btn-primary" target="_blank">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0"/>
            </svg>
            Ver Estudiante
        </a>
    `
}