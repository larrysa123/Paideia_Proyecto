document.addEventListener('DOMContentLoaded', async function() {
    
    const urlParams = new URLSearchParams(window.location.search);
    const idCurso = urlParams.get('id');

    if (!idCurso) {
        alert("Curso no especificado.");
        window.location.href = '../../index.php';
        return;
    }

    // --- FASE 1: CARGAR DETALLES DEL CURSO ---
    try {
        const respuesta = await fetch(BASE_URL + 'app/api/cursos.php?id_publico=' + idCurso);
        const resultado = await respuesta.json();

        document.getElementById('cargando-detalle').classList.add('d-none');

        if (resultado.status === 'success') {
            const curso = resultado.data;
            const rutaImagen = curso.imagen ? `../../assets/img/cursos/${curso.imagen}` : 'https://via.placeholder.com/800x400?text=Paideia+Curso';

            document.getElementById('det-imagen').src = rutaImagen;
            document.getElementById('det-titulo').innerText = curso.titulo;
            document.getElementById('det-descripcion').innerText = curso.descripcion || 'Este curso no tiene descripción detallada aún.';
            document.getElementById('det-precio').innerText = curso.precio + ' €';
            
            document.getElementById('contenido-detalle').classList.remove('d-none');
        } else {
            document.getElementById('cargando-detalle').innerHTML = `<h3 class="text-danger">${resultado.mensaje}</h3>`;
            document.getElementById('cargando-detalle').classList.remove('d-none');
            return; // Si no hay curso, paramos aquí
        }

    } catch (error) {
        console.error("Error al cargar detalles:", error);
    }

    // --- FASE 2: LÓGICA DEL BOTÓN INSCRIBIRSE ---
    const btnInscribirse = document.getElementById('btn-inscribirse');
    
    // Si el botón existe en el HTML, le damos vida
    if (btnInscribirse) {
        btnInscribirse.addEventListener('click', async function() {
            
            // Efecto visual de "Cargando..."
            const textoOriginal = btnInscribirse.innerHTML;
            btnInscribirse.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Procesando...';
            btnInscribirse.disabled = true;

            try {
                // Llamamos a la nueva API de inscripciones
                const resInscripcion = await fetch(BASE_URL + 'app/api/inscripciones.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_curso: idCurso }) 
                });

                const final = await resInscripcion.json();

                if (final.status === 'success') {
                    // Si todo va bien, al panel del alumno
                    alert(final.mensaje);
                    window.location.href = '../alumno/mis_cursos.php'; 
                } else {
                    // Si hay error, leemos el código oculto para saber a dónde mandarlo
                    if (final.code === 'NO_LOGIN') {
                        window.location.href = '../login.php'; // Ajusta la ruta si login.php está en otra carpeta
                    } else if (final.code === 'YA_INSCRITO') {
                        alert(final.mensaje);
                        window.location.href = '../alumno/mis_cursos.php';
                    } else {
                        alert(final.mensaje);
                    }
                }
            } catch (error) {
                console.error("Error al inscribirse:", error);
                alert("Error de conexión al procesar la matrícula.");
            } finally {
                // Restauramos el botón
                btnInscribirse.innerHTML = textoOriginal;
                btnInscribirse.disabled = false;
            }
        });
    }
});