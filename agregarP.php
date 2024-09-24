<?php
require_once 'src/Producto.php';

$producto = new Producto('productos');

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'] ?? '';
    $precio = $_POST['precio'] ?? 0;

    // Obtener todos los productos
    $productos = $producto->readAll();

    // Verificar si el ID ya existe
    $idExists = isset($productos[$id]);

    // Crear un nuevo producto
    if ($action == 'create') {
        if ($idExists) {
            echo "<p style='color:red;'>El ID ya está registrado, por favor ingrese un ID único.</p>";
        } else {
            if ($id && $nombre && $precio > 0) {
                $producto->create([
                    'id' => $id,
                    'nombre' => $nombre,
                    'precio' => $precio
                ]);
                echo "<p style='color:green;'>Producto creado correctamente.</p>";
            } else {
                echo "<p style='color:red;'>Por favor, complete todos los campos.</p>";
            }
        }
    }

    // Actualizar un producto existente
    if ($action == 'update' && $id !== null) {
        if (!$idExists) {
            echo "<p style='color:red;'>El producto con este ID no existe.</p>";
        } else {
            $producto->update($id, [
                'nombre' => $nombre,
                'precio' => $precio
            ]);
            echo "<p style='color:green;'>Producto actualizado correctamente.</p>";
        }
    }

    // Eliminar un producto
    if ($action == 'delete' && $id !== null) {
        $producto->delete($id);
        echo "<p style='color:green;'>Producto eliminado correctamente.</p>";
    }
}

// Obtener todos los productos para mostrar
$productos = $producto->readAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Productos</title>
</head>
<body>


<br><br><a href="agregarF.php">Gestión Factura</a><br><br>
    <a href="index.php">Gestión Cliente</a><br><br>
<h1>Gestión de Productos</h1>

<!-- Formulario para crear/actualizar productos -->
<form method="POST" action="agregarP.php">
    <label for="id">ID:</label>
    <input type="text" name="id" id="id" >
    <label for="nombre">Nombre:</label>
    <input type="text" name="nombre" id="nombre" required>
    <label for="precio">Precio:</label>
    <input type="number" step="0.01" name="precio" id="precio" required>
    <button type="submit" name="action" value="create" id="createBtn">Crear Producto</button>
    <button type="submit" name="action" value="update" id="updateBtn" style="display:none;">Actualizar Producto</button>
</form>

<h2>Lista de Productos</h2>

<!-- Mostrar la lista de productos -->
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($productos)) : ?>
            <?php foreach ($productos as $id => $producto): ?>
                <tr>
                    <td><?php echo htmlspecialchars($id); ?></td>
                    <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                    <td><?php echo htmlspecialchars($producto['precio']); ?></td>
                    <td>
                        <button onclick="editProducto('<?php echo $id; ?>', '<?php echo $producto['nombre']; ?>', '<?php echo $producto['precio']; ?>')">Editar</button>
                        <form method="POST" action="agregarP.php" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
                            <button type="submit" name="action" value="delete">Eliminar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr><td colspan="4">No hay productos registrados.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

<script>
// Función para llenar el formulario con los datos del producto a editar
function editProducto(id, nombre, precio) {
    document.getElementById('id').value = id;
    document.getElementById('nombre').value = nombre;
    document.getElementById('precio').value = precio;

    document.getElementById('createBtn').style.display = 'none';
    document.getElementById('updateBtn').style.display = 'inline';
}
</script>

</body>
</html>