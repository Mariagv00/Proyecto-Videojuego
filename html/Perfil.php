<?php
session_start();
if (!isset($_SESSION['id_usuario'])) {
  header("Location: Login.php");
  exit;
}

try {
  $conexion = new PDO("mysql:host=localhost;dbname=tiendavideojuegos;charset=utf8", "root", "");
  $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Error de conexión: " . $e->getMessage());
}

// Obtener datos del cliente
$cliente_id = $_SESSION['id_usuario'];
$cliente_stmt = $conexion->prepare("SELECT nombre, correo FROM clientes WHERE id = ?");
$cliente_stmt->execute([$cliente_id]);
$cliente = $cliente_stmt->fetch(PDO::FETCH_ASSOC);

// Obtener pedidos del cliente
$pedidos_stmt = $conexion->prepare("SELECT id, detalle, total, fecha FROM pedidos WHERE id_cliente = ? ORDER BY fecha DESC");
$pedidos_stmt->execute([$cliente_id]);
$pedidos = $pedidos_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mi Perfil</title>
  <link rel="stylesheet" href="../css/Perfil.css" />
</head>
<body>
   
<!-- ✅ NAVBAR -->
<nav class="navbar">
  <div class="logo">Game Zone</div>
</nav>


  <div class="perfil-container">
    <h1>Perfil de <?= htmlspecialchars($cliente['nombre']) ?></h1>
    <div class="datos-usuario">
      <p><strong>Nombre:</strong> <?= htmlspecialchars($cliente['nombre']) ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($cliente['correo']) ?></p>
    </div>

    <h2>Historial de Pedidos</h2>
    <table>
      <thead>
        <tr>
          <th>Detalle</th>
          <th>Total</th>
          <th>Fecha</th>
          <th>Factura</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pedidos as $pedido): ?>
          <tr>
            <td><?= htmlspecialchars($pedido['detalle']) ?></td>
            <td><?= number_format($pedido['total'], 2) ?> €</td>
            <td><?= date('d/m/Y', strtotime($pedido['fecha'])) ?></td>
            <td>
              <a href="Factura.php?id=<?= $pedido['id'] ?>" target="_blank">
                <button>Descargar PDF</button>
              </a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <div class="volver-tienda">
  <a href="tienda.php">
    <button>Volver a la tienda</button>
  </a>
</div>

  </div>
</body>
</html>
