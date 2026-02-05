<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Paideia</title>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700&family=Roboto:wght@300;400;500&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --color-primary: #2C5282;      
            --color-secondary: #D69E2E;    
            --color-bg: #F7F9FC;           
            --color-text: #2D3748;         
            --font-heading: 'Cinzel', serif;
            --font-body: 'Roboto', sans-serif;
        }

        body { 
            font-family: var(--font-body); 
            background-color: var(--color-bg); 
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: var(--color-text);
        }

        .contenedor-form {
            background: white;
            padding: 40px;
            border-radius: 4px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
            border-top: 5px solid var(--color-primary);
        }

        h2 {
            font-family: var(--font-heading);
            color: var(--color-primary);
            text-align: center;
            margin-bottom: 30px;
            font-size: 2rem;
        }

        .campo { margin-bottom: 20px; }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: var(--color-primary);
        }

        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e0;
            border-radius: 4px;
            box-sizing: border-box; /* Para que el padding no rompa el ancho */
            font-family: var(--font-body);
        }

        input:focus {
            outline: none;
            border-color: var(--color-secondary);
            box-shadow: 0 0 0 3px rgba(214, 158, 46, 0.2);
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: var(--color-primary);
            color: white;
            border: none;
            border-radius: 4px;
            font-family: var(--font-heading);
            font-weight: bold;
            font-size: 1.1rem;
            cursor: pointer;
            transition: background 0.3s;
            margin-top: 10px;
        }

        button:hover { background-color: #1A365D; }

        .mensaje {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9rem;
        }
        
        .enlace { color: var(--color-secondary); text-decoration: none; font-weight: bold; }
        .enlace:hover { text-decoration: underline; }
    </style>
</head>
<body>

    <div class="contenedor-form">
        <h2>Únete a Paideia</h2>
        <form id="formRegistro">
            <div class="campo">
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="campo">
                <label for="apellidos">Apellidos</label>
                <input type="text" id="apellidos" name="apellidos" required>
            </div>
            <div class="campo">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Crear Cuenta</button>
        </form>
        <div class="mensaje">
            ¿Ya tienes cuenta? <a href="login.html" class="enlace">Inicia Sesión</a>
        </div>
    </div>

    <script>
        document.getElementById('formRegistro').addEventListener('submit', async (e) => {
            e.preventDefault(); // Evitar que se recargue la página

            // Recoger datos
            const datos = {
                nombre: document.getElementById('nombre').value,
                apellidos: document.getElementById('apellidos').value,
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            };

            try {
                // Enviar petición al Backend
                const respuesta = await fetch('../api/registro.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(datos)
                });

                const resultado = await respuesta.json();

                if (respuesta.ok) {
                    alert('¡Registro exitoso! Ahora puedes iniciar sesión.');
                    // Aquí redirigiremos al login cuando lo tengamos hecho
                    // window.location.href = 'login.html'; 
                } else {
                    alert('Error: ' + resultado.error);
                }

            } catch (error) {
                console.error('Error:', error);
                alert('Hubo un error de conexión con el servidor.');
            }
        });
    </script>
</body>
</html>