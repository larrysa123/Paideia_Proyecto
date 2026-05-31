document.addEventListener('DOMContentLoaded', async function () {
    // Obtener el ID del curso desde la URL (ej: editar_curso.php?id=5)
    const urlParams = new URLSearchParams(window.location.search);
    const idCurso = urlParams.get('id');

    if (!idCurso) {
        alert("ID de curso no encontrado.");
        window.location.href = 'panel.php';
        return;
    }

    // FASE DE PRECARGA
    try {
        // Ruta corregida al Alias de Apache
        const respuesta = await fetch('/api/cursos.php?id=' + idCurso);
        const resultado = await respuesta.json();

        if (resultado.status === 'success') {
            const curso = resultado.data;

            // Actualizamos el título de la página dinámicamente con el nombre del curso
            document.getElementById('titulo-pagina-editar').innerText = 'Editando: ' + curso.titulo;

            // Rellenamos el formulario con lo que hay en la BD
            document.getElementById('id_curso').value = curso.id_curso;
            document.getElementById('titulo').value = curso.titulo;
            document.getElementById('descripcion').value = curso.descripcion;
            document.getElementById('precio').value = curso.precio;

            // Mostramos el nombre de la imagen al profesor y lo guardamos en el input oculto
            document.getElementById('nombre_imagen_actual').innerText = curso.imagen ? curso.imagen : 'Ninguna';
            document.getElementById('imagen_actual').value = curso.imagen || '';
        } else {
            alert("Error al cargar el curso: " + resultado.mensaje);
        }
    } catch (error) {
        console.error("Error en precarga:", error);
    }

    // FASE DE GUARDADO
    const formulario = document.getElementById('formEditarCurso');
    formulario.addEventListener('submit', async function (e) {
        e.preventDefault();

        // Empaquetamos archivos y textos juntos
        const datosFormulario = new FormData(formulario);

        const btnSubmit = formulario.querySelector('button[type="submit"]');
        const textoOriginal = btnSubmit.innerHTML;
        btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Guardando...';
        btnSubmit.disabled = true;

        try {
            // Usamos POST apuntando al Alias para que PHP procese el FormData con la imagen
            const res = await fetch('/api/cursos.php', {
                method: 'POST',
                body: datosFormulario
            });

            const final = await res.json();

            if (final.status === 'success') {
                alert(final.mensaje);
                window.location.href = 'panel.php';
            } else {
                alert("Error: " + final.mensaje);
            }
        } catch (error) {
            console.error("Error al actualizar:", error);
            alert("Hubo un error al intentar guardar los cambios.");
        } finally {
            btnSubmit.innerHTML = textoOriginal;
            btnSubmit.disabled = false;
        }
    });
});