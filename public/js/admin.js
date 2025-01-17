
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


function monstrar_mensaje_campo_incopleto(campo, nombre, texto) {
    Swal.fire({
        icon: "error",
        title: "Datos",
        text: nombre + " esta incompleto o es incorrecto. " + texto,
    }).then(() => {
        campo.classList.add("invalid_campo");
        setTimeout(() => {
            campo.classList.remove("invalid_campo");
            campo.focus();
        }, 2000);
    });
}

function validar_password_form() {
    const password = document.getElementById('Password');
    if (password.value.length==0){
        monstrar_mensaje_campo_incopleto(password, "La contraseña", "Recuerda que la contraseña es obligatoria");
        return false
    }
    if (!regex_complete_password.test(password.value)) {
        monstrar_mensaje_campo_incopleto(password, "La contraseña", "Recuerda que la contraseña debe tener al menos 8 caracteres");
        return false;
    }
    return true;
}

function validar_email() {
    const email = document.getElementById('email');
    if (email.value.length==0){
        monstrar_mensaje_campo_incopleto(email, "El correo", "Recuerda que el correo es obligatorio");
        return false
    }
    return true;
}

function validar_formulario_admin() {
    if(!validar_email()) return;
    if(!validar_password_form()) return;

    const form = document.getElementById('form_admin');
    const event = new Event('submit', {
        'bubbles': true,
        'cancelable': true
    });
    form.dispatchEvent(event);
    
}

$(document).ready(function() {

    // Envio de la información
    $("#form_admin").on("submit", function(e) {
        console.log("Enviando formulario");
        e.preventDefault();

        let formData = new FormData(this);
        const url = '../src/controllers/login_admin.php';
        $("#loader").value = loading;
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
                        title: "Inicio exitoso",
                        text: response.message,
                    });
                    setTimeout(() => {
                        location.href = 'http://localhost/Proyecto_Final/public/admin/';
                    }, 1000);
                }
            },
            error: () => {
                grecaptcha.reset();
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