// 1. Esperamos a que la página cargue por completo
document.addEventListener('DOMContentLoaded', function() {
    
    // 2. Buscamos el formulario
    const formularioCrear = document.getElementById('formCrearCurso');

    // 3. ESCUDO: Solo ejecutamos esto si el formulario existe en la página actual
    if (formularioCrear) {
        
        formularioCrear.addEventListener('submit', async function(evento) {
            evento.preventDefault(); 

            // Recogemos los datos del formulario de creación
            const datosCurso = {
                titulo: document.getElementById('titulo').value,
                descripcion: document.getElementById('descripcion').value,
                precio: document.getElementById('precio').value,
                imagen: document.getElementById('imagen').value
            };

            try {
                // Enviamos a la API
                const respuesta = await fetch(BASE_URL + 'app/api/cursos.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(datosCurso)
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
            }
        });
    }
});