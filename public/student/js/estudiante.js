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


function acept_term(id){
    aceptado = document.getElementById("aceptar").checked;
    if(!aceptado){
        Swal.fire({
            icon: "error",
            title: "No se ha aceptado el termino",
            text: "Por favor acepta el termino para continuar"
        });
        return;
    }

    const url = '../../src/controllers/acept_terms.php';
    let formData = new FormData();
    formData.append('request_id', id);
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
                    title: "Registo exitoso",
                    text: response.message,
                }).then(() => {
                    location.reload();
                });
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

function monstrar_mensaje_campo_incopleto(campo, nombre, texto) {
    Swal.fire({
        icon: "error",
        title: "Datos",
        text: nombre + " esta incompleto o es incorrecto. " + texto,
    }).then(() => {
        setTimeout(() => {
            campo.focus();
        }, 300);
    });
}

function validar_comprobante(){
    const comprobante = document.getElementById('comprobante');
    if (comprobante.value.length==0) {
        monstrar_mensaje_campo_incopleto(comprobante, "El comprobante", "");
        return false;
    }
    return true;
}

function validate_send_payment_url(){
    if(!validar_comprobante()) return;

    const form = document.getElementById('form_payment');
    const event = new Event('submit', {
        'bubbles': true,
        'cancelable': true
    });
    form.dispatchEvent(event);
}

$(document).ready(function() {

    // Envio de la información
    $("#form_payment").on("submit", function(e) {
        console.log("Enviando formulario");
        e.preventDefault();
        let formData = new FormData(this);
        const url = '../../src/controllers/upload_comprobante.php';
        $("#loader").html(loading);
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: (res) => {
                const response = JSON.parse(res);
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
                        title: "Envio exitoso",
                        text: response.message,
                    }).then(() => {
                        location.reload();
                    });
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