<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login con Material Icons</title>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'DM Sans', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-weight: 300;
        }

        .form {
            font-family: 'DM Sans', sans-serif;
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border: 2px solid #e1e1e1;
            text-align: center;
           
        }

        .input-group {
            display: flex;
            align-items: center;
            margin-bottom: 34px;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 5px 10px;
            background-color: #fff;
        }

        .input-group .material-icons {
            margin-right: 8px;
            color: #666;
        }

        .input-group input {
            border: none;
            outline: none;
            width: 100%;
            font-size: 14px;
            padding: 8px 0;
        }

        .input-group .toggle-password {
            cursor: pointer;
            color: #666;
        }

        h1 {
            font-family: 'DM Sans', sans-serif;
            color: #009fdf;
            width: 300px;
            font-size: 35px;
        }

        button {
            background-color: #009fdf;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #007bbd;
        }
    </style>
</head>
<body>

    <div class="form">
        <h1>Iniciar Sesión</h1>  
        <form>
            <div class="input-group">
                <span class="material-icons">person</span>
                <input type="text" placeholder="Usuario" name="usuario">
            </div>

            <div class="input-group">
                <span class="material-icons">lock</span>
                <input type="password" placeholder="Contraseña" id="password">
                
            </div>

            <button type="submit">Enviar</button>
        </form>
    </div>

   
</body>
</html>
