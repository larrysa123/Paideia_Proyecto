const API_URL = BASE_URL + 'app/api/cursos.php';

async function cargarCursos() {
    const contenedor = document.getElementById('contenedor-cursos');

    try {
        const respuesta = await fetch(API_URL);
        if (!respuesta.ok) throw new Error('Error en la red');

        const resultado = await respuesta.json();
        contenedor.innerHTML = '';

        if (resultado.status === 'error') {
            contenedor.innerHTML = `<div class="col-12 text-center py-5"><h3 class="text-muted fw-light">${resultado.mensaje}</h3></div>`;
            return;
        }

        const cursos = resultado.data;

        cursos.forEach(curso => {
            const rutaImagen = curso.imagen ? `assets/img/cursos/${curso.imagen}` : 'https://via.placeholder.com/400x225?text=Paideia+Curso';
            const descripcionCorta = curso.descripcion ? curso.descripcion : 'Sin descripción disponible.';

            // =========================================================
            // LÓGICA DE ESTRELLAS LEYENDO valoracion_media
            // =========================================================
            const media = parseFloat(curso.valoracion_media) || 0;
            const totalVotos = parseInt(curso.total_votos) || 0;
            let estrellasHTML = '';

            if (totalVotos > 0) {
                let iconosEstrellas = '';
                for (let i = 1; i <= 5; i++) {
                    if (media >= i) {
                        iconosEstrellas += '<i class="bi bi-star-fill text-warning small"></i>';
                    } else if (media >= i - 0.5) {
                        iconosEstrellas += '<i class="bi bi-star-half text-warning small"></i>';
                    } else {
                        iconosEstrellas += '<i class="bi bi-star text-warning small"></i>';
                    }
                }

                estrellasHTML = `
                    <div class="mb-2 d-flex align-items-center">
                        <span class="text-dark fw-bold me-1 small">${media.toFixed(1)}</span>
                        <div class="me-1">${iconosEstrellas}</div>
                        <span class="text-muted small">(${totalVotos})</span>
                    </div>
                `;
            } else {
                estrellasHTML = `
                    <div class="mb-2 text-muted small">
                        <span class="badge bg-light text-dark border"><i class="bi bi-stars text-warning"></i> Nuevo</span>
                    </div>
                `;
            }

            const htmlCurso = `
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="${rutaImagen}" class="card-img-top" alt="${curso.titulo}" style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title h5 fw-bold text-primary">${curso.titulo}</h3>
                            <p class="card-text text-secondary flex-grow-1 mb-2" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                ${descripcionCorta}
                            </p>
                            
                            ${estrellasHTML}

                            <div class="mt-2 d-flex justify-content-between align-items-center border-top pt-3">
                                <span class="precio-texto">${curso.precio} €</span>
                                <button onclick="verDetalle(${curso.id_curso})" class="btn btn-paideia rounded-pill px-4">
                                    VER CURSO
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            contenedor.innerHTML += htmlCurso;
        });

    } catch (error) {
        console.error('Error:', error);
        contenedor.innerHTML = '<div class="alert alert-danger text-center my-5">Error de conexión con la Academia.</div>';
    }
}

function verDetalle(id) {
    window.location.href = `vista/cursos/detalle.php?id=${id}`;
}

document.addEventListener('DOMContentLoaded', cargarCursos);