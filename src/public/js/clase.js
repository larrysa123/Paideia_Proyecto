document.addEventListener('DOMContentLoaded', async function () {
    const idCurso = document.getElementById('id_curso_oculto').value;

    if (!idCurso) {
        alert("Error: No se encontró el ID del curso.");
        window.location.href = 'mis_cursos.php';
        return;
    }

    try {
        const respuesta = await fetch(BASE_URL + 'app/api/videos.php?id_curso=' + idCurso);
        const resultado = await respuesta.json();

        document.getElementById('cargando-temario').classList.add('d-none');
        const listaLecciones = document.getElementById('lista-lecciones');

        if (resultado.status === 'success' && resultado.data.length > 0) {
            const videos = resultado.data;
            videos.forEach((video, index) => {
                let urlEmbed = video.url_youtube;
                if (urlEmbed.includes('watch?v=')) urlEmbed = urlEmbed.replace('watch?v=', 'embed/');
                else if (urlEmbed.includes('youtu.be/')) urlEmbed = urlEmbed.replace('youtu.be/', 'youtube.com/embed/');

                const claseActivo = index === 0 ? 'bg-light border-start border-primary border-4 fw-bold' : 'boton-leccion-light border-start border-transparent border-4';

                listaLecciones.innerHTML += `
                    <button onclick="cambiarVideo('${urlEmbed}', '${video.titulo.replace(/'/g, "\\'")}', this)" 
                            class="list-group-item list-group-item-action text-dark py-3 boton-leccion ${claseActivo}">
                        <span class="badge bg-paideia me-2">${video.orden}</span> ${video.titulo}
                    </button>
                `;

                if (index === 0) {
                    document.getElementById('reproductor-youtube').src = urlEmbed;
                    document.getElementById('titulo-leccion-actual').innerText = video.titulo;
                    document.getElementById('desc-leccion-actual').innerText = video.descripcion || "Sin descripción adicional.";
                }
            });
        } else {
            document.getElementById('titulo-leccion-actual').innerText = "No hay lecciones disponibles";
            document.getElementById('desc-leccion-actual').innerText = "Vuelve más tarde cuando el profesor añada el temario.";
            listaLecciones.innerHTML = `<div class="text-center text-muted p-4"><i class="bi bi-camera-video-off fs-1 d-block mb-3"></i><p>Sin vídeos.</p></div>`;
        }
    } catch (error) {
        document.getElementById('cargando-temario').innerHTML = '<div class="alert alert-danger m-3">Error de conexión con el servidor.</div>';
    }

    // --- Lógica del Modal de Valoración ---
    const modalValoracion = document.getElementById('modalValoracion');
    const contenedorEstrellas = document.getElementById('modal-estrellas-curso');
    const textoComentario = document.getElementById('modal-texto-curso');
    const btnGuardar = document.getElementById('btn-guardar-resena');
    const feedback = document.getElementById('modal-feedback');

    function pintarEstrellas(puntuacion) {
        const iconos = contenedorEstrellas.querySelectorAll('i');
        iconos.forEach((icono, index) => {
            if (index < puntuacion) {
                icono.classList.remove('bi-star');
                icono.classList.add('bi-star-fill');
            } else {
                icono.classList.remove('bi-star-fill');
                icono.classList.add('bi-star');
            }
        });
    }

    if (modalValoracion) {
        modalValoracion.addEventListener('show.bs.modal', async function () {
            feedback.innerHTML = ''; 
            try {
                const res = await fetch(BASE_URL + 'app/api/valoraciones.php?id_curso=' + idCurso + '&accion=miresena');
                const json = await res.json();
                if (json.status === 'success' && json.data) {
                    textoComentario.value = json.data.texto || '';
                    const estrellasPrevias = json.data.estrellas || 0;
                    contenedorEstrellas.setAttribute('data-puntuacion', estrellasPrevias);
                    pintarEstrellas(estrellasPrevias);
                }
            } catch (error) {}
        });
    }

    if (contenedorEstrellas) {
        contenedorEstrellas.addEventListener('click', function(e) {
            if (e.target.tagName === 'I') {
                const puntuacion = e.target.getAttribute('data-value');
                contenedorEstrellas.setAttribute('data-puntuacion', puntuacion);
                pintarEstrellas(puntuacion);
            }
        });
    }

    if (btnGuardar) {
        btnGuardar.addEventListener('click', async function() {
            const puntuacion = contenedorEstrellas.getAttribute('data-puntuacion');
            const texto = textoComentario.value.trim();

            if (puntuacion == 0) {
                feedback.innerText = "Por favor, selecciona al menos una estrella.";
                return;
            }
            // ¡Ya no bloqueamos si el texto está vacío!

            btnGuardar.disabled = true;
            btnGuardar.innerText = "Guardando...";

            try {
                const res = await fetch(BASE_URL + 'app/api/valoraciones.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_curso: idCurso, estrellas: puntuacion, texto: texto })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    const modalInstance = bootstrap.Modal.getInstance(modalValoracion);
                    modalInstance.hide();
                    alert("¡Gracias por tu valoración!");
                } else {
                    feedback.innerText = data.mensaje;
                }
            } catch (error) {
                feedback.innerText = "Error de conexión con el servidor.";
            } finally {
                btnGuardar.disabled = false;
                btnGuardar.innerText = "Guardar Valoración";
            }
        });
    }
});

function cambiarVideo(urlEmbed, titulo, boton) {
    document.getElementById('reproductor-youtube').src = urlEmbed;
    document.getElementById('titulo-leccion-actual').innerText = titulo;
    const botones = document.querySelectorAll('.boton-leccion');
    botones.forEach(btn => {
        btn.classList.remove('bg-light', 'border-primary', 'fw-bold');
        btn.classList.add('boton-leccion-light', 'border-transparent');
    });
    boton.classList.remove('boton-leccion-light', 'border-transparent');
    boton.classList.add('bg-light', 'border-primary', 'fw-bold');
}