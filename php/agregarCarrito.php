<?php
session_start();

$nombre = $_POST['nombre'] ?? '';
$precio = floatval($_POST['precio'] ?? 0);
$imagen = $_POST['imagen'] ?? '';

if ($nombre && $imagen) {
  if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
  }

  $encontrado = false;
  foreach ($_SESSION['carrito'] as &$item) {
    if ($item['nombre'] === $nombre) {
      $item['cantidad'] += 1;
      $encontrado = true;
      break;
    }
  }

  if (!$encontrado) {
    $_SESSION['carrito'][] = [
      'nombre' => $nombre,
      'precio' => $precio,
      'imagen' => $imagen,
      'cantidad' => 1
    ];
  }
}

// ✅ Redirige de vuelta a tienda para seguir comprando
header("Location: ../html/tienda.php");
exit();

