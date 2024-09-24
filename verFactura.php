<?php
// Verifica si se ha proporcionado el ID de la factura
if (isset($_GET['factura_id'])) {
    $facturaId = $_GET['factura_id'];

    // Carga el archivo JSON con las facturas
    $json = file_get_contents('facturas.json');
    $facturas = json_decode($json, true);

    // Busca la factura por el ID proporcionado
    $facturaEncontrada = null;
    foreach ($facturas as $factura) {
        if ($factura['id'] == $facturaId) {
            $facturaEncontrada = $factura;
            break;
        }
    }

    // Si se encontró la factura, mostrarla
    if ($facturaEncontrada) {
        $totalFinal = 0;
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Factura #<?php echo htmlspecialchars($facturaId); ?></title>
            <style>
                table {
                    width: 100%;
                    border-collapse: collapse;
                }
                table, th, td {
                    border: 1px solid black;
                }
                th, td {
                    padding: 8px;
                    text-align: left;
                }
            </style>
        </head>
        <body>

        <h1>Factura #<?php echo htmlspecialchars($facturaId); ?></h1>
        <a href="agregarF.php">Volver</a><br><br>
        <!-- Información del Usuario -->
        <p><strong>Nombre del Usuario:</strong> <?php echo htmlspecialchars($facturaEncontrada['usuario']); ?></p>

        <h3>Productos</h3>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($facturaEncontrada['productos'] as $producto): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                        <td><?php echo "$" . number_format($producto['precio'], 2); ?></td>
                        <td><?php
                            $total = $producto['cantidad'] * $producto['precio'];
                            echo "$" . number_format($total, 2);
                            $totalFinal += $total;
                        ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Mostrar el total final -->
        <p><strong>Total a pagar:</strong> $<?php echo number_format($totalFinal, 2); ?></p>

        </body>
        </html>
        <?php
    } else {
        // Si no se encuentra la factura, mostrar un mensaje de error
        echo "<h1>Factura no encontrada</h1>";
        echo "<p>No se encontró ninguna factura con el ID: " . htmlspecialchars($facturaId) . "</p>";
    }
} else {
    echo "<h1>Error</h1>";
    echo "<p>No se proporcionó un ID de factura.</p>";
}
?>