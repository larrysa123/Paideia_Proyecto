document.getElementById('formLogin').addEventListener('submit', async function(evento){
    evento.preventDefault(); 

//Recogemos los valores

const datosLogin = {
    email: document.getElementById('email').value,
    password: document.getElementById('password').value
};

try{

    const respuesta = await fetch(BASE_URL + 'app/api/login.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(datosLogin) 
    });
    
    const datos = await respuesta.json()

    const parrafoRespuesta = document.getElementById('respuestaServidor');
    parrafoRespuesta.innerText = datos.mensaje;

   if (datos.status === 'success') {
            parrafoRespuesta.style.color = 'green';

            //REVISAR y ver si ponerlo en registro
            setTimeout(() => {
                window.location.href = BASE_URL + 'public/index.php';
            }, 1500);
    }else{
        parrafoRespuesta.style.color = 'red';
    }
}catch (error){
    console.error("Hubo un error de conexión", error);
}


});