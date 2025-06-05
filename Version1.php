<?php
$conexion = new mysqli("localhost", "root", "", "visitas_db");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
<?php include('db.php'); ?>
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
    echo "Ingreso registrado.";
}
?>
🔚 4. Marcar salida (registrar_salida.php)
php
Copiar
Editar
<?php include('db.php'); ?>
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
    echo "Salida registrada.";
}
?>
<?php include('db.php'); ?>

<form method="GET" action="">
  <input type="date" name="fecha">
  <input type="text" name="persona" placeholder="Persona visitada">
  <button type="submit">Filtrar</button>
</form>

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