<?php
session_start();

// Conexión a la base de datos
try {
  $conexion = new PDO("mysql:host=localhost;dbname=tiendavideojuegos;charset=utf8", "root", "");
  $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Error de conexión: " . $e->getMessage());
}

// Función para obtener los datos de la API de Steam
function obtenerDatosJuego($appid) {
  $url = "https://store.steampowered.com/api/appdetails?appids=$appid&cc=es&l=spanish";
  $json = @file_get_contents($url);
  $datos = json_decode($json, true);
  return $datos[$appid]['data'] ?? null;
}

// Obtener appids por categoría desde la base de datos
$categorias = [];
$sql = "SELECT categoria, appid FROM juegos ORDER BY categoria";
foreach ($conexion->query($sql) as $row) {
  $categorias[$row['categoria']][] = $row['appid'];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Game Zone - Tienda</title>
  <link rel="stylesheet" href="../css/Tienda.css">
  <script>
    function moverSlider(btn, direccion) {
      const container = btn.parentElement.querySelector('.juegos-slider');
      const scrollAmount = container.offsetWidth / 1.2;
      container.scrollBy({ left: direccion * scrollAmount, behavior: 'smooth' });
    }
  </script>
</head>
<body>

<!-- ✅ NAVBAR -->
<nav class="navbar">
  <div class="logo">Game Zone</div>
  <ul class="nav-links">
    <?php foreach (array_keys($categorias) as $categoria): ?>
      <li><a href="#<?= $categoria ?>"><?= $categoria ?></a></li>
    <?php endforeach; ?>
  </ul>
  <div class="cart-icon">
  <a href="Perfil.php" class="profile-icon">
    <img src="../img/perfil.png" alt="Perfil">
  </a>
    <a href="Carrito.php">
      <img src="../img/cesta.png" alt="Carrito">
    </a>
  </div>
</nav>

<!-- ✅ DESCRIPCIÓN -->
<section class="descripcion-tienda">
  <h1>Bienvenido a Game Zone</h1>
  <p>Explora y compra tus juegos favoritos de acción, aventura, terror, multijugador y RPG. ¡Descubre nuevos mundos y vive grandes historias!</p>
</section>

<!-- ✅ JUEGOS -->
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
        <form method="POST" action="../php/agregarCarrito.php" class="juego">
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

<!-- ✅ PIE DE PÁGINA -->
<footer class="footer">
  <p>© 2025 Todos los derechos reservados. Todas las marcas registradas pertenecen a sus respectivos dueños en EE. UU. y otros países.</p>
  <p>Todos los precios incluyen IVA (donde sea aplicable).</p>
  <div class="footer-links">
    <a href="#">Política de Privacidad</a> |
    <a href="#">Información legal</a> |
    <a href="#">Acuerdo de Suscriptor a Steam</a>
  </div>
</footer>

<!-- ✅ AGREGAR AL CARRITO -->
<?php
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['nombre'], $_POST['precio'], $_POST['imagen'])) {
  $nuevo = [
    'nombre' => $_POST['nombre'],
    'precio' => (float)$_POST['precio'],
    'imagen' => $_POST['imagen'],
    'cantidad' => 1
  ];

  if (!isset($_SESSION['carrito'])) $_SESSION['carrito'] = [];

  $encontrado = false;
  foreach ($_SESSION['carrito'] as &$item) {
    if ($item['nombre'] === $nuevo['nombre']) {
      $item['cantidad'] += 1;
      $encontrado = true;
      break;
    }
  }

  if (!$encontrado) {
    $_SESSION['carrito'][] = $nuevo;
  }

  echo "<script>window.location.href = 'tienda.php';</script>";
}
?>
</body>
</html>

