<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Game Zone - Tienda</title>
  <link rel="stylesheet" href="../css/Tienda.css">
  <script>
function moverSlider(btn, direccion) {
  const container = btn.parentElement.querySelector('.juegos-slider');
  const scrollAmount = container.offsetWidth / 1.2; // ajusta el paso
  container.scrollBy({ left: direccion * scrollAmount, behavior: 'smooth' });
}
</script>


</head>


<body>

<!-- ✅ NAVBAR -->
<nav class="navbar">
  <div class="logo">Game Zone</div>

  <ul class="nav-links">
    <li><a href="#Acción">Acción</a></li>
    <li><a href="#Aventura">Aventura</a></li>
    <li><a href="#Terror">Terror</a></li>
    <li><a href="#Multijugador">Multijugador</a></li>
    <li><a href="#RPG">RPG</a></li>
  </ul>

  <div class="cart-icon">
  <?php $count = isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0; ?>
<div class="cart-icon">
  <a href="Carrito.php">
    <img src="../img/cesta.png" alt="Carrito">
    <span class="cart-count"><?= $count ?></span>
  </a>
</nav>

<!-- ✅ Descripción de la tienda -->
<section class="descripcion-tienda">
  <h1>Bienvenido a Game Zone</h1>
  <p>Explora y compra tus juegos favoritos de acción, aventura, terror, multijugador y RPG. ¡Descubre nuevos mundos y vive grandes historias!</p>
</section>


<?php
session_start();

if (!isset($_SESSION['carrito'])) {
  $_SESSION['carrito'] = [];
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nombre'], $_POST['precio'], $_POST['imagen'])) {
  $producto = [
    'nombre' => $_POST['nombre'],
    'precio' => $_POST['precio'],
    'imagen' => $_POST['imagen']
  ];
  $_SESSION['carrito'][] = $producto;
}

function obtenerDatosJuego($appid) {
  $url = "https://store.steampowered.com/api/appdetails?appids=$appid&cc=es&l=spanish";
  $json = file_get_contents($url);
  $datos = json_decode($json, true);
  return $datos[$appid]['data'] ?? null;
}

$categorias = [
  "Acción" => [730, 271590, 578080, 812140, 381210, 400],
  "Aventura" => [292030, 1174180, 304430, 381210, 1222140, 582160],
  "Terror" => [945360, 19000, 409710, 250580, 239140, 8500],
  "Multijugador" => [440, 570, 359550, 105600, 431960, 1172470],
  "RPG" => [582010, 1091500, 72850, 391540, 374320, 377160]
];

?>


<!-- ✅ LISTADO DE JUEGOS POR CATEGORÍA -->
<main class="contenido">
  <?php foreach ($categorias as $nombreCategoria => $appids): ?>
    <h2 id="<?= $nombreCategoria ?>"><?= $nombreCategoria ?></h2>

    <div class="slider-container">
      <button class="flecha izquierda" onclick="moverSlider(this, -1)">←</button>
      <div class="juegos-slider">
        <?php foreach ($appids as $appid):
          $juego = obtenerDatosJuego($appid);
          if (!$juego) continue;

          $nombre = $juego['name'];
          $descripcion = $juego['short_description'];
          $imagen = $juego['header_image'];
          $precio = $juego['is_free'] ? 0.00 : (isset($juego['price_overview']) ? $juego['price_overview']['final'] / 100 : 0.00);
        ?>
          <form method="POST" class="juego">
            <img src="<?= $imagen ?>" alt="<?= $nombre ?>">
            <h3><?= $nombre ?></h3>
            <p><?= $descripcion ?></p>
            <p><strong>Precio:</strong> <?= $precio == 0 ? 'Gratis' : number_format($precio, 2) . ' €' ?></p>
            <input type="hidden" name="nombre" value="<?= $nombre ?>">
            <input type="hidden" name="precio" value="<?= $precio ?>">
            <input type="hidden" name="imagen" value="<?= $imagen ?>">
            <button type="submit">Comprar</button>
          </form>
        <?php endforeach; ?>
      </div>
      <button class="flecha derecha" onclick="moverSlider(this, 1)">→</button>
    </div>
  <?php endforeach; ?>
</main>

<footer class="footer">
  <p>© 2025 Todos los derechos reservados. Todas las marcas registradas pertenecen a sus respectivos dueños en EE. UU. y otros países.</p>
  <p>Todos los precios incluyen IVA (donde sea aplicable).</p>
  <div class="footer-links">
    <a href="#">Política de Privacidad</a> |
    <a href="#">Información legal</a> 
  </div>
</footer>


</body>
</html>

