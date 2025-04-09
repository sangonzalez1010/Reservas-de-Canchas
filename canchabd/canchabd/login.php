<?php
session_start();  // Inicia una sesión PHP para mantener información durante la navegación.

include 'config.php';  // Se incluye el archivo de configuración que contiene la conexión a la base de datos.

error_reporting(E_ALL);  // Muestra todos los errores de PHP (solo en desarrollo).
ini_set('display_errors', 1);  // Asegura que los errores sean visibles en el navegador.

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {  // Verifica si el formulario ha sido enviado.
    $email = trim($_POST['email']);  // Obtiene y limpia el correo del formulario.
    $password = $_POST['password'];  // Obtiene la contraseña ingresada.

    // Verifica si la conexión a la base de datos está activa.
    if (!$conn) {
        die("Error en la conexión a la base de datos: " . mysqli_connect_error());
    }

    // Consulta SQL preparada para buscar un usuario por correo electrónico de forma segura.
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);  // Prepara la consulta SQL.

    // Verifica si la consulta se preparó correctamente.
    if (!$stmt) {
        die("Error en la consulta: " . $conn->error);
    }

    // Vincula el parámetro del correo en la consulta.
    $stmt->bind_param("s", $email);
    $stmt->execute();  // Ejecuta la consulta.
    $result = $stmt->get_result();  // Obtiene el resultado de la consulta.

    if ($result->num_rows > 0) {  // Si se encontró al menos un usuario con ese correo.
        $user = $result->fetch_assoc();  // Obtiene los datos del primer usuario encontrado.

        // Verifica si la cuenta está activa.
        if ($user['estado'] !== 'activo') {
            $error = "Tu cuenta está bloqueada.";  // Si no está activa, muestra un mensaje de error.
        } else {
            // Verifica si la contraseña es correcta utilizando password_verify() para comparar con la contraseña encriptada.
            if (password_verify($password, $user['contraseña'])) { 
                // Si la contraseña es correcta, se guarda la información del usuario en la sesión.
                $_SESSION['usuario_id'] = $user['id'];
                $_SESSION['nombre'] = $user['nombre'];
                $_SESSION['rol_id'] = $user['rol_id']; // Guarda el rol del usuario para redirigir según el tipo de usuario.

                // Redirige según el rol del usuario (administrador, empleado o cliente).
                if ($user['rol_id'] == 1 || $user['rol_id'] == 2) {  // Si es admin o empleado.
                    header("Location: admin_panel.php");  // Redirige al panel de administración.
                    exit();
                } elseif ($user['rol_id'] == 3) {  // Si es cliente.
                    $_SESSION['cliente_id'] = $user['id'];  // Guarda el ID del cliente en la sesión.
                    header("Location: reserva.php");  // Redirige a la página de reserva de canchas.
                    exit();
                } else {
                    $error = "Error: rol desconocido.";  // Si el rol no está definido, muestra un error.
                }
            } else {
                $error = "Correo o contraseña incorrectos.";  // Si la contraseña no es correcta.
            }
        }
    } else {
        $error = "Correo o contraseña incorrectos.";  // Si no se encuentra el usuario con ese correo.
    }

    // Cierra la consulta preparada.
    $stmt->close();
}

// Cierra la conexión a la base de datos si está abierta.
if (isset($conn)) {
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <!-- Carga de los estilos de Bootstrap desde un CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles_login.css">  <!-- Estilos personalizados para la página de login -->
</head>
<body class="d-flex align-items-center justify-content-center" style="height: 100vh; background: url('img/panel.webp') no-repeat center center fixed; background-size: cover;">

    <!-- Contenedor principal de la página de login -->
    <div class="container" style="max-width: 400px;">
        <div class="text-center mb-4">
            <img src="img/logo.png" alt="Logo" class="img-fluid" style="max-width: 150px;">  <!-- Logo de la empresa o sistema -->
        </div>
        <h2 class="text-center text-white mb-4">Iniciar Sesión</h2>

        <!-- Si se ha generado un error, se muestra en un mensaje de alerta -->
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php } ?>

        <!-- Formulario para ingresar las credenciales -->
        <form method="POST" action="">
            <div class="mb-3">
                <label for="email" class="form-label text-white">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" required>  <!-- Campo para ingresar el correo -->
            </div>

            <div class="mb-3">
                <label for="password" class="form-label text-white">Contraseña</label>
                <input type="password" name="password" class="form-control" required>  <!-- Campo para ingresar la contraseña -->
            </div>

            <button type="submit" name="login" class="btn btn-success w-100">Iniciar Sesión</button>  <!-- Botón para enviar el formulario -->
        </form>

        <div class="text-center mt-3">
            <a href="registro.php" class="text-white">¿No tienes cuenta? Regístrate aquí</a>  <!-- Enlace para registro de nuevos usuarios -->
        </div>

        <div class="text-center mt-2">
            <a href="restablecer.php" class="text-white">¿Olvidaste tu contraseña?</a>  <!-- Enlace para restablecer la contraseña -->
        </div>
    </div>

    <!-- Carga de los scripts de Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
