<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $rol_id = 3; // Cliente por defecto

    // Validar datos
    if (empty($nombre) || empty($email) || empty($telefono) || empty($password) || empty($confirm_password)) {
        $error = "Por favor, complete todos los campos.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Verificar si el email ya está registrado
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "El correo electrónico ya está registrado.";
        } else {
            // Cifrar la contraseña
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insertar usuario en la base de datos con el teléfono
            $sql = "INSERT INTO usuarios (nombre, email, telefono, contraseña, rol_id) VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('ssssi', $nombre, $email, $telefono, $hashed_password, $rol_id);
            
            if ($stmt->execute()) {
                // Redirigir a login después de registrar
                header("Location: login.php");
                exit();
            } else {
                $error = "Error al registrar el usuario.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
</head>
<body>
    <h1>Registrar cuenta</h1>
    
    <?php if (isset($error)) { echo "<p style='color:red;'>$error</p>"; } ?>

    <form method="POST">
        <input type="text" name="nombre" placeholder="Nombre" required><br>
        <input type="email" name="email" placeholder="Correo Electrónico" required><br>
        <input type="text" name="telefono" placeholder="Número de Teléfono" required><br>
        <input type="password" name="password" placeholder="Contraseña" required><br>
        <input type="password" name="confirm_password" placeholder="Confirmar Contraseña" required><br>
        <button type="submit">Registrar</button>
    </form>
</body>
</html>
