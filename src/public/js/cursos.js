// src/public/js/cursos.js

// Ajusta esta URL si tu carpeta se llama diferente
const API_URL = 'http://localhost/Paideia_Proyecto/src/api/cursos.php';

async function cargarCursos() {
    const contenedor = document.getElementById('contenedor-cursos');
    
    try {
        const respuesta = await fetch(API_URL);
        
        if (!respuesta.ok) throw new Error('Error en la red');
        
        const cursos = await respuesta.json();

        contenedor.innerHTML = ''; // Limpiar spinner

        if (cursos.length === 0) {
            contenedor.innerHTML = '<div class="col-12 text-center"><p class="text-muted">No hay cursos disponibles.</p></div>';
            return;
        }

        cursos.forEach(curso => {
            // Nota: Usamos las clases de Bootstrap y las nuestras (.btn-paideia, .precio-texto)
           const htmlCurso = `
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card h-100">
                        <img src="../assets/${curso.imagen}" class="card-img-top" alt="${curso.titulo}" style="height: 200px; object-fit: cover;">
                        <div class="card-body d-flex flex-column">
                            <h3 class="card-title h4">${curso.titulo}</h3>
                            <p class="card-text text-secondary flex-grow-1">${curso.descripcion}</p>
                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <span class="precio-texto">${curso.precio} €</span>
                                <button onclick="verDetalle(${curso.id_curso})" class="btn btn-paideia">
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
        contenedor.innerHTML = '<div class="alert alert-danger text-center">Error de conexión con la Academia.</div>';
    }
}

function verDetalle(id) {
    // Aquí redirigiremos al detalle más adelante
    alert('Navegando al curso ID: ' + id);
}

// Ejecutar al cargar
document.addEventListener('DOMContentLoaded', cargarCursos);