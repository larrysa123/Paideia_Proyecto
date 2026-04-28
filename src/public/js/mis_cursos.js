document.addEventListener('DOMContentLoaded', async function() {
    try {
        const respuesta = await fetch(BASE_URL + 'app/api/inscripciones.php');
        const resultado = await respuesta.json();

        const spinner = document.getElementById('cargando-mis-cursos');
        if (spinner) spinner.classList.add('d-none');

        if (resultado.status === 'success') {
            const cursos = resultado.data;
            const grid = document.getElementById('grid-mis-cursos');

            if (cursos.length === 0) {
                document.getElementById('mensaje-vacio').classList.remove('d-none');
                return;
            }

            cursos.forEach(curso => {
                const imgPath = curso.imagen ? `../../assets/img/cursos/${curso.imagen}` : 'https://via.placeholder.com/300x200';
                const progreso = curso.progreso || 0;

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
                                    <div class="progress mb-2" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: ${progreso}%"></div>
                                    </div>
                                    <div class="text-center">
                                        <a href="#" class="text-warning text-decoration-none small fw-bold btn-abrir-modal-valoracion" data-id="${curso.id_curso}">
                                            <i class="bi bi-star-fill"></i> Valorar curso
                                        </a>
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
        }
    } catch (error) {
        console.error("Error de fetch:", error);
    }

    // =========================================================
    // Lógica del Modal en Mis Cursos
    // =========================================================
    const contenedorEstrellas = document.getElementById('modal-estrellas-curso');
    const textoComentario = document.getElementById('modal-texto-curso');
    const btnGuardar = document.getElementById('btn-guardar-resena');
    const feedback = document.getElementById('modal-feedback');

    function pintarEstrellasModal(puntuacion) {
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

    // Delegación de eventos para abrir el modal al hacer clic en "Valorar curso" de cualquier tarjeta
    document.addEventListener('click', async function(e) {
        const btnValorar = e.target.closest('.btn-abrir-modal-valoracion');
        if (btnValorar) {
            e.preventDefault();
            const idCurso = btnValorar.getAttribute('data-id');
            document.getElementById('modal_id_curso_oculto').value = idCurso;

            // Limpiamos modal
            feedback.innerText = '';
            textoComentario.value = '';
            contenedorEstrellas.setAttribute('data-puntuacion', 0);
            pintarEstrellasModal(0);

            // Intentamos recuperar la reseña previa de este curso
            try {
                const res = await fetch(BASE_URL + 'app/api/valoraciones.php?id_curso=' + idCurso + '&accion=miresena');
                const json = await res.json();
                if (json.status === 'success' && json.data) {
                    textoComentario.value = json.data.texto || '';
                    const estrellasPrevias = json.data.estrellas || 0;
                    contenedorEstrellas.setAttribute('data-puntuacion', estrellasPrevias);
                    pintarEstrellasModal(estrellasPrevias);
                }
            } catch (err) {}

            // Abrimos el modal
            const modalEl = document.getElementById('modalValoracion');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });

    if (contenedorEstrellas) {
        contenedorEstrellas.addEventListener('click', function(e) {
            if (e.target.tagName === 'I') {
                const puntuacion = e.target.getAttribute('data-value');
                contenedorEstrellas.setAttribute('data-puntuacion', puntuacion);
                pintarEstrellasModal(puntuacion);
            }
        });
    }

    if (btnGuardar) {
        btnGuardar.addEventListener('click', async function() {
            const idCurso = document.getElementById('modal_id_curso_oculto').value;
            const puntuacion = contenedorEstrellas.getAttribute('data-puntuacion');
            const texto = textoComentario.value.trim();

            if (puntuacion == 0) {
                feedback.innerText = "Por favor, selecciona una puntuación.";
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
                    const modalEl = document.getElementById('modalValoracion');
                    const modalInstance = bootstrap.Modal.getInstance(modalEl);
                    modalInstance.hide();
                    alert("¡Gracias por tu valoración!");
                } else {
                    feedback.innerText = data.mensaje;
                }
            } catch (err) {
                feedback.innerText = "Error de red.";
            } finally {
                btnGuardar.disabled = false;
                btnGuardar.innerText = "Guardar Reseña";
            }
        });
    }
});

function irALeccion(id) {
    window.location.href = `clase.php?id=${id}`;
}