document.getElementById('formRegistro').addEventListener('submit', async function(evento) {
    evento.preventDefault(); // Evitamos que la página se recargue

    // 1. Recogemos los valores de todos los inputs
    const datosUsuario = {
        nombre: document.getElementById('nombre').value,
        apellidos: document.getElementById('apellidos').value,
        email: document.getElementById('email').value,
        password: document.getElementById('password').value
    };

    try {
        // 2. Enviamos el paquete completo a la API
        const respuesta = await fetch(BASE_URL + 'app/api/registro.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(datosUsuario) // Convertimos el objeto a texto JSON
        });

        // 3. Leemos lo que nos devuelve el servidor
        const datos = await respuesta.json();

        // 4. Mostramos el mensaje y cambiamos el color según si ha ido bien o mal
        const parrafoRespuesta = document.getElementById('respuestaServidor');
        parrafoRespuesta.innerText = datos.mensaje;
        
        if (datos.status === 'success') {
            parrafoRespuesta.style.color = 'green';
            // Aquí en el futuro podríamos redirigir al login: window.location.href = 'login.php';
        } else {
            parrafoRespuesta.style.color = 'red';
        }

    } catch (error) {
        console.error("Hubo un error de conexión:", error);
    }
});