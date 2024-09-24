<?php
require 'src/Factura.php'; // Incluye la clase Factura

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = $_POST['usuario'];
    $dni = $_POST['usuario'];
    $productos = [];
    $totalGeneral = 0; // Inicializar el total general

    // Cargar los clientes desde el archivo JSON
    $clientes = json_decode(file_get_contents('clientes.json'), true); // Cambia a tu archivo de clientes

    // Verificar si el DNI ya existe
    $dniExists = false;
    foreach ($clientes as $existingCliente) {
        if ($existingCliente['dni'] == $dni) {
            // Si el DNI ya existe
            $dniExists = true;
            break;
        }
    }

    if (!$dniExists) {
        echo "El DNI no existe. Por favor, verifica el DNI del usuario.";
        exit; // Detener la ejecución si el DNI no es válido
    }

    // Obtener productos y cantidades del formulario
    if (isset($_POST['producto']) && isset($_POST['cantidad'])) {
        foreach ($_POST['producto'] as $key => $producto) {
            $precio = obtenerPrecio($producto); // Función que devuelve el precio del producto
            $cantidad = $_POST['cantidad'][$key];
            $totalProducto = $precio * $cantidad; // Calcula el total para este producto
            
            // Agregar el producto al array
            $productos[] = [
                'nombre' => $producto,
                'cantidad' => $cantidad,
                'precio' => $precio,
                'total' => $totalProducto // Almacena el total para este producto
            ];
            
            $totalGeneral += $totalProducto; // Sumar al total general
        }
    }

    // Cargar facturas existentes, si el archivo no existe o está vacío, inicializar como un array vacío
    $facturas = json_decode(file_get_contents('facturas.json'), true) ?? [];

    // Verificar si el array está vacío para asignar el primer ID
    if (empty($facturas)) {
        $nuevoId = 1; // Si no hay facturas, el primer ID será 1
    } else {
        // Obtener el último ID y sumarle 1 para el nuevo ID
        $nuevoId = end($facturas)['id'] + 1;
    }

    // Crear una nueva factura
    $factura = [
        'id' => $nuevoId, // Asignar el nuevo ID
        'usuario' => $usuario,
        'productos' => $productos,
        'total' => $totalGeneral
    ];

    // Guardar la factura en el archivo facturas.json
    $facturas[] = $factura; // Agregar la nueva factura al array
    file_put_contents('facturas.json', json_encode($facturas, JSON_PRETTY_PRINT)); // Guardar de nuevo en el archivo

    echo "Factura creada exitosamente con el ID: $nuevoId.";
}

function obtenerPrecio($nombreProducto)
{
    $productos = json_decode(file_get_contents('productos.json'), true);
    foreach ($productos as $producto) {
        if ($producto['nombre'] === $nombreProducto) {
            return $producto['precio'];
        }
    }
    return 0; // Devuelve 0 si no se encuentra el producto
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Factura</title>
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
    <script>
        function agregarProducto() {
            var tabla = document.getElementById("tablaProductos");
            var fila = tabla.insertRow();
            
            var celdaProducto = fila.insertCell(0);
            var selectProducto = document.createElement("select");
            selectProducto.name = "producto[]";
            selectProducto.required = true;
            selectProducto.onchange = function() { actualizarTotal(fila); }; // Llama a actualizarTotal
            llenarProductos(selectProducto); 
            celdaProducto.appendChild(selectProducto);

            var celdaCantidad = fila.insertCell(1);
            var inputCantidad = document.createElement("input");
            inputCantidad.type = "number";
            inputCantidad.name = "cantidad[]";
            inputCantidad.min = "1";
            inputCantidad.required = true;
            inputCantidad.oninput = function() { actualizarTotal(fila); }; // Llama a actualizarTotal
            celdaCantidad.appendChild(inputCantidad);

            var celdaTotal = fila.insertCell(2);
            var totalSpan = document.createElement("span");
            totalSpan.textContent = "Total: $0.00"; // Muestra el total inicial
            totalSpan.className = "total";
            celdaTotal.appendChild(totalSpan);

            var celdaEliminar = fila.insertCell(3);
            var btnEliminar = document.createElement("button");
            btnEliminar.type = "button";
            btnEliminar.textContent = "Eliminar";
            btnEliminar.onclick = function() { eliminarFila(fila); };
            celdaEliminar.appendChild(btnEliminar);
        }

        function eliminarFila(fila) {
            var tabla = document.getElementById("tablaProductos");
            tabla.deleteRow(fila.rowIndex);
        }

        function llenarProductos(select) {
            var productos = <?php
                $json = file_get_contents('productos.json');
                if ($json === false) {
                    echo "[]"; // En caso de error, retorna un array vacío
                } else {
                    echo $json;
                }
            ?>;

            // Opción por defecto
            var optionDefault = document.createElement("option");
            optionDefault.value = "";
            optionDefault.text = "Selecciona un producto";
            select.appendChild(optionDefault);

            productos.forEach(function(producto) {
                var option = document.createElement("option");
                option.value = producto.nombre; // Usa el nombre como valor
                option.text = producto.nombre + " - Precio: " + producto.precio;
                select.appendChild(option);
            });
        }

        function actualizarTotal(fila) {
            var select = fila.cells[0].getElementsByTagName("select")[0];
            var cantidadInput = fila.cells[1].getElementsByTagName("input")[0];
            var totalSpan = fila.cells[2].getElementsByClassName("total")[0];

            var productoNombre = select.value;
            var cantidad = cantidadInput.value;

            if (productoNombre && cantidad > 0) {
                var productos = <?php
                    $json = file_get_contents('productos.json');
                    if ($json === false) {
                        echo "[]"; // En caso de error, retorna un array vacío
                    } else {
                        echo $json;
                    }
                ?>;
                var precio = 0;

                // Buscar el precio del producto seleccionado
                productos.forEach(function(producto) {
                    if (producto.nombre === productoNombre) {
                        precio = producto.precio;
                    }
                });

                var total = precio * cantidad;
                totalSpan.textContent = "Total: $" + total.toFixed(2);
            } else {
                totalSpan.textContent = "Total: $0.00";
            }
        }

        window.onload = function() {
            // Llenar el primer select al cargar la página
            var selectInicial = document.querySelector('select[name="producto[]"]');
            llenarProductos(selectInicial);
        }
    </script>
</head>
<body>
    

<br><br><a href="index.php">Gestión Cliente</a><br><br>
    <a href="agregarP.php">Gestión Producto</a><br><br>
    <h1>Crear Factura</h1>
    <form action="agregarF.php" method="POST">
        <label for="usuario">DNI del Usuario:</label>
        <input type="text" id="usuario" name="usuario" required>
        <br><br>

        <h3>Productos</h3>
        <table id="tablaProductos">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <select name="producto[]" required onchange="actualizarTotal(this.parentNode.parentNode)">
                            <!-- Opción por defecto se llenará desde JavaScript -->
                        </select>
                    </td>
                    <td>
                        <input type="number" name="cantidad[]" min="1" required oninput="actualizarTotal(this.parentNode.parentNode)">
                    </td>
                    <td>
                        <span class="total">Total: $0.00</span>
                    </td>
                    <td>
                        <button type="button" onclick="eliminarFila(this.parentNode.parentNode)">Eliminar</button>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>

        <button type="button" onclick="agregarProducto()">Agregar Producto</button>
        <br><br>

        <button type="submit" id="createBtn">Crear Factura</button>
    </form>



<br><br>
<!-- Formulario para ver una factura existente -->
<h1>Ver Factura</h1>
<form action="verFactura.php" method="GET">
    <label for="factura_id">ID de la Factura:</label>
    <input id="factura_id" name="factura_id" required>
    <br><br>
    <button type="submit">Ver Factura</button>
</form>
</body>
</html>