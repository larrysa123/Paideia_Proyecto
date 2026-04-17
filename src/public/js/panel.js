document.addEventListener('DOMContentLoaded', function () {
    cargarMisCursos();
});

async function cargarMisCursos() {
    const contenedor = document.getElementById('contenedor-mis-cursos');
    const tabCursos = document.getElementById('cursos-tab'); // Para actualizar el contador (3)

    try {
        // Llamamos a la API con el parámetro que creamos en el controlador
        const respuesta = await fetch(BASE_URL + 'app/api/cursos.php?mis_cursos=true');
        const resultado = await respuesta.json();

        if (resultado.status === 'success') {
            const cursos = resultado.data;
            contenedor.innerHTML = ''; // Limpiamos el spinner

            // Actualizamos el contador de la pestaña
            tabCursos.innerHTML = `<i class="bi bi-book me-2"></i>Mis Cursos (${cursos.length})`;

            cursos.forEach(curso => {
                const card = `
                <div class="col-md-4">
                    <div class="card h-100 p-3 shadow-sm border-0">
                        <div class="card-body p-0 d-flex flex-column">
                            <h6 class="card-title-panel mb-2">${curso.titulo}</h6>
                            <p class="card-text text-muted card-desc-panel mb-3">
                                ${curso.descripcion || 'Sin descripción.'}
                            </p>
                            <div class="mb-4">
                                <span class="d-block mb-2 precio-texto">${curso.precio}€</span>
                                <span class="d-block curso-meta">Estado: <strong>${curso.estado}</strong></span>
                            </div>
                         
                            <div class="d-flex gap-2 mt-auto">
                                <button onclick="gestionarTemario(${curso.id_curso})" class="btn btn-paideia-dark text-white flex-grow-1" title="Gestionar Temario">
                                    <i class="bi bi-collection-play me-1"></i> Temario
                                </button>
                                <a href="editar_curso.php?id=${curso.id_curso}" class="btn btn-paideia px-3" title="Editar Curso">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button onclick="eliminarCurso(${curso.id_curso})" class="btn btn-paideia-danger px-3" title="Eliminar Curso">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>`;
                contenedor.innerHTML += card;
            });
        } else {
            contenedor.innerHTML = `<div class="col-12 text-center py-5 text-muted">
                <h5>${resultado.mensaje}</h5>
                <p>¡Anímate a crear tu primer curso!</p>
            </div>`;
            tabCursos.innerHTML = `<i class="bi bi-book me-2"></i>Mis Cursos (0)`;
        }
    } catch (error) {
        console.error("Error cargando el panel:", error);
        contenedor.innerHTML = '<div class="alert alert-danger">Error al conectar con el servidor.</div>';
    }
}

// NUEVA FUNCIÓN: Redirige al gestor de vídeos
function gestionarTemario(id) {
    // Viajamos a la nueva página pasándole el ID del curso
    window.location.href = `gestionar_videos.php?id=${id}`;
}

// Función para el botón de eliminar
async function eliminarCurso(id) {
    // 1. Pedimos confirmación para no borrar por accidente
    if (confirm('¿Estás seguro de que quieres eliminar este curso? Esta acción no se puede deshacer.')) {

        try {
            // 2. Enviamos la petición DELETE a la API, pasando el ID en la URL
            const respuesta = await fetch(BASE_URL + 'app/api/cursos.php?id=' + id, {
                method: 'DELETE'
            });

            // 3. Leemos la respuesta del servidor
            const resultado = await respuesta.json();

            if (resultado.status === 'success') {
                // Si ha ido bien, avisamos...
                alert(resultado.mensaje);
                // ...y volvemos a cargar las tarjetas para que el curso desaparezca de la pantalla al instante
                cargarMisCursos();
            } else {
                alert("Error: " + resultado.mensaje);
            }

        } catch (error) {
            console.error("Error al eliminar:", error);
            alert("Hubo un error de conexión al intentar eliminar el curso.");
        }
    }
}