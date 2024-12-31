
const regex_curp = /^[A-Z]{4}[0-9]{6}[HM]{1}[A-Z]{2}[QWRTYPSDFGHJKLZXCVBNM]{3}[A-Z0-9]{1}[0-9]{1}$/;
const regex_phone = /^[0-9]{10}$/;

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

function validar_boleta(){
    const boleta = document.getElementById('boleta');
    if (boleta.value.length==0 || !regex_phone.test(boleta.value)) {
        monstrar_mensaje_campo_incopleto(boleta, "La Boleta", "Recuerda que solo admite 10 números");
        return false;
    }
    return true;
}

function validar_password_form() {
    const password = document.getElementById('Password');
    if (password.value.length==0){
        monstrar_mensaje_campo_incopleto(password, "La contraseña", "Recuerda que la contraseña es obligatoria");
        return false
    }
    return true;
}

function validar_formulario() {
    if(!validar_boleta()) return;
    if(!validar_password_form()) return;

    
}