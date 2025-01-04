

function mostrarnucas(show) {
    console.log("mostrar nucas");
    const lockerField = document.getElementById('lockerField');
    lockerField.style.display = show ? 'block' : 'none';
    if (!show) {
        document.getElementById('numeroLocker').value = '';
    }
}



const requirements = {
    minLength: document.getElementById('minLength'),
    uppercase: document.getElementById('uppercase'),
    lowercase: document.getElementById('lowercase'),
    number: document.getElementById('number'),
    specialChar: document.getElementById('specialChar'),
};

const regex = {
    minLength: /^.{8,30}$/,
    uppercase: /[A-Z]/,
    lowercase: /[a-z]/,
    number: /\d/,
    specialChar: /[$#*+]/,
};

function validatePassword() {
    const passwordInput = document.getElementById('password');
    const value = passwordInput.value;

    // Check each requirement
    for (let key in regex) {
        if (regex[key].test(value)) {
            requirements[key].classList.add('valid');
            requirements[key].classList.remove('invalid');
        } else {
            requirements[key].classList.add('invalid');
            requirements[key].classList.remove('valid');
        }
    }
}

function mostrarPassword() {
    const passwordInput = document.getElementById('password');
    const passwordFieldType = passwordInput.getAttribute('type');
    const eyeIcon = document.getElementById('ojocontra');
    if (passwordFieldType === 'password') {
        passwordInput.setAttribute('type', 'text');
        eyeIcon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16">
            <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588M5.21 3.088A7 7 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474z"/>
            <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12z"/>
        </svg>`
    } else {
        passwordInput.setAttribute('type', 'password');
        eyeIcon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
        </svg>`
    }
}

function mostrarPassword2() {
    const passwordInput = document.getElementById('password2');
    const passwordFieldType = passwordInput.getAttribute('type');
    const eyeIcon = document.getElementById('ojocontra2');
    if (passwordFieldType === 'password') {
        passwordInput.setAttribute('type', 'text');
        eyeIcon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-slash-fill" viewBox="0 0 16 16">
            <path d="m10.79 12.912-1.614-1.615a3.5 3.5 0 0 1-4.474-4.474l-2.06-2.06C.938 6.278 0 8 0 8s3 5.5 8 5.5a7 7 0 0 0 2.79-.588M5.21 3.088A7 7 0 0 1 8 2.5c5 0 8 5.5 8 5.5s-.939 1.721-2.641 3.238l-2.062-2.062a3.5 3.5 0 0 0-4.474-4.474z"/>
            <path d="M5.525 7.646a2.5 2.5 0 0 0 2.829 2.829zm4.95.708-2.829-2.83a2.5 2.5 0 0 1 2.829 2.829zm3.171 6-12-12 .708-.708 12 12z"/>
        </svg>`
    } else {
        passwordInput.setAttribute('type', 'password');
        eyeIcon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-eye-fill" viewBox="0 0 16 16">
            <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
            <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
        </svg>`
    }
}

function show_code_verification() {
    const email = document.getElementById('correo');
    if (email.value.length==0 || !regex_email.test(email.value)) {
        document.getElementById('verification_code_container').style.display = 'none';
        return;
    }
    document.getElementById('verification_code_container').style.display = 'block';
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

function validar_primernombre(){
    const primer_nombre = document.getElementById('primer_nombre');
    if (primer_nombre.value.length==0 || !regex_names_without_spaces.test(primer_nombre.value)) {
        monstrar_mensaje_campo_incopleto(primer_nombre, "El Primer nombre", "Recuerda que solo admite letras, no espacios si tienes mas nombres colocalos en el campo de segundo nombre");
        return false;
    }
    return true;
}

function validar_segundonombre(){
    const segundo_nombre = document.getElementById('segundo_nombre');
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

function validar_num_locker(){
    const num_locker = document.getElementById('numeroLocker');
    if (num_locker.value.length==0 || !regex_num_locker.test(num_locker.value)) {
        monstrar_mensaje_campo_incopleto(num_locker, "El Número de locker", "Recuerda que solo admite entre 1 y 3 números");
        return false;
    }
    const value = parseInt(num_locker.value);
    if (value < 1 || value > 100) {
        monstrar_mensaje_campo_incopleto(num_locker, "El Número de locker", "Recuerda que el número de locker debe estar entre 1 y 999");
        return false;
    }
    return true;
}

function validar_primer_apellido(){
    const primer_apellido = document.getElementById('primer_apellido');
    if (primer_apellido.value.length==0 || !regex_names_without_spaces.test(primer_apellido.value)) {
        monstrar_mensaje_campo_incopleto(primer_apellido, "El Primer apellido", "Recuerda que solo admite letras, no espacios si tienes mas apellidos colocalos en el campo de segundo apellido");
        return false;
    }
    return true;
}

function validar_segundo_apellido(){
    const segundo_apellido = document.getElementById('segundo_apellido');
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

function validar_telefono(){
    const telefono = document.getElementById('telefono');
    if (telefono.value.length==0 || !regex_phone.test(telefono.value)) {
        monstrar_mensaje_campo_incopleto(telefono, "El Teléfono", "Recuerda que solo admite 10 números");
        return false;
    }
    return true;
}

function validar_email(){
    const email = document.getElementById('correo');
    if (email.value.length==0 || !regex_email.test(email.value)) {
        monstrar_mensaje_campo_incopleto(email, "El Email", "Recuerda que solo admite correos institucionales");
        return false;
    }
    return true;
}

function validar_curp(){
    const curp = document.getElementById('curp');
    if (curp.value.length==0 || !regex_curp.test(curp.value)) {
        monstrar_mensaje_campo_incopleto(curp, "El CURP", "Recuerda que la CURP debe tener 18 caracteres e ingresa un formato valido");
        return false;
    }
    return true;
}

function validar_boleta(){
    const boleta = document.getElementById('boleta');
    if (boleta.value.length==0 || !regex_phone.test(boleta.value)) {
        monstrar_mensaje_campo_incopleto(boleta, "La Boleta", "Recuerda que solo admite 10 números");
        return false;
    }
    return true;
}

function validar_altura(){
    const altura = document.getElementById('estatura');
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

function validar_horario(){
    const horario = document.getElementById('horario');
    if (horario.value.length==0) {
        monstrar_mensaje_campo_incopleto(horario, "El Horario", "");
        return false;
    }
    return true;
}

function validar_credencial(){
    const credencial = document.getElementById('credencial');
    if (credencial.value.length==0) {
        monstrar_mensaje_campo_incopleto(credencial, "La Credencial", "");
        return false;
    }
    console.log(credencial.files[0].name);
    return true;
}

function validar_password_form() {
    const password = document.getElementById('password');
    const password2 = document.getElementById('password2');
    if (password.value != password2.value) {
        monstrar_mensaje_campo_incopleto(password, "La Contraseña", "Las contraseñas no coinciden");
        return false;
    }
    for (let key in regex) {
        if (!regex[key].test(password.value)) {
            monstrar_mensaje_campo_incopleto(password, "La Contraseña", "Recuerda que la contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula y un número");
            return false;
        }
    }

    if(!regex_complete_password.test(password.value)){
        monstrar_mensaje_campo_incopleto(password, "La Contraseña", "Recuerda que la contraseña debe tener al menos 8 caracteres, maximo 30, una mayúscula, una minúscula y un número");
        return false;
    }
    return true;
}

function verify_verification_code() {
    const verification_code = document.getElementById('verification_code');
    if (verification_code.value.length==0) {
        monstrar_mensaje_campo_incopleto(verification_code, "El Código de verificación", "");
        return false;
    }
    if (!regex_code_verification.test(verification_code.value)) {
        monstrar_mensaje_campo_incopleto(verification_code, "El Código de verificación", "Recuerda que el código de verificación debe tener 6 caracteres y solo admite letras mayúsculas y números");
        return false;
    }
    return true;
}

function validateForm() {
    if (!validar_password_form()) return;

    let is_renovacion = false;
    let seleccion = document.querySelector('input[name="tipoSolicitud"]:checked');
    if (seleccion == null) {
        Swal.fire({
            icon: "error",
            title: "Tipo de solicitud no seleccionada",
            text: "Selecciona un tipo de solicitud",
        });
        return false;
    }
    if (seleccion.value == "renovacion") {
        is_renovacion = true;
        if(!validar_num_locker() )return;
    }
    if (!validar_primernombre()) return;
    if (!validar_segundonombre()) return;
    if (!validar_primer_apellido()) return;
    if (!validar_segundo_apellido()) return;
    if (!validar_telefono()) return;
    if (!validar_email()) return;
    if (!validar_boleta()) return;
    if (!validar_altura()) return;
    if (!validar_credencial()) return;
    if (!validar_horario()) return;
    if (!validar_curp()) return;
    if (!validar_password_form()) return;
    if (!verify_captha()) return;

    let form = document.getElementById('registerForm');
    const event = new Event("submit", { bubbles: true, cancelable: true });
    form.dispatchEvent(event);

}

function reload_capcha() {
    grecaptcha.reset();
}


function reset_form() {
    document.getElementById('registerForm').reset();
    mostrarnucas(false);
    reload_capcha();
}

function verify_captha() {
    const recaptchaResponse = document.getElementById('g-recaptcha-response').value;
    if (recaptchaResponse) {
        return true;
    }
    Swal.fire({
        icon: "error",
        title: "reCAPTCHA no completado",
        text: "Por favor, completa el reCAPTCHA para continuar",
    });
    return false;
}


$(document).ready(function() {

    // Envio de la información
    $("#registerForm").on("submit", function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        const url = 'http://localhost/Proyecto_Final/src/controllers/register_student_admin.php';
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
                    grecaptcha.reset();
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


