
const regex_names = /^[a-zA-ZÀ-ÿ\s]{1,40}$/;
const regex_names_without_spaces = /^[a-zA-ZÀ-ÿ]{1,40}$/;
const regex_email = /^[a-zA-Z0-9_.+-]+@alumno\.ipn\.mx$/;
const regex_space = /\s/;
const regex_phone = /^[0-9]{10}$/;
const regex_num_locker = /^[0-9]{1,3}$/;
const regex_height = /^[0-9]{1,3}$/;
const regex_curp = /^[A-Z]{4}[0-9]{6}[HM]{1}[A-Z]{2}[QWRTYPSDFGHJKLZXCVBNM]{3}[A-Z0-9]{1}[0-9]{1}$/;
const regex_complete_password = /^[A-Za-z0-9$#*+]{8,30}$/

let loading = "<script>"+
    "Swal.fire({"+
            "title: 'Datos enviados',"+
            "html: 'Los datos fueron enviados espere un momento',"+
            "timer: 10000,"+
            "allowOutsideClick: false,"+
            "allowEscapeKey: false,"+
            "timerProgressBar: true,"+
            "didOpen: () => {"+
            "Swal.showLoading()"+
            "}"+
        "})"+
    "</script>";

let personas = [];

function get_students() {
    const url = 'http://localhost/Proyecto_Final/src/controllers/get_students.php';
    document.getElementById('loader').innerHTML = loading;
    $.ajax({
        url: url,
        type: "GET",
        processData: false,
        contentType: false,
        success: (res) => {
            document.getElementById('loader').innerHTML = "";
            const response = JSON.parse(res);
            if (!response.success) {
                Swal.fire({
                    icon: "error",
                    title: "No se pudo enviar la información",
                    text: response.message,
                });
            } else {
                personas = response.data;
                set_data_in_table();
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


get_students();

function set_data_in_table(){
    // ordenar por hora de crecion
    personas.sort((a, b) => {
        return new Date(b.hora_solicitud) - new Date(a.hora_solicitud);
    });
    let texto = personas.map((persona, index) => `
    <tr data-bs-toggle="modal" data-bs-target="#infoModal" onclick="mostrar(${index})">
    <td>${persona.boleta}</td>
    <td>${persona.complete_name}</td>
    <td>${persona.email}</td>
    <td>${estado[persona.estado_solicitud]}</td>
    <td>${persona.casillero==0? "---": persona.casillero}</td>
    </tr>`);


    document.getElementById('tableBody').innerHTML =  texto.join('');

    const rows = document.querySelectorAll('#tableBody tr');
    rows.forEach(row => {
        row.addEventListener('mouseover', () => {
            row.classList.add('table-success');
        });

        row.addEventListener('mouseout', () => {
            row.classList.remove('table-success');
        });
    });

    const filter = get_url_param();
    if (filter) {
        document.getElementById('filterInput').value = filter;
        filter_rows(filter);
    }
}

let personass = [
]

let estado = {
    "0": "<span class='pendiente'>Pendiente</span>",
    "1": "<span class='aprobado'>Aceptado</span>",
    "2": '<span class="terminado">Terminada</span>',
    "3": '<span class="rechazado">Rechazada</span>',
    "4": '<span class="rechazado">Vencida</span>',
}

function get_url_param(){
    const url = new URL(window.location.href);
    const search_params = new URLSearchParams(url.search);
    return search_params.get('search');
}

function filter_rows(filter){
    const rows = document.querySelectorAll('#tableBody tr');

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const match = Array.from(cells).some(cell =>
            cell.textContent.toLowerCase().includes(filter)
        );
            row.style.display = match ? '' : 'none';
        });
}
    

document.getElementById('filterInput').addEventListener('input', function () {
    const filter = this.value.toLowerCase();
    filter_rows(filter);
});

// Cambio de color en la fila de la tabla de estudiantes

function open_archivo(name){
    console.log(name);
    window.open(`http://localhost/Proyecto_Final/src/uploads/${name}`, '_blank');
}

// funcion paraenviar los datos al 
function mostrar(estudiante_index){
    let estudiante_obj = personas[estudiante_index];
    document.getElementById('primer_nombre_update').value = estudiante_obj?.first_name;
    document.getElementById('segundo_nombre_update').value = estudiante_obj?.second_name;
    document.getElementById('primer_apellido_update').value = estudiante_obj?.first_last_name;
    document.getElementById('segundo_apellido_update').value = estudiante_obj?.second_last_name;
    document.getElementById('telefono_update').value = estudiante_obj?.phone;
    document.getElementById('correo_update').value = estudiante_obj?.email;
    document.getElementById('boleta_update').value = estudiante_obj?.boleta;
    document.getElementById('estatura_update').value = estudiante_obj?.height;
    document.getElementById('curp_update').value = estudiante_obj?.curp;
    document.getElementById('numeroLocker_update').value = estudiante_obj?.casillero;
    document.getElementById('id_update').value = estudiante_obj?.id;
    document.getElementById('verCredencial').setAttribute('onclick', `open_archivo('${estudiante_obj?.credencial}')`);
    document.getElementById('verHorario').setAttribute('onclick', `open_archivo('${estudiante_obj?.horario}')`);
    document.getElementById('verArchivos').style.display = 'flex';
    document.getElementById('editArchivos').style.display = 'None';
    document.getElementById('Editar_btn').style.display = 'block';
    document.getElementById('Save_btn').style.display = 'None';
    document.getElementById('primer_nombre_update').setAttribute('readonly', true);
    document.getElementById('segundo_nombre_update').setAttribute('readonly', true);
    document.getElementById('primer_apellido_update').setAttribute('readonly', true);
    document.getElementById('segundo_apellido_update').setAttribute('readonly', true);
    document.getElementById('telefono_update').setAttribute('readonly', true);
    document.getElementById('correo_update').setAttribute('readonly', true);
    document.getElementById('boleta_update').setAttribute('readonly', true);
    document.getElementById('estatura_update').setAttribute('readonly', true);
    document.getElementById('curp_update').setAttribute('readonly', true);
}

function editar_estudiante(){
    document.getElementById('primer_nombre_update').removeAttribute('readonly');
    document.getElementById('segundo_nombre_update').removeAttribute('readonly');
    document.getElementById('primer_apellido_update').removeAttribute('readonly');
    document.getElementById('segundo_apellido_update').removeAttribute('readonly');
    document.getElementById('telefono_update').removeAttribute('readonly');
    document.getElementById('correo_update').removeAttribute('readonly');
    document.getElementById('boleta_update').removeAttribute('readonly');
    document.getElementById('estatura_update').removeAttribute('readonly');
    document.getElementById('curp_update').removeAttribute('readonly');
    document.getElementById('numeroLocker_update').removeAttribute('readonly');
    document.getElementById('verArchivos').style.display = 'None';
    document.getElementById('editArchivos').style.display = 'block';
    document.getElementById('Editar_btn').style.display = 'None';
    document.getElementById('Save_btn').style.display = 'block';
}


function validar_primernombre_update(){
    const primer_nombre = document.getElementById('primer_nombre_update');
    if (primer_nombre.value.length==0 || !regex_names_without_spaces.test(primer_nombre.value)) {
        monstrar_mensaje_campo_incopleto(primer_nombre, "El Primer nombre", "Recuerda que solo admite letras, no espacios si tienes mas nombres colocalos en el campo de segundo nombre");
        return false;
    }
    return true;
}

function validar_segundonombre_update(){
    const segundo_nombre = document.getElementById('segundo_nombre_update');
    if (segundo_nombre.value.length!=0 && !regex_names.test(segundo_nombre.value)) {
        monstrar_mensaje_campo_incopleto(segundo_nombre, "El Segundo nombre", "Recuerda que solo admite letras y espacios");
        return false;
    }
    let value = segundo_nombre.value.replace(/\s/g, '');
    if (value.length > 0 && value.length < 2) {
        monstrar_mensaje_campo_incopleto(segundo_nombre, "El Segundo nombre", "Recuerda que si tienes un segundo nombre debe tener al menos 2 letras y no lo llenes de espacios en blanco");
        return false;
    }
    return true;
}

function validar_num_locker_update(){
    const num_locker = document.getElementById('numeroLocker_update');
    if (num_locker.value.length!=0 && !regex_num_locker.test(num_locker.value)) {
        monstrar_mensaje_campo_incopleto(num_locker, "El Número de locker", "Recuerda que solo admite entre 1 y 3 números");
        return false;
    }
    if (num_locker.value.length==0) {
        return true;
    }
    const value = parseInt(num_locker.value);
    if (value < 1 || value > 100) {
        monstrar_mensaje_campo_incopleto(num_locker, "El Número de locker", "Recuerda que el número de locker debe estar entre 1 y 100");
        return false;
    }
    return true;
}

function validar_primer_apellido_update(){
    const primer_apellido = document.getElementById('primer_apellido_update');
    if (primer_apellido.value.length==0 || !regex_names_without_spaces.test(primer_apellido.value)) {
        monstrar_mensaje_campo_incopleto(primer_apellido, "El Primer apellido", "Recuerda que solo admite letras, no espacios si tienes mas apellidos colocalos en el campo de segundo apellido");
        return false;
    }
    return true;
}

function validar_segundo_apellido_update(){
    const segundo_apellido = document.getElementById('segundo_apellido_update');
    if (segundo_apellido.value.length!=0 && !regex_names.test(segundo_apellido.value)) {
        monstrar_mensaje_campo_incopleto(segundo_apellido, "El Segundo apellido", "Recuerda que solo admite letras y espacios");
        return false;
    }
    let value = segundo_apellido.value.replace(/\s/g, '');
    if (value.length > 0 && value.length < 2) {
        monstrar_mensaje_campo_incopleto(segundo_apellido, "El Segundo apellido", "Recuerda que si tienes un segundo apellido debe tener al menos 2 letras y no lo llenes de espacios en blanco");
        return false;
    }
    return true;
}

function validar_telefono_update(){
    const telefono = document.getElementById('telefono_update');
    if (telefono.value.length==0 || !regex_phone.test(telefono.value)) {
        monstrar_mensaje_campo_incopleto(telefono, "El Teléfono", "Recuerda que solo admite 10 números");
        return false;
    }
    return true;
}

function validar_email_update(){
    const email = document.getElementById('correo_update');
    if (email.value.length==0 || !regex_email.test(email.value)) {
        monstrar_mensaje_campo_incopleto(email, "El Email", "Recuerda que solo admite correos institucionales");
        return false;
    }
    return true;
}

function validar_curp_update(){
    const curp = document.getElementById('curp_update');
    if (curp.value.length==0 || !regex_curp.test(curp.value)) {
        monstrar_mensaje_campo_incopleto(curp, "El CURP", "Recuerda que la CURP debe tener 18 caracteres e ingresa un formato valido");
        return false;
    }
    return true;
}

function validar_boleta_update(){
    const boleta = document.getElementById('boleta_update');
    if (boleta.value.length==0 || !regex_phone.test(boleta.value)) {
        monstrar_mensaje_campo_incopleto(boleta, "La Boleta", "Recuerda que solo admite 10 números");
        return false;
    }
    return true;
}

function validar_altura_update(){
    const altura = document.getElementById('estatura_update');
    if (altura.value.length==0 || !regex_height.test(altura.value)) {
        monstrar_mensaje_campo_incopleto(altura, "La Estatura", "");
        return false;
    }
    if (altura.value < 50 || altura.value > 270) {
        monstrar_mensaje_campo_incopleto(altura, "La Estatura", "Recuerda que la estatura debe estar entre 50 y 270");
        return false;
    }
    return true;
}


function validateForm_update() {
    
    if(!validar_num_locker_update())return;
    if (!validar_primernombre_update()) return;
    if (!validar_segundonombre_update()) return;
    if (!validar_primer_apellido_update()) return;
    if (!validar_segundo_apellido_update()) return;
    if (!validar_telefono_update()) return;
    if (!validar_email_update()) return;
    if (!validar_boleta_update()) return;
    if (!validar_altura_update()) return;
    if (!validar_curp_update()) return;

    let form = document.getElementById('updateForm');
    const event = new Event("submit", { bubbles: true, cancelable: true });
    form.dispatchEvent(event);

}

$(document).ready(function() {

    $("#updateForm").on("submit", function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        const url = 'http://localhost/Proyecto_Final/src/controllers/update_student.php';
        $("#loader").html(loading);
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
                    Swal.fire({
                        icon: "success",
                        title: "Actualización exitosa",
                        text: response.message,
                    }).then(() => {
                        window.location.href = window.location.origin + window.location.pathname;
                    });
                    setTimeout(() => {
                        window.location.href = window.location.origin + window.location.pathname;
                    }, 1000);
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
    });
});

function delete_user(){
    const id = document.getElementById('id_update').value;
    Swal.fire({
        title: '¿Estás seguro de eliminar este estudiante?',
        text: "No podrás revertir esto!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, eliminarlo!'
    }).then((result) => {
        if (result.isConfirmed) {
            const url = 'http://localhost/Proyecto_Final/src/controllers/delete_student.php';
            let formData = new FormData();
            formData.append('id', id);
            $("#loader").html(loading);
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
                        Swal.fire({
                            icon: "success",
                            title: "Se elimino el estudiante",
                            text: response.message,
                        })
                        setTimeout(() => {
                            window.location.href = window.location.origin + window.location.pathname;
                        }, 1000);
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
    });
}