document.getElementById('formLogin').addEventListener('submit', async function (evento) {
    evento.preventDefault();

    // Recogemos los valores
    const datosLogin = {
        email: document.getElementById('email').value,
        password: document.getElementById('password').value
    };

    try {
        // Ruta limpia al alias de Apache (sin BASE_URL)
        const respuesta = await fetch('/api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datosLogin)
        });

        const datos = await respuesta.json();

        const parrafoRespuesta = document.getElementById('respuestaServidor');
        parrafoRespuesta.innerText = datos.mensaje;

        if (datos.status === 'success') {
            parrafoRespuesta.style.color = 'green';

            // Redirección limpia a la raíz, eliminando dependencia de BASE_URL
            setTimeout(() => {
                window.location.href = '/index.php';
            }, 1500);
        } else {
            parrafoRespuesta.style.color = 'red';
        }
    } catch (error) {
        console.error("Hubo un error de conexión", error);
        document.getElementById('respuestaServidor').innerText = "Error de conexión con el servidor.";
    }
});