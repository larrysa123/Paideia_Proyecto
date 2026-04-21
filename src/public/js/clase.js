document.addEventListener('DOMContentLoaded', async function () {
    const idCurso = document.getElementById('id_curso_oculto').value;

    if (!idCurso) {
        alert("Error: No se encontró el ID del curso.");
        window.location.href = 'mis_cursos.php';
        return;
    }

    try {
        // 1. Pedimos los vídeos a nuestra API
        const respuesta = await fetch(BASE_URL + 'app/api/videos.php?id_curso=' + idCurso);
        const resultado = await respuesta.json();

        // 2. Ocultamos el spinner de carga
        document.getElementById('cargando-temario').classList.add('d-none');

        const listaLecciones = document.getElementById('lista-lecciones');

        if (resultado.status === 'success' && resultado.data.length > 0) {
            const videos = resultado.data;

            videos.forEach((video, index) => {
                let urlEmbed = video.url_youtube;
                if (urlEmbed.includes('watch?v=')) {
                    urlEmbed = urlEmbed.replace('watch?v=', 'embed/');
                } else if (urlEmbed.includes('youtu.be/')) {
                    urlEmbed = urlEmbed.replace('youtu.be/', 'youtube.com/embed/');
                }

                const claseActivo = index === 0 ? 'bg-light border-start border-primary border-4 fw-bold' : 'boton-leccion-light border-start border-transparent border-4';

                listaLecciones.innerHTML += `
                    <button onclick="cambiarVideo('${urlEmbed}', '${video.titulo.replace(/'/g, "\\'")}', this)" 
                            class="list-group-item list-group-item-action text-dark py-3 boton-leccion ${claseActivo}">
                        <span class="badge bg-paideia me-2">${video.orden}</span> 
                        ${video.titulo}
                    </button>
                `;

                if (index === 0) {
                    document.getElementById('reproductor-youtube').src = urlEmbed;
                    document.getElementById('titulo-leccion-actual').innerText = video.titulo;
                    document.getElementById('desc-leccion-actual').innerText = video.descripcion || "Sin descripción adicional.";
                }
            });

        } else {
            // 3. Si no hay vídeos, avisamos en el centro
            document.getElementById('titulo-leccion-actual').innerText = "No hay lecciones disponibles";
            document.getElementById('desc-leccion-actual').innerText = "Vuelve más tarde cuando el profesor añada el temario.";

            listaLecciones.innerHTML = `
                <div class="text-center text-muted p-4">
                    <i class="bi bi-camera-video-off fs-1 d-block mb-3"></i>
                    <p>El profesor aún no ha subido vídeos a este curso.</p>
                </div>
            `;
        }

    } catch (error) {
        console.error("Error al cargar la clase:", error);
        document.getElementById('cargando-temario').innerHTML = '<div class="alert alert-danger m-3">Error de conexión con el servidor.</div>';
    }
});

function cambiarVideo(urlEmbed, titulo, boton) {
    document.getElementById('reproductor-youtube').src = urlEmbed;
    document.getElementById('titulo-leccion-actual').innerText = titulo;

    const botones = document.querySelectorAll('.boton-leccion');
    botones.forEach(btn => {
        btn.classList.remove('bg-light', 'border-primary', 'fw-bold'); A
        btn.classList.add('boton-leccion-light', 'border-transparent');
    });

    boton.classList.remove('boton-leccion-light', 'border-transparent');
    boton.classList.add('bg-light', 'border-primary', 'fw-bold');
}


// --- Lógica de Valoración de Estrellas ---
document.addEventListener('click', async function (e) {
    if (e.target.closest('#estrellas-curso i')) {
        const estrella = e.target;
        const puntuacion = estrella.getAttribute('data-value');
        const idCurso = document.getElementById('id_curso_oculto').value;

        // 1. Pintar las estrellas visualmente
        const todasLasEstrellas = document.querySelectorAll('#estrellas-curso i');
        todasLasEstrellas.forEach((s, index) => {
            if (index < puntuacion) {
                s.classList.remove('bi-star');
                s.classList.add('bi-star-fill');
            } else {
                s.classList.remove('bi-star-fill');
                s.classList.add('bi-star');
            }
        });

        // 2. Enviar a la API
        try {
            const res = await fetch(BASE_URL + 'app/api/valoraciones.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ id_curso: idCurso, estrellas: puntuacion })
            });
            const data = await res.json();

            const feedback = document.getElementById('feedback-valoracion');
            if (data.status === 'success') {
                feedback.innerHTML = `<span class="text-success"><i class="bi bi-check-circle-fill"></i> ¡Voto guardado!</span>`;
            } else {
                feedback.innerText = "Error al guardar.";
            }
        } catch (error) {
            console.error("Error al valorar:", error);
        }
    }
});