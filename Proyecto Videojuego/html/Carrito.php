<?php
session_start();

// Eliminar producto individual
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
  $index = (int)$_GET['eliminar'];
  if (isset($_SESSION['carrito'][$index])) {
    unset($_SESSION['carrito'][$index]);
    $_SESSION['carrito'] = array_values($_SESSION['carrito']);
  }
  header("Location: Carrito.php");
  exit();
}

// Cancelar compra
if (isset($_GET['cancelar'])) {
  $_SESSION['carrito'] = [];
  header("Location: Carrito.php");
  exit();
}

// Finalizar compra
if (isset($_GET['finalizar'])) {
  if (!isset($_SESSION['id_usuario'])) {
    echo "<script>alert('Debes iniciar sesión para finalizar la compra.'); window.location.href = 'Login.php';</script>";
    exit();
  }

  $id_cliente = $_SESSION['id_usuario'];
  $detalle = '';
  $total = 0;

  foreach ($_SESSION['carrito'] as $producto) {
    $cantidad = $producto['cantidad'] ?? 1;
    $subtotal = $producto['precio'] * $cantidad;
    $total += $subtotal;
    $detalle .= $producto['nombre'] . " (x$cantidad) - " . number_format($subtotal, 2) . " €\n";
  }

  try {
    $conn = new PDO("mysql:host=localhost;dbname=tiendavideojuegos;charset=utf8", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("INSERT INTO pedidos (id_cliente, detalle, total) VALUES (:id_cliente, :detalle, :total)");
    $stmt->execute([
      'id_cliente' => $id_cliente,
      'detalle' => $detalle,
      'total' => $total
    ]);

    $_SESSION['carrito'] = [];
    echo "<script>alert('¡Compra realizada con éxito! Pedido guardado.'); window.location.href = 'Carrito.php';</script>";
    exit();

  } catch (PDOException $e) {
    echo "<script>alert('Error al guardar el pedido: " . $e->getMessage() . "');</script>";
  }
}

// Modificar cantidad
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion'], $_POST['index'])) {
  $accion = $_POST['accion'];
  $index = (int)$_POST['index'];

  if (isset($_SESSION['carrito'][$index])) {
    if ($accion === "aumentar") {
      $_SESSION['carrito'][$index]['cantidad'] += 1;
    } elseif ($accion === "reducir") {
      $_SESSION['carrito'][$index]['cantidad'] -= 1;
      if ($_SESSION['carrito'][$index]['cantidad'] < 1) {
        unset($_SESSION['carrito'][$index]);
      }
    }
    $_SESSION['carrito'] = array_values($_SESSION['carrito']);
  }

  header("Location: Carrito.php");
  exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Tu Carrito</title>
  <link rel="stylesheet" href="../css/Carrito.css">
</head>
<body>

<div class="carrito-container">
  <h1>🛒 Tu Carrito</h1>

  <?php
  $total = 0;

  if (!empty($_SESSION['carrito'])):
    foreach ($_SESSION['carrito'] as $index => $producto):
      $cantidad = $producto['cantidad'] ?? 1;
      $subtotal = $producto['precio'] * $cantidad;
      $total += $subtotal;
  ?>
    <div class="producto-carrito">
      <img src="<?= $producto['imagen'] ?>" alt="<?= $producto['nombre'] ?>">
      
      <div class="producto-info">
        <h3><?= $producto['nombre'] ?></h3>
        <p>Precio unitario: <?= number_format($producto['precio'], 2) ?> €</p>
        <p>Subtotal: <?= number_format($subtotal, 2) ?> €</p>

        <div class="cantidad-controles">
          <form method="post" action="Carrito.php">
            <input type="hidden" name="accion" value="reducir">
            <input type="hidden" name="index" value="<?= $index ?>">
            <button type="submit" class="cantidad-btn">➖</button>
          </form>

          <span class="cantidad-num"><?= $cantidad ?></span>

          <form method="post" action="Carrito.php">
            <input type="hidden" name="accion" value="aumentar">
            <input type="hidden" name="index" value="<?= $index ?>">
            <button type="submit" class="cantidad-btn">➕</button>
          </form>

          <form method="get" action="Carrito.php" style="margin-left: auto;">
            <input type="hidden" name="eliminar" value="<?= $index ?>">
            <button type="submit" class="eliminar">Eliminar</button>
          </form>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
    <div class="total">
      Total: <?= number_format($total, 2) ?> €
    </div>
  <?php else: ?>
    <p style="text-align:center;">Tu carrito está vacío.</p>
  <?php endif; ?>

  <div class="acciones">
    <a href="tienda.php">← Volver a Tienda</a>
    <a href="Carrito.php?cancelar=true">Cancelar Compra</a>
    <?php if (!empty($_SESSION['carrito'])): ?>
      <a href="Carrito.php?finalizar=true">Finalizar Compra</a>
    <?php endif; ?>
  </div>
</div>

</body>
</html>

