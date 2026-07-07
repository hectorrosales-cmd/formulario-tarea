<?php
require_once "conexion.php";

if (isset($_GET['eliminar'])) {
    $id = (int) $_GET['eliminar'];
    $stmt = $conexion->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: consultar_producto.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar'])) {
    $nombre = trim($_POST['nombre']);
    $categoria = trim($_POST['categoria']);
    $cantidad = (int) $_POST['cantidad'];
    $precio = (float) $_POST['precio'];

    $stmt = $conexion->prepare("INSERT INTO productos (nombre, categoria, cantidad, precio) VALUES (?, ?, ?, ?)");
    $stmt->execute([$nombre, $categoria, $cantidad, $precio]);
    header("Location: consultar_producto.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $id = (int) $_POST['id'];
    $nombre = trim($_POST['nombre']);
    $categoria = trim($_POST['categoria']);
    $cantidad = (int) $_POST['cantidad'];
    $precio = (float) $_POST['precio'];

    $stmt = $conexion->prepare("UPDATE productos SET nombre=?, categoria=?, cantidad=?, precio=? WHERE id=?");
    $stmt->execute([$nombre, $categoria, $cantidad, $precio, $id]);
    header("Location: consultar_producto.php?pagina=" . ($_POST['pagina'] ?? 1));
    exit;
}

$porPagina = 5;
$pagina = isset($_GET['pagina']) ? (int) $_GET['pagina'] : 1;
if ($pagina < 1) $pagina = 1;
$inicio = ($pagina - 1) * $porPagina;

$totalProductos = $conexion->query("SELECT COUNT(*) FROM productos")->fetchColumn();
$totalPaginas = max(1, ceil($totalProductos / $porPagina));

$stmt = $conexion->prepare("SELECT * FROM productos ORDER BY id LIMIT :inicio, :porPagina");
$stmt->bindValue(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindValue(':porPagina', $porPagina, PDO::PARAM_INT);
$stmt->execute();
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Consultar y Modificar Producto</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    /* Paleta formal pero vivida (Azul profundo + Azul Eléctrico) */
    :root {
        --color-primario: #0f172a; /* Azul pizarra muy profundo (formal) */
        --color-vivido: #0066ff;   /* Azul eléctrico vibrante (vivido) */
        --bg-sutil: #f4f6fa;       /* Fondo grisáceo azulado limpio */
    }

    body {
        background-color: var(--bg-sutil) !important;
    }

    /* Encabezado de la Tabla */
    .thead-formal {
        background-color: var(--color-primario) !important;
        color: #ffffff !important;
    }

    /* Botón Principal Vivido */
    .btn-vivido {
        background-color: var(--color-vivido) !important;
        border-color: var(--color-vivido) !important;
        color: #ffffff !important;
        font-weight: 500;
    }
    .btn-vivido:hover {
        background-color: #0052cc !important;
        border-color: #0052cc !important;
    }

    /* Ajuste en los Modales */
    .modal-header-formal {
        background-color: var(--color-primario);
        color: #ffffff;
    }
    .modal-header-formal .btn-close {
        filter: invert(1); /* Hace la equis blanca */
    }

    /* Paginación Activa Personalizada */
    .pagination .page-item.active .page-link {
        background-color: var(--color-vivido) !important;
        border-color: var(--color-vivido) !important;
    }
    .pagination .page-link {
        color: var(--color-primario);
    }
</style>
</head>
<body>

<div class="container py-4">
    <h2 class="mb-4 fw-bold" style="color: var(--color-primario);">Consultar y Modificar Producto</h2>

    <button class="btn btn-vivido mb-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#modalAgregar">
        + Nuevo Producto
    </button>

    <div class="modal fade" id="modalAgregar" tabindex="-1">
      <div class="modal-dialog">
        <div class="modal-content">
          <form method="POST" action="consultar_producto.php">
            <div class="modal-header modal-header-formal">
              <h5 class="modal-title">Agregar Producto</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Nombre</label>
                    <input type="text" name="nombre" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Categoría</label>
                    <input type="text" name="categoria" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Cantidad</label>
                    <input type="number" name="cantidad" class="form-control" min="0" value="0" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Precio</label>
                    <input type="number" step="0.01" name="precio" class="form-control" min="0" value="0" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" name="agregar" class="btn btn-vivido">Guardar</button>
            </div>
          </form>
        </div>
      </div>
    </div>

    <div class="table-responsive shadow-sm rounded">
        <table class="table table-hover bg-white mb-0 align-middle">
            <thead class="thead-formal">
                <tr>
                    <th class="py-3">ID</th>
                    <th class="py-3">Nombre</th>
                    <th class="py-3">Categoría</th>
                    <th class="py-3">Cantidad</th>
                    <th class="py-3">Precio</th>
                    <th class="py-3 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody>
            <?php if (count($productos) === 0): ?>
                <tr><td colspan="6" class="text-center py-4 text-muted">No hay productos registrados.</td></tr>
            <?php else: ?>
                <?php foreach ($productos as $p): ?>
                <tr>
                    <td class="fw-bold text-secondary"><?= $p['id'] ?></td>
                    <td><?= htmlspecialchars($p['nombre']) ?></td>
                    <td><span class="badge bg-light text-dark border"><?= htmlspecialchars($p['categoria']) ?></span></td>
                    <td><?= $p['cantidad'] ?></td>
                    <td class="fw-bold text-dark">L. <?= number_format($p['precio'], 2) ?></td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#modalEditar<?= $p['id'] ?>">
                            Editar
                        </button>
                        <a href="consultar_producto.php?eliminar=<?= $p['id'] ?>"
                           class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('¿Eliminar el producto \'<?= htmlspecialchars($p['nombre'], ENT_QUOTES) ?>\'?');">
                            Eliminar
                        </a>
                    </td>
                </tr>

                <div class="modal fade" id="modalEditar<?= $p['id'] ?>" tabindex="-1">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form method="POST" action="consultar_producto.php">
                        <div class="modal-header modal-header-formal">
                          <h5 class="modal-title">Editar Producto #<?= $p['id'] ?></h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <input type="hidden" name="id" value="<?= $p['id'] ?>">
                            <input type="hidden" name="pagina" value="<?= $pagina ?>">

                            <div class="mb-3">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="<?= htmlspecialchars($p['nombre']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Categoría</label>
                                <input type="text" name="categoria" class="form-control" value="<?= htmlspecialchars($p['categoria']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Cantidad</label>
                                <input type="number" name="cantidad" class="form-control" value="<?= $p['cantidad'] ?>" min="0" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Precio</label>
                                <input type="number" step="0.01" name="precio" class="form-control" value="<?= $p['precio'] ?>" min="0" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" name="editar" class="btn btn-vivido">Guardar cambios</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <nav class="mt-4">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPaginas; $i++): ?>
                <li class="page-item <?= $i == $pagina ? 'active' : '' ?>">
                    <a class="page-link" href="consultar_producto.php?pagina=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>

    <div class="text-start mt-3">
        <a href="menu_inventario.php" class="btn btn-sm btn-outline-secondary">Volver al menú</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>