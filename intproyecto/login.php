<?php
session_start();

$conn = new mysqli("localhost", "root", "", "gestion_proyectos");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT id, username, password, rol, email FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $usuario = $result->fetch_assoc();
        
        // Verificar contraseña
        if ($password === $usuario['password']) {
            if ($usuario['rol'] == 'administrador') {
                $_SESSION['user_id'] = $usuario['id'];
                $_SESSION['username'] = $usuario['username'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['rol'] = $usuario['rol'];
                $_SESSION['loggedin'] = true;
                
                header("Location: admin.php");
                exit();
            } else {
                $error = "Acceso restringido: Solo para administradores";
            }
        } else {
            $error = "Correo o contraseña incorrectos";
        }
    } else {
        $error = "Correo o contraseña incorrectos";
    }
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gestión de Proyectos</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(to right, #e2e2e2, #c9d6ff);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }
        
        .form-box {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .form-box h2 {
            margin-bottom: 20px;
            color: #333;
        }
        
        .form-box input {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        .form-box input:focus {
            border-color: #3e5fd9;
            outline: none;
        }
        
        .form-box button {
            width: 100%;
            padding: 12px;
            background: #3e5fd9;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .form-box button:hover {
            background: #2d4bb7;
        }
        
        .form-box p {
            margin-top: 15px;
            color: #666;
        }
        
        .form-box a {
            color: #3e5fd9;
            text-decoration: none;
        }
        
        .form-box a:hover {
            text-decoration: underline;
        }
        
        .error {
            color: #f44336;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        .password-container {
            position: relative;
        }
        
        .password-container input {
            padding-right: 40px;
        }
        
        .toggle-password {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-box" id="login--form">
            <form action="login.php" method="POST">
                <h2>Iniciar Sesión</h2>
                
                <?php if (!empty($error)): ?>
                    <div class="error"><?php echo $error; ?></div>
                <?php endif; ?>
                
                <input type="email" name="email" placeholder="Correo electrónico" required>
                
                <div class="password-container">
                    <input type="password" name="password" id="password" placeholder="Contraseña" required>
                    <i class="fas fa-eye toggle-password" onclick="togglePassword()"></i>
                </div>
                
                <button type="submit" name="login">Ingresar</button>
                <p>¿No tienes una cuenta? <a href="#">Regístrate</a></p>
            </form>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const icon = document.querySelector('.toggle-password');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>
</html>