
const regex_curp = /^[A-Z]{4}[0-9]{6}[HM]{1}[A-Z]{2}[QWRTYPSDFGHJKLZXCVBNM]{3}[A-Z0-9]{1}[0-9]{1}$/;

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

function validar_curp(){
    const curp = document.getElementById('Curp');
    if (curp.value.length==0 || !regex_curp.test(curp.value)) {
        monstrar_mensaje_campo_incopleto(curp, "El CURP", "Recuerda que la CURP debe tener 18 caracteres e ingresa un formato valido");
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

function validar_formulario_admin() {
    if(!validar_curp()) return;
    if(!validar_password_form()) return;

    Swal.fire({
        icon: "success",
        title: "Datos",
        text: "Datos correctos",
    }).then(() => {
        location.reload()
    });
}