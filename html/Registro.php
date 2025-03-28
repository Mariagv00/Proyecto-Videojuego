<?php
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = $_POST['username'] ?? '';
  $correo = $_POST['email'] ?? '';
  $pass1  = $_POST['password'] ?? '';
  $pass2  = $_POST['confirm-password'] ?? '';

  // Validar contraseñas iguales
  if ($pass1 !== $pass2) {
    $mensaje = "Las contraseñas no coinciden.";
  } else {
    try {
      $conn = new PDO("mysql:host=localhost;dbname=tiendavideojuegos;charset=utf8", "root", "");
      $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

      // Verificar si ya existe el correo
      $stmt = $conn->prepare("SELECT * FROM clientes WHERE correo = :correo");
      $stmt->execute(['correo' => $correo]);

      if ($stmt->rowCount() > 0) {
        $mensaje = "El correo ya está registrado.";
      } else {
        // Guardar usuario
        $stmt = $conn->prepare("INSERT INTO clientes (nombre, correo, contrasena) VALUES (:nombre, :correo, :contrasena)");
        $stmt->execute([
          'nombre' => $nombre,
          'correo' => $correo,
          'contrasena' => $pass1 // Opcional: usar password_hash($pass1, PASSWORD_DEFAULT)
        ]);

        echo "<script>alert('Registro exitoso'); window.location.href = 'Inicio.html';</script>";
        exit();
      }
    } catch (PDOException $e) {
      $mensaje = "Error en la conexión: " . $e->getMessage();
    }
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registro</title>
  <link rel="stylesheet" href="../css/Registro.css" />
</head>
<body>

  <!-- ✅ NAVBAR -->
  <nav class="navbar">
    <div class="logo">Game Zone</div>
  </nav>

  <!-- ✅ FORMULARIO DE REGISTRO -->
  <div class="container">
    <h2>Registro de Usuario</h2>

    <?php if ($mensaje): ?>
      <script>alert("<?= $mensaje ?>");</script>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="username">Nombre de Usuario</label>
        <input type="text" id="username" name="username" required />
      </div>
      <div class="form-group">
        <label for="email">Correo Electrónico</label>
        <input type="email" id="email" name="email" required />
      </div>
      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required />
      </div>
      <div class="form-group">
        <label for="confirm-password">Confirmar Contraseña</label>
        <input type="password" id="confirm-password" name="confirm-password" required />
      </div>
      <button type="submit">Registrarse</button>
      <button type="button" onclick="location.href='Inicio.html'">Volver</button>
    </form>
  </div>

</body>
</html>
