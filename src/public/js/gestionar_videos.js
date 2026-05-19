let sortableInstance = null; // Guardará la instancia del Drag&Drop

document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const idCurso = urlParams.get('id');

    if (!idCurso) {
        alert("ID de curso no válido.");
        window.location.href = 'panel.php';
        return;
    }

    // Guardamos el ID en el formulario oculto
    document.getElementById('id_curso_video').value = idCurso;

    // Cargar los vídeos al entrar
    cargarVideos(idCurso);

    // =========================================================
    // LÓGICA DEL FORMULARIO (Crear y Editar)
    // =========================================================
    document.getElementById('formVideo').addEventListener('submit', async function (e) {
        e.preventDefault();

        const idVideoEditar = document.getElementById('id_video_editar').value;

        // Calculamos el orden contando solo los <li> que tengan el atributo data-id (vídeos reales)
        const listaActual = document.querySelectorAll('#lista-videos li[data-id]').length;
        const ordenAutomatico = listaActual + 1;

        const datosVideo = {
            id_curso: document.getElementById('id_curso_video').value,
            titulo: document.getElementById('titulo_video').value,
            url_youtube: document.getElementById('url_video').value,
            orden: ordenAutomatico
        };

        const btnSubmit = document.getElementById('btn-guardar-video');
        const textoOriginal = btnSubmit.innerText;
        btnSubmit.innerText = "Guardando...";
        btnSubmit.disabled = true;

        try {
            let res, resultado;

            if (idVideoEditar) {
                // MODO EDICIÓN (PUT)
                datosVideo.id_video = idVideoEditar;
                res = await fetch(BASE_URL + 'app/api/videos.php', {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(datosVideo)
                });
            } else {
                // MODO CREACIÓN (POST)
                res = await fetch(BASE_URL + 'app/api/videos.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(datosVideo)
                });
            }

            resultado = await res.json();

            if (resultado.status === 'success') {
                cancelarEdicion(); // Limpia y resetea el formulario
                cargarVideos(idCurso);
            } else {
                alert("Error: " + resultado.mensaje);
            }
        } catch (error) {
            console.error("Error al guardar vídeo:", error);
        } finally {
            btnSubmit.innerText = textoOriginal;
            btnSubmit.disabled = false;
        }
    });
});

// =========================================================
// PINTAR VÍDEOS E INICIALIZAR EL DRAG AND DROP
// =========================================================
async function cargarVideos(idCurso) {
    try {
        const res = await fetch(BASE_URL + 'app/api/videos.php?id_curso=' + idCurso);
        const resultado = await res.json();

        document.getElementById('cargando-videos').classList.add('d-none');
        const lista = document.getElementById('lista-videos');
        lista.innerHTML = '';
        lista.classList.remove('d-none');

        if (resultado.status === 'success' && resultado.data.length > 0) {
            resultado.data.forEach(video => {
                // Escapamos comillas para evitar que rompan el onclick
                const tituloSeguro = video.titulo.replace(/'/g, "\\'");

                lista.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="${video.id_video}" style="cursor: grab;">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-grip-vertical text-muted fs-4 me-2 drag-handle" style="cursor: grab;" title="Arrastrar"></i>
                            <div>
                                <span class="badge bg-secondary me-2 num-leccion">Lec. ${video.orden}</span>
                                <span class="fw-bold">${video.titulo}</span>
                                <br>
                                <a href="${video.url_youtube}" target="_blank" class="small text-muted text-decoration-none">
                                    <i class="bi bi-link-45deg"></i> Ver enlace original
                                </a>
                            </div>
                        </div>
                        <div>
                            <button onclick="prepararEdicion(${video.id_video}, '${tituloSeguro}', '${video.url_youtube}')" class="btn btn-sm btn-outline-primary me-1" title="Editar Lección">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button onclick="eliminarVideo(${video.id_video}, ${idCurso})" class="btn btn-sm btn-outline-danger" title="Borrar Lección">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </li>
                `;
            });

            // Inicializamos SortableJS para el Drag & Drop
            if (sortableInstance) sortableInstance.destroy(); // Destruimos el anterior si recargamos

            sortableInstance = new Sortable(lista, {
                handle: '.drag-handle', // Solo se arrastra desde los puntitos
                animation: 150,
                ghostClass: 'bg-light',
                onEnd: function () {
                    actualizarOrdenBackend(idCurso);
                }
            });

        } else {
            lista.innerHTML = '<li class="list-group-item text-muted text-center py-4">No hay vídeos subidos todavía. Rellena el formulario para empezar.</li>';
        }
    } catch (error) {
        console.error("Error al cargar vídeos:", error);
    }
}

// =========================================================
// COMUNICAR EL NUEVO ORDEN A LA BASE DE DATOS
// =========================================================
async function actualizarOrdenBackend(idCurso) {
    const elementos = document.querySelectorAll('#lista-videos li');
    let nuevoOrdenArray = [];

    // Cambiamos visualmente las etiquetas "Lec. X" y preparamos el paquete de datos
    elementos.forEach((li, index) => {
        const nuevoNum = index + 1;
        li.querySelector('.num-leccion').innerText = `Lec. ${nuevoNum}`;

        nuevoOrdenArray.push({
            id_video: li.getAttribute('data-id'),
            orden: nuevoNum
        });
    });

    try {
        // Mandamos el array al servidor en silencio
        await fetch(BASE_URL + 'app/api/videos.php', {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ accion: 'reordenar', orden_videos: nuevoOrdenArray })
        });
    } catch (error) {
        console.error("Error al reordenar en BD:", error);
    }
}

// =========================================================
// FUNCIONES DE EDICIÓN Y BORRADO
// =========================================================
function prepararEdicion(idVideo, titulo, url) {
    document.getElementById('titulo-formulario').innerHTML = '<i class="bi bi-pencil-square me-2"></i>Editar Lección';
    document.getElementById('id_video_editar').value = idVideo;
    document.getElementById('titulo_video').value = titulo;
    document.getElementById('url_video').value = url;

    document.getElementById('btn-guardar-video').innerText = 'Actualizar Vídeo';
    document.getElementById('btn-cancelar-edicion').classList.remove('d-none');
}

function cancelarEdicion() {
    document.getElementById('formVideo').reset();
    document.getElementById('titulo-formulario').innerHTML = '<i class="bi bi-plus-circle me-2"></i>Añadir Nueva Lección';
    document.getElementById('id_video_editar').value = '';

    document.getElementById('btn-guardar-video').innerText = 'Guardar Vídeo';
    document.getElementById('btn-cancelar-edicion').classList.add('d-none');
}

async function eliminarVideo(idVideo, idCurso) {
    if (confirm('¿Seguro que quieres borrar esta lección? Se perderán también las dudas del foro asociadas.')) {
        try {
            const res = await fetch(BASE_URL + 'app/api/videos.php?id=' + idVideo, { method: 'DELETE' });
            const resultado = await res.json();
            if (resultado.status === 'success') {
                cargarVideos(idCurso);
            } else {
                alert(resultado.mensaje);
            }
        } catch (error) {
            console.error("Error al eliminar:", error);
        }
    }
}