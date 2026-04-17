
// 1. Ajustamos la ruta relativa hacia nuestra nueva API MVC
const API_URL = BASE_URL + 'app/api/cursos.php';

async function cargarCursos() {
    const contenedor = document.getElementById('contenedor-cursos');
    
    try {
        const respuesta = await fetch(API_URL);
        
        if (!respuesta.ok) throw new Error('Error en la red');
        
        // 2. Leemos el "paquete" JSON que nos manda el servidor
        const resultado = await respuesta.json();

        contenedor.innerHTML = ''; // Limpiamos el spinner de carga

        // 3. Comprobamos si el servidor nos devuelve un error (ej: no hay cursos)
        if (resultado.status === 'error') {
            contenedor.innerHTML = `<div class="col-12 text-center py-5"><h3 class="text-muted fw-light">${resultado.mensaje}</h3></div>`;
            return;
        }

        // 4. Si hay éxito, cogemos el array de cursos que viene dentro de "data"
        const cursos = resultado.data;
        
        cursos.forEach(curso => {
            // Protección: Si no hay imagen en BD, ponemos una por defecto
            const rutaImagen = curso.imagen ? `assets/img/cursos/${curso.imagen}` : 'https://via.placeholder.com/400x225?text=Paideia+Curso';
            const descripcionCorta = curso.descripcion ? curso.descripcion : 'Sin descripción disponible.';

            // Construimos la tarjeta con tus clases CSS personalizadas
            const htmlCurso = `
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <img src="${rutaImagen}" class="card-img-top" alt="${curso.titulo}" style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title h5 fw-bold text-primary">${curso.titulo}</h3>
                            <p class="card-text text-secondary flex-grow-1" style="display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden;">
                                ${descripcionCorta}
                            </p>
                            <div class="mt-3 d-flex justify-content-between align-items-center border-top pt-3">
                                <span class="precio-texto">${curso.precio} €</span>
                                <button onclick="verDetalle(${curso.id_curso})" class="btn btn-paideia rounded-pill px-4">
                                    INSCRIBIRSE
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

// Función real para cuando el usuario hace clic en inscribirse
function verDetalle(id) {
    // Redirigimos a la página de detalle pasando el ID por la URL
    window.location.href = `vista/cursos/detalle.php?id=${id}`;
}

// Ejecutar la función cargarCursos en cuanto el HTML esté listo
document.addEventListener('DOMContentLoaded', cargarCursos);