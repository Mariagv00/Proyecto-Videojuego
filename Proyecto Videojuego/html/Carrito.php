<?php
session_start();

// Eliminar producto individual
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
  $index = (int)$_GET['eliminar'];
  if (isset($_SESSION['carrito'][$index])) {
    unset($_SESSION['carrito'][$index]);
    $_SESSION['carrito'] = array_values($_SESSION['carrito']); // reindexar
  }
  header("Location: carrito.php");
  exit();
}

// Cancelar compra = vaciar carrito
if (isset($_GET['cancelar'])) {
  $_SESSION['carrito'] = [];
  header("Location: carrito.php");
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
        $total += $producto['precio'];
    ?>
      <div class="producto-carrito">
        <img src="<?= $producto['imagen'] ?>" alt="<?= $producto['nombre'] ?>">
        <div class="producto-info">
          <h3><?= $producto['nombre'] ?></h3>
          <p>Precio: <?= number_format($producto['precio'], 2) ?> €</p>
        </div>
        <form method="get" action="carrito.php">
          <input type="hidden" name="eliminar" value="<?= $index ?>">
          <button type="submit" class="eliminar">Eliminar</button>
        </form>
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
      <a href="carrito.php?cancelar=true">Cancelar Compra</a>
    </div>
  </div>

</body>
</html>

