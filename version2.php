
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Visitas</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f0f2f5;
      margin: 0;
      padding: 20px;
    }

    h2 {
      color: #333;
    }

    form {
      background-color: #fff;
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 10px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      max-width: 400px;
    }

    input[type="text"],
    input[type="date"] {
      width: 100%;
      padding: 8px;
      margin: 6px 0;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    button {
      background-color: #007BFF;
      color: white;
      padding: 10px 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    button:hover {
      background-color: #0056b3;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background-color: #fff;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      margin-top: 20px;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 10px;
      text-align: left;
    }

    th {
      background-color: #007BFF;
      color: white;
    }

    .container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }

    .form-section {
      flex: 1;
      min-width: 300px;
    }

    .titulo {
      font-size: 22px;
      font-weight: bold;
      margin-bottom: 10px;
    }

    .mensaje {
      color: green;
      margin-top: 10px;
    }
  </style>
</head>
<body>

<?php
$conexion = new mysqli("localhost", "root", "", "visitas_db");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>

<div class="container">

  <div class="form-section">
    <div class="titulo">REGISTRO DE PERSONAS</div>
    <form method="POST" action="">
      <input type="text" name="nombre" placeholder="Nombre" required><br>
      <input type="text" name="apellido" placeholder="Apellido" required><br>
      <input type="text" name="dni" placeholder="DNI" required><br>
      <input type="text" name="motivo" placeholder="Motivo de la visita" required><br>
      <input type="text" name="persona_visita" placeholder="Persona a la que visita" required><br>
      <button type="submit" name="registrar">Registrar ingreso</button>
    </form>
    <?php
    if (isset($_POST['registrar'])) {
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $dni = $_POST['dni'];
        $motivo = $_POST['motivo'];
        $persona = $_POST['persona_visita'];
        $hora_ingreso = date('Y-m-d H:i:s');

        $sql = "INSERT INTO visitas (nombre, apellido, dni, motivo, persona_visita, hora_ingreso)
                VALUES ('$nombre', '$apellido', '$dni', '$motivo', '$persona', '$hora_ingreso')";
        $conexion->query($sql);
        echo "<div class='mensaje'>Ingreso registrado.</div>";
    }
    ?>
  </div>

  <div class="form-section">
    <div class="titulo">REGISTRAR SALIDA</div>
    <form method="POST" action="">
      <input type="text" name="dni" placeholder="DNI para marcar salida" required>
      <button type="submit" name="salida">Registrar salida</button>
    </form>
    <?php
    if (isset($_POST['salida'])) {
        $dni = $_POST['dni'];
        $hora_egreso = date('Y-m-d H:i:s');
        $sql = "UPDATE visitas SET hora_egreso='$hora_egreso' WHERE dni='$dni' AND hora_egreso IS NULL";
        $conexion->query($sql);
        echo "<div class='mensaje'>Salida registrada.</div>";
    }
    ?>
  </div>

  <div class="form-section">
    <div class="titulo">FILTRO Y REINICIO</div>
    <form method="GET" action="">
      <input type="date" name="fecha">
      <input type="text" name="persona" placeholder="Persona visitada">
      <button type="submit">Filtrar</button>
    </form>

    <form method="POST" action="">
      <button type="submit" name="reiniciar" onclick="return confirm('¿Estás seguro que querés borrar todos los registros?')">Reiniciar sistema</button>
    </form>

    <?php
    if (isset($_POST['reiniciar'])) {
        $sql = "DELETE FROM visitas";
        if ($conexion->query($sql)) {
            echo "<div class='mensaje'>Todos los registros fueron eliminados correctamente.</div>";
        } else {
            echo "<div class='mensaje' style='color:red;'>Error al reiniciar el sistema: " . $conexion->error . "</div>";
        }
    }
    ?>
  </div>
</div>

<div class="titulo">LISTADO DE VISITAS</div>
<?php
$where = [];

if (!empty($_GET['fecha'])) {
    $fecha = $_GET['fecha'];
    $where[] = "DATE(hora_ingreso) = '$fecha'";
}

if (!empty($_GET['persona'])) {
    $persona = $_GET['persona'];
    $where[] = "persona_visita LIKE '%$persona%'";
}

$condiciones = count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '';

$sql = "SELECT * FROM visitas $condiciones ORDER BY hora_ingreso DESC";
$resultado = $conexion->query($sql);

echo "<table border='1'><tr><th>Nombre</th><th>DNI</th><th>Motivo</th><th>Persona</th><th>Ingreso</th><th>Egreso</th></tr>";
while ($row = $resultado->fetch_assoc()) {
    echo "<tr>
        <td>{$row['nombre']} {$row['apellido']}</td>
        <td>{$row['dni']}</td>
        <td>{$row['motivo']}</td>
        <td>{$row['persona_visita']}</td>
        <td>{$row['hora_ingreso']}</td>
        <td>{$row['hora_egreso']}</td>
    </tr>";
}
echo "</table>";
?>

</body>
</html>
