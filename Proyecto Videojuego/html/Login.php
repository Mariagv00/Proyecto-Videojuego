<?php
session_start();

$mensaje = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  $usuario = $_POST['username'] ?? '';
  $password = $_POST['password'] ?? '';

  try {
    $conn = new PDO("mysql:host=localhost;dbname=tiendavideojuegos;charset=utf8", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT * FROM clientes WHERE nombre = :nombre LIMIT 1");
    $stmt->execute(['nombre' => $usuario]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($cliente && $cliente['contrasena'] === $password) {
      $_SESSION['usuario'] = $cliente['nombre'];
      $_SESSION['id_usuario'] = $cliente['id']; // 🔐 clave para las compras

      header("Location: tienda.php");
      exit();
    } else {
      $mensaje = "Usuario o contraseña incorrectos.";
    }

  } catch (PDOException $e) {
    $mensaje = "Error de conexión: " . $e->getMessage();
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Iniciar Sesión</title>
  <link rel="stylesheet" href="../css/Login.css" />
</head>
<body>

  <nav class="navbar">
    <div class="logo">Game Zone</div>
  </nav>

  <div class="login-container">
    <h2>Iniciar Sesión</h2>

    <?php if ($mensaje): ?>
      <script>alert("<?= $mensaje ?>");</script>
    <?php endif; ?>

    <form method="POST">
      <div class="form-group">
        <label for="username">Nombre de Usuario</label>
        <input type="text" id="username" name="username" required />
      </div>
      <div class="form-group">
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required />
      </div>
      <button type="submit">Iniciar Sesión</button>
      <button type="button" onclick="location.href='Inicio.html'">Volver</button>
    </form>
  </div>

</body>
</html>
