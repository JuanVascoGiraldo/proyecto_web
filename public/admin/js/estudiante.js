// Filtro de busqueda en la tabla de estudiantes

let personas = [
    {
        "boleta": "2023631015",
        "nombre": "Juan Esteban Vasco Giraldo",
        "correo": "jvascog100@alumno.ipn.mx",
        "credencial": "FILE_6777427b60c642.90745910.pdf",
        "horario": "FILE_6777427b60c642.90745910.pdf",
        "fecha_solicitud": "2021-06-01",
        "fecha_entrega": "2021-06-01",
        "estado": "0",
        "casillero": "---"
    },
    {
        "boleta": "2023632015",
        "nombre": "Juan Esteban Vasco Giraldo",
        "correo": "jvascog190@alumno.ipn.mx",
        "credencial": "FILE_6777427b60c642.90745910.pdf",
        "horario": "FILE_6777427b60c642.90745910.pdf",
        "fecha_solicitud": "2021-06-01",
        "fecha_entrega": "2021-06-01",
        "estado": "0",
        "casillero": "---"
    },
    {
        "boleta": "2023633015",
        "nombre": "Juan Esteban Vasco Giraldo",
        "correo": "jvascog1900@alumno.ipn.mx",
        "credencial": "FILE_6777427b60c642.90745910.pdf",
        "horario": "FILE_6777427b60c642.90745910.pdf",
        "fecha_solicitud": "2021-06-01",
        "fecha_entrega": "2021-06-01",
        "estado": "1",
        "casillero": "12"
    },
    {
        "boleta": "2023634015",
        "nombre": "Juan Esteban Vasco Giraldo",
        "correo": "jvasco1900@alumno.ipn.mx",
        "credencial": "FILE_6777427b60c642.90745910.pdf",
        "horario": "FILE_6777427b60c642.90745910.pdf",
        "fecha_solicitud": "2021-06-01",
        "fecha_entrega": "2021-06-01",
        "estado": "2",
        "casillero": "---"
    },
    {
        "boleta": "2023635015",
        "nombre": "Juan Esteban Vasco Giraldo",
        "correo": "jvasog1900@alumno.ipn.mx",
        "credencial": "FILE_6777427b60c642.90745910.pdf",
        "horario": "FILE_6777427b60c642.90745910.pdf",
        "fecha_solicitud": "2021-06-01",
        "fecha_entrega": "2021-06-01",
        "estado": "1",
        "casillero": "11"
    }
]

let estado = {
    "0": "<span class='pendiente'>Pendiente</span>",
    "2": '<span class="rechazado">Rechazada</span>',
    "1": '<span class="terminado">Terminada</span>'
}


let texto = personas.map((persona, index) => `
    <tr data-bs-toggle="modal" data-bs-target="#infoModal" onclick="mostrar(${index})">
    <td>${persona["boleta"]}</td>
    <td>${persona["nombre"]}</td>
    <td>${persona["correo"]}</td>
    <td>${estado[persona["estado"]]}</td>
    <td>${persona["casillero"]}</td>
    </tr>`);


document.getElementById('tableBody').innerHTML =  texto.join('');
    

document.getElementById('filterInput').addEventListener('input', function () {
    const filter = this.value.toLowerCase();
    const rows = document.querySelectorAll('#tableBody tr');

    rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const match = Array.from(cells).some(cell =>
            cell.textContent.toLowerCase().includes(filter)
        );
            row.style.display = match ? '' : 'none';
        });
});

// Cambio de color en la fila de la tabla de estudiantes
const rows = document.querySelectorAll('#tableBody tr');
rows.forEach(row => {
    row.addEventListener('mouseover', () => {
        row.classList.add('table-success');
    });

    row.addEventListener('mouseout', () => {
        row.classList.remove('table-success');
    });
});

function open_archivo(name){
    console.log(name);
    window.open(`http://localhost/Proyecto_Final/src/uploads/${name}`, '_blank');
}

// funcion paraenviar los datos al 
function mostrar(estudiante_index){
    let estudiante_obj = personas[estudiante_index];
    let datos_per = document.getElementById("datos_personales");
    datos_per.innerHTML = `<p>Boleta: ${estudiante_obj.boleta}</p>
        <p>Nombre: ${estudiante_obj.nombre}</p>
        <p>Correo: ${estudiante_obj.correo}</p>
        <button type="button" class="btn btn-outline-success" onclick="open_archivo('${estudiante_obj.credencial}')">Ver credencial</button>
        <button type="button" class="btn btn-outline-info" onclick="open_archivo('${estudiante_obj.horario}')">Ver horario</button>`;
    let datos_es = document.getElementById("solcitud");
    datos_es.innerHTML = `
        <p>Fecha de solicitud: ${estudiante_obj.fecha_solicitud}</p>
        <p>Estado: ${estado[estudiante_obj.estado]}</p>
    `;
}