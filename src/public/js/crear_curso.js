document.addEventListener('DOMContentLoaded', function() {
    const formularioCrear = document.getElementById('formCrearCurso');

    if (formularioCrear) {
        formularioCrear.addEventListener('submit', async function(evento) {
            evento.preventDefault(); 
            
            // Creamos un objeto FormData para poder adjuntar archivos físicos
            const datosFormulario = new FormData(formularioCrear);

            // Efecto visual en el botón
            const btnSubmit = formularioCrear.querySelector('button[type="submit"]');
            const textoOriginal = btnSubmit.innerHTML;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Subiendo...';
            btnSubmit.disabled = true;

            try {
                // Al usar FormData NO enviamos el header Content-Type, el navegador lo calcula solo
                const respuesta = await fetch(BASE_URL + 'app/api/cursos.php', {
                    method: 'POST',
                    body: datosFormulario
                });

                const datos = await respuesta.json();

                if (datos.status === 'success') {
                    alert(datos.mensaje);
                    window.location.href = 'panel.php'; 
                } else {
                    alert("Error: " + datos.mensaje);
                }
            } catch (error) {
                console.error("Error de conexión:", error);
                alert("Hubo un error al intentar subir el curso.");
            } finally {
                btnSubmit.innerHTML = textoOriginal;
                btnSubmit.disabled = false;
            }
        });
    }
});