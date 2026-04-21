document.addEventListener('DOMContentLoaded', async function() {
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
            
            document.getElementById('titulo-curso-clase').innerText = "Reproduciendo lecciones...";

            videos.forEach((video, index) => {
                // 3. Truco: Convertir URL normal de YouTube a URL "Embed" para iframes
                let urlEmbed = video.url_youtube;
                if (urlEmbed.includes('watch?v=')) {
                    urlEmbed = urlEmbed.replace('watch?v=', 'embed/');
                } else if (urlEmbed.includes('youtu.be/')) {
                    urlEmbed = urlEmbed.replace('youtu.be/', 'youtube.com/embed/');
                }

                // 4. Marcamos la primera lección visualmente como "activa" por defecto
                // Usamos un fondo claro y un borde grueso azul (border-primary) a la izquierda
                const claseActivo = index === 0 ? 'bg-light border-start border-primary border-4 fw-bold' : 'boton-leccion-light border-start border-transparent border-4';
                
                listaLecciones.innerHTML += `
                    <button onclick="cambiarVideo('${urlEmbed}', '${video.titulo.replace(/'/g, "\\'")}', this)" 
                            class="list-group-item list-group-item-action text-dark py-3 boton-leccion ${claseActivo}">
                        <span class="badge bg-paideia me-2">${video.orden}</span> 
                        ${video.titulo}
                    </button>
                `;

                // 5. Autoplay: Cargamos el primer vídeo en el reproductor automáticamente
                if (index === 0) {
                    document.getElementById('reproductor-youtube').src = urlEmbed;
                    document.getElementById('titulo-leccion-actual').innerText = video.titulo;
                    document.getElementById('desc-leccion-actual').innerText = video.descripcion || "Sin descripción adicional.";
                }
            });

        } else {
            // Si el curso no tiene vídeos todavía
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

// Función para cambiar de vídeo cuando el alumno hace clic en el temario
function cambiarVideo(urlEmbed, titulo, boton) {
    // 1. Cambiamos el vídeo y el título principal
    document.getElementById('reproductor-youtube').src = urlEmbed;
    document.getElementById('titulo-leccion-actual').innerText = titulo;

    // 2. Quitamos el diseño de "activo" a todos los botones
    const botones = document.querySelectorAll('.boton-leccion');
    botones.forEach(btn => {
        btn.classList.remove('bg-light', 'border-primary', 'fw-bold');
        btn.classList.add('boton-leccion-light', 'border-transparent');
    });

    // 3. Le ponemos el diseño de "activo" solo al botón que acabamos de pulsar
    boton.classList.remove('boton-leccion-light', 'border-transparent');
    boton.classList.add('bg-light', 'border-primary', 'fw-bold');
}