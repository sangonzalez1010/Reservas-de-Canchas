<?php
session_start();  // Inicia una sesión PHP para mantener la información del usuario durante la navegación.

include 'config.php';  // Se incluye el archivo de configuración que contiene la conexión a la base de datos.

if (isset($_POST['register'])) {  // Verifica si el formulario de registro ha sido enviado.
    // Recibe y limpia los datos del formulario
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);  // Encripta la contraseña ingresada.

    // Verifica si el correo electrónico ya está registrado en la base de datos
    $check_sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($check_sql);  // Prepara la consulta SQL.
    $stmt->bind_param("s", $email);  // Vincula el parámetro del correo.
    $stmt->execute();  // Ejecuta la consulta.
    $result = $stmt->get_result();  // Obtiene el resultado de la consulta.

    // Si el correo ya existe, muestra un mensaje de error.
    if ($result->num_rows > 0) {
        $error = "El correo ya está registrado.";  // Mensaje de error si el correo ya está registrado.
    } else {
        // Si el correo no está registrado, inserta un nuevo usuario en la base de datos.
        $sql = "INSERT INTO usuarios (nombre, email, telefono, contraseña, estado, rol_id, fecha_registro, rol) 
                VALUES (?, ?, ?, ?, 'activo', 3, NOW(), 'cliente')";
        $stmt = $conn->prepare($sql);  // Prepara la consulta de inserción.
        $stmt->bind_param("ssss", $nombre, $email, $telefono, $password);  // Vincula los parámetros.

        // Si la inserción es exitosa, redirige al usuario al formulario de inicio de sesión con un mensaje de éxito.
        if ($stmt->execute()) {
            header("Location: login.php?success=1");  // Redirige a la página de login con un mensaje de éxito.
            exit();
        } else {
            $error = "Error al registrar el usuario.";  // Si ocurre un error al registrar, muestra un mensaje de error.
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cliente</title>
    <!-- Carga de Bootstrap desde un CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Asegúrate de que este archivo contenga los estilos personalizados -->
    <link rel="stylesheet" href="css/styles_login.css">  
</head>
<body class="d-flex align-items-center justify-content-center" style="height: 100vh; background: url('img/panel.webp') no-repeat center center fixed; background-size: cover;">
    <div class="container">
        <div class="text-center mb-4">
            <!-- Logo de la empresa o sistema -->
            <img src="img/logo.png" alt="Logo" class="img-fluid" style="max-width: 150px;">
        </div>
        <h2 class="text-center text-white mb-4">Registro de Cliente</h2>

        <!-- Mostrar mensaje de error si existe -->
        <?php if (isset($error)) { ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>  <!-- Mensaje de error mostrado -->
        <?php } ?>

        <!-- Formulario de registro -->
        <form method="POST" action="">
            <!-- Campo para ingresar el nombre del usuario -->
            <div class="mb-3">
                <label for="nombre" class="form-label text-white">Nombre</label>
                <input type="text" name="nombre" class="form-control" required>  <!-- Campo obligatorio -->
            </div>

            <!-- Campo para ingresar el correo electrónico -->
            <div class="mb-3">
                <label for="email" class="form-label text-white">Correo Electrónico</label>
                <input type="email" name="email" class="form-control" required>  <!-- Campo obligatorio -->
            </div>

            <!-- Campo para ingresar el teléfono del usuario -->
            <div class="mb-3">
                <label for="telefono" class="form-label text-white">Número de Teléfono</label>
                <input type="text" name="telefono" class="form-control" required>  <!-- Campo obligatorio -->
            </div>

            <!-- Campo para ingresar la contraseña -->
            <div class="mb-3">
                <label for="password" class="form-label text-white">Contraseña</label>
                <input type="password" name="password" class="form-control" required>  <!-- Campo obligatorio -->
            </div>

            <!-- Botón de registro -->
            <button type="submit" name="register" class="btn btn-success w-100">Registrarse</button>  <!-- Enviar el formulario -->
        </form>

        <!-- Enlace para redirigir a la página de inicio de sesión -->
        <div class="text-center mt-3">
            <a href="login.php" class="text-white">¿Ya tienes cuenta? Iniciar sesión</a>  <!-- Enlace de inicio de sesión -->
        </div>
    </div>

    <!-- Carga de los scripts de Bootstrap desde un CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
