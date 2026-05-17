document.addEventListener('DOMContentLoaded', async function () {
    const idCurso = document.getElementById('id_curso_oculto').value;

    if (!idCurso) {
        alert("Error: No se encontró el ID del curso.");
        window.location.href = 'mis_cursos.php';
        return;
    }

    // 1. CARGAR TEMARIO Y VÍDEOS
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

                // Limpiamos la descripción de saltos de línea para evitar errores de sintaxis en JS
                const descLimpia = (video.descripcion || "Sin descripción adicional.").replace(/(\r\n|\n|\r)/gm, " ");

                // AÑADIDO: Pasamos video.id_video y la descripción a cambiarVideo
                listaLecciones.innerHTML += `
                    <button onclick="cambiarVideo('${urlEmbed}', '${video.titulo.replace(/'/g, "\\'")}', this, ${video.id_video}, '${descLimpia.replace(/'/g, "\\'")}')" 
                            class="list-group-item list-group-item-action text-dark py-3 boton-leccion ${claseActivo}">
                        <span class="badge bg-paideia me-2">${video.orden}</span> ${video.titulo}
                    </button>
                `;

                // Si es el primer vídeo, lo cargamos automáticamente
                if (index === 0) {
                    // Usamos un pequeño timeout para asegurar que el botón se ha renderizado antes de seleccionarlo
                    setTimeout(() => {
                        cambiarVideo(urlEmbed, video.titulo, listaLecciones.firstElementChild, video.id_video, descLimpia);
                    }, 50);
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


    // ==========================================
    // LÓGICA DEL FORO DE DUDAS (COMENTARIOS)
    // ==========================================
    const btnEnviarComentario = document.getElementById('btn-enviar-comentario');
    if (btnEnviarComentario) {
        btnEnviarComentario.addEventListener('click', async function () {
            const idVideo = document.getElementById('id_video_actual').value;
            const texto = document.getElementById('texto-nuevo-comentario').value.trim();

            if (!texto || !idVideo) return;

            btnEnviarComentario.disabled = true;
            try {
                const res = await fetch(BASE_URL + 'app/api/foro_video.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_video: idVideo, texto: texto })
                });
                const data = await res.json();
                if (data.status === 'success') {
                    document.getElementById('texto-nuevo-comentario').value = '';
                    cargarForo(idVideo); // Recargar comentarios
                } else {
                    alert(data.mensaje);
                }
            } catch (e) {
                console.error(e);
            } finally {
                btnEnviarComentario.disabled = false;
            }
        });
    }

    // Lógica para el botón "Responder" de cada comentario
    const contenedorComentarios = document.getElementById('contenedor-comentarios');
    if (contenedorComentarios) {
        contenedorComentarios.addEventListener('click', async function (e) {
            if (e.target.classList.contains('btn-responder-duda')) {
                const idPadre = e.target.getAttribute('data-id');
                const idVideo = document.getElementById('id_video_actual').value;
                const contenedorResp = document.getElementById(`caja-respuesta-${idPadre}`);

                // Si ya está abierto, lo mandamos. Si no, lo mostramos.
                if (contenedorResp.classList.contains('d-none')) {
                    contenedorResp.classList.remove('d-none');
                } else {
                    const texto = document.getElementById(`input-respuesta-${idPadre}`).value.trim();
                    if (!texto) return;

                    try {
                        const res = await fetch(BASE_URL + 'app/api/foro_video.php', {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({ id_video: idVideo, texto: texto, id_padre: idPadre })
                        });
                        const data = await res.json();
                        if (data.status === 'success') {
                            cargarForo(idVideo);
                        }
                    } catch (err) { }
                }
            }
        });
    }

    // ==========================================
    // LÓGICA DE VALORACIÓN DEL VÍDEO INDIVIDUAL
    // ==========================================
    const contenedorEstrellasVideo = document.getElementById('estrellas-video');
    if (contenedorEstrellasVideo) {
        contenedorEstrellasVideo.addEventListener('click', async function (e) {
            if (e.target.tagName === 'I') {
                const puntuacion = e.target.getAttribute('data-value');
                const idVideo = document.getElementById('id_video_actual').value;
                const feedbackVideo = document.getElementById('feedback-video');

                pintarEstrellasVideo(puntuacion);

                try {
                    const res = await fetch(BASE_URL + 'app/api/foro_video.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ accion: 'valorar', id_video: idVideo, estrellas: puntuacion })
                    });
                    const data = await res.json();
                    if (data.status === 'success') {
                        feedbackVideo.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill"></i> Guardado</span>`;
                        setTimeout(() => feedbackVideo.innerHTML = '', 2000);
                    }
                } catch (err) { }
            }
        });
    }

    // ==========================================
    // LÓGICA DEL MODAL DE CURSO GENERAL (INTACTO)
    // ==========================================
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
            } catch (error) { }
        });
    }

    if (contenedorEstrellas) {
        contenedorEstrellas.addEventListener('click', function (e) {
            if (e.target.tagName === 'I') {
                const puntuacion = e.target.getAttribute('data-value');
                contenedorEstrellas.setAttribute('data-puntuacion', puntuacion);
                pintarEstrellas(puntuacion);
            }
        });
    }

    if (btnGuardar) {
        btnGuardar.addEventListener('click', async function () {
            const puntuacion = contenedorEstrellas.getAttribute('data-puntuacion');
            const texto = textoComentario.value.trim();

            if (puntuacion == 0) {
                feedback.innerText = "Por favor, selecciona al menos una estrella.";
                return;
            }

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

// ==========================================
// FUNCIONES AUXILIARES GLOBALES
// ==========================================

function pintarEstrellasVideo(puntuacion) {
    const iconos = document.querySelectorAll('#estrellas-video i');
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

function cambiarVideo(urlEmbed, titulo, boton, idVideo, descripcion = "") {
    document.getElementById('reproductor-youtube').src = urlEmbed;
    document.getElementById('titulo-leccion-actual').innerText = titulo;

    // Título del foro y descripción del vídeo
    const foroTitulo = document.getElementById('titulo-foro-actual');
    if (foroTitulo) foroTitulo.innerText = titulo;
    if (descripcion) document.getElementById('desc-leccion-actual').innerText = descripcion;

    const botones = document.querySelectorAll('.boton-leccion');
    botones.forEach(btn => {
        btn.classList.remove('bg-light', 'border-primary', 'fw-bold');
        btn.classList.add('boton-leccion-light', 'border-transparent');
    });

    // Si la llamada viene de inicializar la página puede que el botón no exista aún
    if (boton) {
        boton.classList.remove('boton-leccion-light', 'border-transparent');
        boton.classList.add('bg-light', 'border-primary', 'fw-bold');
    }

    // Registrar el id del vídeo y mostrar las secciones
    const idVideoActual = document.getElementById('id_video_actual');
    if (idVideoActual) {
        idVideoActual.value = idVideo;
        document.getElementById('bloque-estrellas-video').classList.remove('d-none');
        document.getElementById('bloque-foro-video').classList.remove('d-none');

        cargarNotaVideo(idVideo);
        cargarForo(idVideo);
    }
}

async function cargarNotaVideo(idVideo) {
    pintarEstrellasVideo(0);
    document.getElementById('feedback-video').innerHTML = '';
    try {
        const res = await fetch(BASE_URL + `app/api/foro_video.php?id_video=${idVideo}&accion=mivoto`);
        const json = await res.json();
        if (json.status === 'success' && json.data.estrellas) {
            pintarEstrellasVideo(json.data.estrellas);
        }
    } catch (err) { }
}

async function cargarForo(idVideo) {
    const contenedor = document.getElementById('contenedor-comentarios');
    if (!contenedor) return;

    contenedor.innerHTML = '<div class="text-center text-muted py-3"><div class="spinner-border spinner-border-sm text-primary" role="status"></div> Cargando dudas...</div>';

    try {
        const res = await fetch(BASE_URL + `app/api/foro_video.php?id_video=${idVideo}`);
        const json = await res.json();

        if (json.status === 'success') {
            const foro = json.data;
            if (foro.length === 0) {
                contenedor.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-chat-square-text fs-2 d-block mb-2"></i> No hay dudas sobre esta lección todavía. ¡Sé el primero!</div>';
                return;
            }

            let htmlForo = '';
            foro.forEach(com => {
                const badgeProfe = com.nombre_rol === 'profesor' ? '<span class="badge bg-primary ms-2 small">Profesor</span>' : '';

                let htmlRespuestas = '';
                if (com.respuestas && com.respuestas.length > 0) {
                    com.respuestas.forEach(resp => {
                        const badgeProfeResp = resp.nombre_rol === 'profesor' ? '<span class="badge bg-primary ms-2 small">Profesor</span>' : '';
                        htmlRespuestas += `
                            <div class="d-flex mt-3 pt-3 border-top border-light">
                                <div class="me-2"><i class="bi bi-person-circle fs-4 text-secondary"></i></div>
                                <div class="flex-grow-1 bg-white p-2 rounded border border-light">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="small text-dark">${resp.nombre} ${resp.apellidos} ${badgeProfeResp}</strong>
                                        <small class="text-muted" style="font-size: 0.7rem;">${resp.fecha}</small>
                                    </div>
                                    <p class="mb-0 small text-secondary">${resp.texto}</p>
                                </div>
                            </div>
                        `;
                    });
                }

                htmlForo += `
                    <div class="d-flex mb-4">
                        <div class="me-3">
                            <i class="bi bi-person-circle fs-2 text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="bg-white p-3 rounded shadow-sm border border-light">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong class="text-dark">${com.nombre} ${com.apellidos} ${badgeProfe}</strong>
                                    <small class="text-muted" style="font-size: 0.8rem;">${com.fecha}</small>
                                </div>
                                <p class="mb-2 text-secondary">${com.texto}</p>
                                
                                <div class="d-flex align-items-center mt-2">
                                    <button class="btn btn-sm btn-outline-secondary me-2 btn-responder-duda" data-id="${com.id_comentario}">
                                        <i class="bi bi-reply-fill"></i> Responder
                                    </button>
                                </div>

                                <div id="caja-respuesta-${com.id_comentario}" class="d-none mt-2 d-flex">
                                    <input type="text" id="input-respuesta-${com.id_comentario}" class="form-control form-control-sm me-2" placeholder="Escribe tu respuesta...">
                                    <button class="btn btn-sm btn-primary btn-responder-duda" data-id="${com.id_comentario}">Enviar</button>
                                </div>

                                <div class="ms-3 mt-2">
                                    ${htmlRespuestas}
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            contenedor.innerHTML = htmlForo;
        }
    } catch (err) {
        contenedor.innerHTML = '<div class="text-danger small">Error al cargar el foro.</div>';
    }
}