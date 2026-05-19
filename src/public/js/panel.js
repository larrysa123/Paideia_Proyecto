document.addEventListener('DOMContentLoaded', function () {
    // Cargamos tanto cursos como comentarios al iniciar la página
    cargarMisCursos();
    cargarComentariosProfesor(); 
    
    // Escuchar cuando el profesor hace clic en la pestaña "Comentarios"
    // (Opcional, para refrescar datos si el usuario hace clic, aunque ya estén cargados)
    const tabComentarios = document.getElementById('comentarios-tab');
    if (tabComentarios) {
        tabComentarios.addEventListener('click', cargarComentariosProfesor);
    }
});

async function cargarMisCursos() {
    const contenedor = document.getElementById('contenedor-mis-cursos');
    const tabCursos = document.getElementById('cursos-tab'); 

    try {
        const respuesta = await fetch(BASE_URL + 'app/api/cursos.php?mis_cursos=true');
        const resultado = await respuesta.json();

        if (resultado.status === 'success') {
            const cursos = resultado.data;
            contenedor.innerHTML = ''; 
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
        contenedor.innerHTML = '<div class="alert alert-danger">Error al conectar con el servidor.</div>';
    }
}

// =======================================================
// LÓGICA DE LA PESTAÑA COMENTARIOS
// =======================================================
async function cargarComentariosProfesor() {
    const contenedor = document.getElementById('comentarios');
    const tabComentarios = document.getElementById('comentarios-tab');
    
    // Solo mostramos el spinner si la pestaña está activa, para no dar un salto visual en el fondo
    if(tabComentarios.classList.contains('active')){
       contenedor.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary" role="status"></div></div>';
    }

    try {
        // Desactivamos la caché para forzar al navegador a traer las dudas nuevas en tiempo real.
        const respuesta = await fetch(BASE_URL + 'app/api/foro_video.php?accion=mis_comentarios_profesor', { cache: 'no-store' });
        const resultado = await respuesta.json();

        if (resultado.status === 'success') {
            const dudas = resultado.data;
            tabComentarios.innerHTML = `<i class="bi bi-chat-left-text me-2"></i>Comentarios (${dudas.length})`;

            if (dudas.length === 0) {
                contenedor.innerHTML = `
                    <div class="text-center py-5">
                        <i class="bi bi-chat-square-dots text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3 text-muted">Bandeja limpia</h5>
                        <p class="text-muted">No tienes dudas pendientes por responder.</p>
                    </div>`;
                return;
            }

            let htmlDudas = '<div class="row g-4 mt-1">';
            dudas.forEach(duda => {
                htmlDudas += `
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-person-circle text-primary me-2"></i>${duda.alumno_nombre} ${duda.alumno_apellidos}</h6>
                                    <small class="text-muted">${duda.fecha}</small>
                                </div>
                                <div class="mb-3 small text-muted border-bottom pb-2">
                                    <i class="bi bi-book me-1"></i> Curso: <strong>${duda.curso_titulo}</strong> <br>
                                    <i class="bi bi-play-circle me-1"></i> Vídeo: <strong>${duda.video_titulo}</strong>
                                </div>
                                <p class="text-secondary">${duda.texto}</p>
                                
                                <div class="mt-3 bg-light p-3 rounded">
                                    <label class="small fw-bold text-dark mb-1">Tu respuesta:</label>
                                    <textarea id="respuesta-${duda.id_comentario}" class="form-control form-control-sm mb-2" rows="2" placeholder="Escribe tu respuesta como profesor..."></textarea>
                                    <div class="text-end">
                                        <button onclick="responderDuda(${duda.id_video}, ${duda.id_comentario})" class="btn btn-sm btn-paideia">
                                            <i class="bi bi-send me-1"></i> Enviar Respuesta
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            htmlDudas += '</div>';
            contenedor.innerHTML = htmlDudas;
        }
    } catch (error) {
        contenedor.innerHTML = '<div class="alert alert-danger mt-4">Error al cargar la bandeja de comentarios.</div>';
    }
}

// Función que ejecuta el botón de enviar respuesta
async function responderDuda(idVideo, idPadre) {
    const textarea = document.getElementById(`respuesta-${idPadre}`);
    const texto = textarea.value.trim();

    if (!texto) {
        alert("La respuesta no puede estar vacía.");
        return;
    }

    try {
        const res = await fetch(BASE_URL + 'app/api/foro_video.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id_video: idVideo, texto: texto, id_padre: idPadre })
        });
        const data = await res.json();
        
        if (data.status === 'success') {
            alert("Respuesta enviada con éxito.");
            textarea.value = ''; 
            cargarComentariosProfesor(); 
        } else {
            alert(data.mensaje);
        }
    } catch(err) {
        alert("Error de conexión al enviar la respuesta.");
    }
}

// =======================================================
// FUNCIONES GESTIONAR TEMARIO Y ELIMINAR CURSO
// =======================================================
function gestionarTemario(id) {
    window.location.href = `gestionar_videos.php?id=${id}`;
}

async function eliminarCurso(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este curso? Esta acción no se puede deshacer.')) {
        try {
            const respuesta = await fetch(BASE_URL + 'app/api/cursos.php?id=' + id, { method: 'DELETE' });
            const resultado = await respuesta.json();
            if (resultado.status === 'success') {
                alert(resultado.mensaje);
                cargarMisCursos();
            } else {
                alert("Error: " + resultado.mensaje);
            }
        } catch (error) {
            alert("Hubo un error de conexión al intentar eliminar el curso.");
        }
    }
}