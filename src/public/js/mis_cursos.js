document.addEventListener('DOMContentLoaded', async function () {
    // 1. CARGAR TARJETAS DE CURSOS
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

                let bloqueValoracionHTML = '';

                if (curso.mi_nota && curso.mi_nota > 0) {
                    let estrellasDoradas = '';
                    for (let i = 1; i <= 5; i++) {
                        if (i <= curso.mi_nota) {
                            estrellasDoradas += `<i class="bi bi-star-fill text-warning me-1"></i>`;
                        } else {
                            estrellasDoradas += `<i class="bi bi-star text-warning me-1"></i>`;
                        }
                    }
                    bloqueValoracionHTML = `
                        <div class="btn-abrir-modal-valoracion cursor-pointer" data-id="${curso.id_curso}" style="cursor: pointer;">
                            ${estrellasDoradas} <span class="small text-muted">(Tu nota)</span>
                        </div>`;
                } else {
                    bloqueValoracionHTML = `
                        <a href="#" class="text-warning text-decoration-none small fw-bold btn-abrir-modal-valoracion" data-id="${curso.id_curso}">
                            <i class="bi bi-star-fill"></i> Valorar curso
                        </a>`;
                }

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
                                    <div class="progress mb-3" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: ${progreso}%"></div>
                                    </div>
                                    <div class="text-center" style="min-height: 24px;">
                                        ${bloqueValoracionHTML}
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

    // 2. LÓGICA DEL MODAL DE ESTRELLAS
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

    document.addEventListener('click', async function (e) {
        const btnValorar = e.target.closest('.btn-abrir-modal-valoracion');
        if (btnValorar) {
            e.preventDefault();
            const idCurso = btnValorar.getAttribute('data-id');

            const inputOculto = document.getElementById('modal_id_curso_oculto') || document.getElementById('id_curso_oculto');
            if (inputOculto) inputOculto.value = idCurso;

            feedback.innerText = '';
            textoComentario.value = '';
            contenedorEstrellas.setAttribute('data-puntuacion', 0);
            pintarEstrellasModal(0);

            try {
                const res = await fetch(BASE_URL + 'app/api/valoraciones.php?id_curso=' + idCurso + '&accion=miresena');
                const json = await res.json();
                if (json.status === 'success' && json.data) {
                    textoComentario.value = json.data.texto || '';
                    const estrellasPrevias = json.data.estrellas || 0;
                    contenedorEstrellas.setAttribute('data-puntuacion', estrellasPrevias);
                    pintarEstrellasModal(estrellasPrevias);
                }
            } catch (err) { }

            const modalEl = document.getElementById('modalValoracion');
            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        }
    });

    if (contenedorEstrellas) {
        contenedorEstrellas.addEventListener('click', function (e) {
            if (e.target.tagName === 'I') {
                const puntuacion = e.target.getAttribute('data-value');
                contenedorEstrellas.setAttribute('data-puntuacion', puntuacion);
                pintarEstrellasModal(puntuacion);
            }
        });
    }

    if (btnGuardar) {
        btnGuardar.addEventListener('click', async function () {
            const inputOculto = document.getElementById('modal_id_curso_oculto') || document.getElementById('id_curso_oculto');
            const idCurso = inputOculto ? inputOculto.value : '';
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
                    window.location.reload();
                } else {
                    feedback.innerText = data.mensaje;
                    btnGuardar.disabled = false;
                    btnGuardar.innerText = "Guardar Reseña";
                }
            } catch (err) {
                feedback.innerText = "Error de red.";
                btnGuardar.disabled = false;
                btnGuardar.innerText = "Guardar Reseña";
            }
        });
    }

    // =========================================================
    // NUEVA LÓGICA: CARGAR HISTORIAL DE COMPRAS
    // =========================================================
    const tabHistorial = document.getElementById('historial-tab');
    if (tabHistorial) {
        tabHistorial.addEventListener('click', async function() {
            const tbody = document.getElementById('tabla-historial');
            
            // Si ya tiene contenido (y no es el loader), no volvemos a llamar a la BD
            if (tbody.children.length > 1 || !tbody.innerHTML.includes('spinner')) return;

            try {
                const res = await fetch(BASE_URL + 'app/api/pedidos.php?accion=historial');
                const json = await res.json();

                if (json.status === 'success') {
                    const recibos = json.data;
                    tbody.innerHTML = ''; // Limpiar loader

                    if (recibos.length === 0) {
                        tbody.innerHTML = '<tr><td colspan="5" class="text-center py-4 text-muted">Aún no tienes recibos de compra.</td></tr>';
                        return;
                    }

                    recibos.forEach(r => {
                        // Formateamos el ID 
                        const idFormat = '#' + r.id_pedido.toString().padStart(4, '0');
                        
                        tbody.innerHTML += `
                            <tr>
                                <td class="text-muted fw-bold">${idFormat}</td>
                                <td>${r.fecha}</td>
                                <td class="text-dark fw-bold">${r.curso_titulo}</td>
                                <td><span class="badge bg-secondary"><i class="bi bi-credit-card me-1"></i>${r.metodo_pago}</span></td>
                                <td class="text-end text-success fw-bold">${r.total} €</td>
                            </tr>
                        `;
                    });
                } else {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-danger">${json.mensaje}</td></tr>`;
                }
            } catch (err) {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error al cargar el historial.</td></tr>';
            }
        });
    }
});

function irALeccion(id) {
    window.location.href = `clase.php?id=${id}`;
}