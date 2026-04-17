document.addEventListener('DOMContentLoaded', async function() {
    try {
        // 1. Llamamos a nuestra API de inscripciones (Petición GET)
        const respuesta = await fetch(BASE_URL + 'app/api/inscripciones.php');
        const resultado = await respuesta.json();

        // 2. Ocultamos el spinner de carga
        const spinner = document.getElementById('cargando-mis-cursos');
        if (spinner) spinner.classList.add('d-none');

        if (resultado.status === 'success') {
            const cursos = resultado.data;
            const grid = document.getElementById('grid-mis-cursos');

            // Si la base de datos dice que tiene 0 cursos...
            if (cursos.length === 0) {
                document.getElementById('mensaje-vacio').classList.remove('d-none');
                return;
            }

            // Si tiene cursos, los pintamos como columnas
            cursos.forEach(curso => {
                const imgPath = curso.imagen ? `../../assets/img/cursos/${curso.imagen}` : 'https://via.placeholder.com/300x200';
                const progreso = curso.progreso || 0;

                // Creamos la tarjeta dentro de una columna de Bootstrap (col-md-4 = 3 por fila)
                grid.innerHTML += `
                    <div class="col-md-4 col-sm-6">
                        <div class="card h-100 border-0 shadow-sm rounded-3 overflow-hidden">
                            <img src="${imgPath}" class="card-img-top" alt="${curso.titulo}" style="height: 180px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title fw-bold text-dark mb-2">${curso.titulo}</h5>
                                <p class="card-text text-muted small flex-grow-1">${curso.descripcion.substring(0, 80)}...</p>
                                
                                <div class="mt-auto pt-3 border-top">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <small class="text-muted fw-bold">Progreso</small>
                                        <small class="text-primary fw-bold">${progreso}%</small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: ${progreso}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-white border-0 p-3 pt-0">
                                <button onclick="irALeccion(${curso.id_curso})" class="btn btn-outline-primary w-100 rounded-pill fw-bold">
                                    <i class="bi bi-play-circle me-1"></i> Continuar
                                </button>
                            </div>
                        </div>
                    </div>
                `;
            });
        } else {
            console.error("Error del servidor:", resultado.mensaje);
        }
    } catch (error) {
        console.error("Error de fetch:", error);
    }
});

function irALeccion(id) {
    // Aquí es donde mandaremos al alumno cuando pulse "Continuar" para ver los vídeos
    alert("Próximamente: Redirigiendo a la sala de clases del curso " + id);
}