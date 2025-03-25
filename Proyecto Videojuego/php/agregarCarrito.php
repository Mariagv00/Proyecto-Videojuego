<?php
session_start();

$nombre = $_POST['nombre'];
$precio = $_POST['precio'];
$imagen = $_POST['imagen'];

$producto = [
  'nombre' => $nombre,
  'precio' => $precio,
  'imagen' => $imagen
];

// Inicializa el carrito si no existe
if (!isset($_SESSION['carrito'])) {
  $_SESSION['carrito'] = [];
}

// Agrega el producto
$_SESSION['carrito'][] = $producto;

// Redirige al carrito
header("Location: ../html/carrito.php");
exit();
