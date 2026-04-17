document.addEventListener('DOMContentLoaded', async function() {
    // 1. Obtener el ID del curso desde la URL (ej: editar_curso.php?id=5)
    const urlParams = new URLSearchParams(window.location.search);
    const idCurso = urlParams.get('id');

    if (!idCurso) {
        alert("ID de curso no encontrado.");
        window.location.href = 'panel.php';
        return;
    }

    // 2. FASE DE PRECARGA: Pedir los datos actuales a la API
    try {
        const respuesta = await fetch(BASE_URL + 'app/api/cursos.php?id=' + idCurso);
        const resultado = await respuesta.json();

        if (resultado.status === 'success') {
            const curso = resultado.data;
            // Rellenamos el formulario con lo que hay en la BD
            document.getElementById('id_curso').value = curso.id_curso;
            document.getElementById('titulo').value = curso.titulo;
            document.getElementById('descripcion').value = curso.descripcion;
            document.getElementById('precio').value = curso.precio;
            document.getElementById('imagen').value = curso.imagen;
        } else {
            alert("Error al cargar el curso: " + resultado.mensaje);
        }
    } catch (error) {
        console.error("Error en precarga:", error);
    }

    // 3. FASE DE GUARDADO: Escuchar cuando el usuario pulse "Guardar Cambios"
    const formulario = document.getElementById('formEditarCurso');
    formulario.addEventListener('submit', async function(e) {
        e.preventDefault();

        const datosModificados = {
            id_curso: document.getElementById('id_curso').value,
            titulo: document.getElementById('titulo').value,
            descripcion: document.getElementById('descripcion').value,
            precio: document.getElementById('precio').value,
            imagen: document.getElementById('imagen').value
        };

        try {
            // Enviamos por método PUT (Actualizar)
            const res = await fetch(BASE_URL + 'app/api/cursos.php', {
                method: 'PUT',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datosModificados)
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
        }
    });
});