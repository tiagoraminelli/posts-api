<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login de Usuario</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }

        form {
            max-width: 400px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 5px;
        }

        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0;
            box-sizing: border-box;
        }

        button {
            padding: 10px 15px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .message {
            margin: 15px 0;
            padding: 10px;
            border-radius: 4px;
        }

        .error {
            background: #f8d7da;
            color: #842029;
        }

        .success {
            background: #d1e7dd;
            color: #0f5132;
        }
    </style>
</head>

<body>
    <h2>Iniciar sesión</h2>
    <form id="loginForm" method="post" action="">
        <label for="email">Email:</label><br />
        <input type="email" id="email" name="email" required /><br />

        <label for="password">Contraseña:</label><br />
        <input type="password" id="password" name="password" required /><br />

        <button type="submit">Entrar</button>
    </form>

    <div id="responseMessage"></div>

    <script>
        document.getElementById('loginForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const data = {
                email: document.getElementById('email').value,
                password: document.getElementById('password').value
            };

            const responseMessage = document.getElementById('responseMessage');
            responseMessage.textContent = '';
            responseMessage.className = '';

            try {
                const res = await fetch('/posts_api/src/controllers/login.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data)
                });

                const result = await res.json();

                if (res.ok) {
                    responseMessage.textContent = 'Login exitoso. Bienvenido ' + result.usuario.nombre;
                    responseMessage.className = 'message success';
                    // Puedes guardar api_key en localStorage o cookie si quieres
                    console.log('API Key:', result.usuario.api_key);
                    localStorage.setItem('api_key', result.usuario.api_key);
                    fetch('/posts_api/middlewares/Auth.php', {
                        method: 'GET',
                        headers: {
                            'API_KEY': localStorage.getItem('api_key') // o el valor correcto
                        }
                    });

                } else {
                    responseMessage.textContent = result.error || 'Error desconocido';
                    responseMessage.className = 'message error';
                }
            } catch (error) {
                responseMessage.textContent = 'Error al conectar con el servidor.';
                responseMessage.className = 'message error';
            }
        });
    </script>
</body>

</html>