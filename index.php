<?php
require_once 'src/Cliente.php';

$cliente = new Cliente('clientes');

// Manejar el envío del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = $_POST['id'] ?? null;
    $nombres = $_POST['nombres'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $dni = $_POST['dni'] ?? '';
    $telefono = $_POST['telefono'] ?? '';

    // Obtener todos los clientes
    $clientes = $cliente->readAll();

    // Verificar si el DNI ya existe
    $dniExists = false;
    foreach ($clientes as $existingId => $existingCliente) {
        if ($existingCliente['dni'] == $dni && $existingId != $id) {
            $dniExists = true;
            break;
        }
    }

    // Crear un nuevo cliente
    if ($action == 'create') {
        if ($dniExists) {
            echo "El DNI ya está registrado. Por favor, ingrese un DNI único.";
        } else {
            $cliente->create([
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'dni' => $dni,
                'telefono' => $telefono
            ]);
        }
    }

    // Actualizar un cliente existente
    if ($action == 'update' && $id !== null) {
        if ($dniExists) {
            echo "El DNI ya está registrado para otro cliente. No se puede actualizar.";
        } else {
            $cliente->update($id, [
                'nombres' => $nombres,
                'apellidos' => $apellidos,
                'dni' => $dni,
                'telefono' => $telefono
            ]);
        }
    }

    // Eliminar un cliente
    if ($action == 'delete' && $id !== null) {
        $cliente->delete($id);
    }
}

// Obtener todos los clientes para mostrar
$clientes = $cliente->readAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Clientes</title>
</head>
<body>


<br><br><a href="agregarF.php">Gestión Factura</a><br><br>
    <a href="agregarP.php">Gestión Producto</a><br><br>
<h1>Gestión de Clientes</h1>

<!-- Formulario para crear/actualizar clientes -->
<form method="POST" action="index.php">
    <label for="id" hidden>ID:</label>
    <input type="text" name="id" id="id" hidden>
    <label for="nombres">Nombres:</label>
    <input type="text" name="nombres" id="nombres" required>
    <label for="apellidos">Apellidos:</label>
    <input type="text" name="apellidos" id="apellidos" required>
    <label for="dni">DNI:</label>
    <input type="text" name="dni" id="dni" required>
    <label for="telefono">Teléfono:</label>
    <input type="text" name="telefono" id="telefono" required>
    <button type="submit" name="action" value="create" id="createBtn">Crear Cliente</button>
    <button type="submit" name="action" value="update" id="updateBtn" style="display:none;">Actualizar Cliente</button>
</form>

<h2>Lista de Clientes</h2>

<!-- Mostrar la lista de clientes -->
<table border="1">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>DNI</th>
            <th>Teléfono</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($clientes as $id => $cliente): ?>
            <tr>
                <td><?php echo $id; ?></td>
                <td><?php echo htmlspecialchars($cliente['nombres']); ?></td>
                <td><?php echo htmlspecialchars($cliente['apellidos']); ?></td>
                <td><?php echo htmlspecialchars($cliente['dni']); ?></td>
                <td><?php echo htmlspecialchars($cliente['telefono']); ?></td>
                <td>
                    <button onclick="editCliente(<?php echo $id; ?>, '<?php echo $cliente['nombres']; ?>', '<?php echo $cliente['apellidos']; ?>', '<?php echo $cliente['dni']; ?>', '<?php echo $cliente['telefono']; ?>')">Editar</button>
                    <form method="POST" action="index.php" style="display:inline;">
                        <input type="hidden" name="id" value="<?php echo $id; ?>">
                        <button type="submit" name="action" value="delete">Eliminar</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
// Función para llenar el formulario con los datos del cliente a editar
function editCliente(id, nombres, apellidos, dni, telefono) {
    document.getElementById('id').value = id;
    document.getElementById('nombres').value = nombres;
    document.getElementById('apellidos').value = apellidos;
    document.getElementById('dni').value = dni;
    document.getElementById('telefono').value = telefono;

    document.getElementById('createBtn').style.display = 'none';
    document.getElementById('updateBtn').style.display = 'inline';
}
</script>

</body>
</html>