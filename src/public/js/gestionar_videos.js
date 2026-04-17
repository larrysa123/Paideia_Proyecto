document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const idCurso = urlParams.get('id');

    if (!idCurso) {
        alert("ID de curso no válido.");
        window.location.href = 'panel.php';
        return;
    }

    // Guardamos el ID en el formulario oculto
    document.getElementById('id_curso_video').value = idCurso;

    // 1. Cargar los vídeos al entrar
    cargarVideos(idCurso);

    // 2. Escuchar el formulario para guardar vídeos nuevos
    document.getElementById('formVideo').addEventListener('submit', async function(e) {
        e.preventDefault();

        const datosVideo = {
            id_curso: document.getElementById('id_curso_video').value,
            titulo: document.getElementById('titulo_video').value,
            url_youtube: document.getElementById('url_video').value,
            orden: document.getElementById('orden_video').value
        };

        try {
            const res = await fetch(BASE_URL + 'app/api/videos.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosVideo)
            });

            const resultado = await res.json();
            if (resultado.status === 'success') {
                // Limpiamos el formulario y recargamos la lista
                document.getElementById('formVideo').reset();
                // Aumentamos el contador de orden automáticamente
                document.getElementById('orden_video').value = parseInt(datosVideo.orden) + 1; 
                cargarVideos(idCurso);
            } else {
                alert("Error: " + resultado.mensaje);
            }
        } catch (error) {
            console.error("Error al guardar vídeo:", error);
        }
    });
});

// Función para pintar la lista de vídeos
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
                lista.innerHTML += `
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span class="badge bg-secondary me-2">Lec. ${video.orden}</span>
                            <span class="fw-bold">${video.titulo}</span>
                            <br>
                            <a href="${video.url_youtube}" target="_blank" class="small text-muted text-decoration-none">
                                <i class="bi bi-link-45deg"></i> Ver enlace original
                            </a>
                        </div>
                        <button onclick="eliminarVideo(${video.id_video}, ${idCurso})" class="btn btn-sm btn-outline-danger">
                            <i class="bi bi-trash"></i>
                        </button>
                    </li>
                `;
            });
        } else {
            lista.innerHTML = '<li class="list-group-item text-muted text-center">No hay vídeos subidos todavía.</li>';
        }
    } catch (error) {
        console.error("Error al cargar vídeos:", error);
    }
}

// Función para borrar un vídeo
async function eliminarVideo(idVideo, idCurso) {
    if (confirm('¿Seguro que quieres borrar esta lección?')) {
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