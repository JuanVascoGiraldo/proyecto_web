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
        if ((i+1) % 3 === 0) div.className = "locker vencido";
        else if ((i+1) % 2 === 0) div.className = "locker ocupado";
        else if ((i+1) % 7 === 0) div.className = "locker pendiente";
        else div.className = "locker disponible";
        div.textContent = locker;
        div.setAttribute("data-bs-toggle", "modal");
        div.setAttribute("data-bs-target", "#infoModal");
        div.onclick = mostar.bind(null, parseInt(locker) - 1);
        container.appendChild(div);
    });
}

// Mostrar la primera página al cargar
showPage(1);

function mostar(i) {
    document.getElementById('infoModalLabel').innerHTML = `Informacion del Casillero ${i + 1}`;
    document.getElementById('casillero').innerHTML =`
        <p>Estado: ${i % 3 === 0 ? 'Vencido' : i % 2 === 0 ? 'Ocupado' : i % 7 === 0 ? 'Pendiente' : 'Disponible'}</p>
        <p>Fecha de vencimiento: ${i % 3 === 0 ? '01/01/2021' : 'N/A'}</p>
        <p>Boleta: ${i % 2 === 0 ? '2019630001' : 'N/A'}</p>
        <p>Nombre: ${i % 2 === 0 ? 'Juan Perez' : 'N/A'}</p>
        <p>Correo: ${i % 2 === 0 ? 'jvascog1900@alumno.ipn.mx' : 'N/A'}</p>
    `
}