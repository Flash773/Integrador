<?php
$conexion = new mysqli("localhost", "root", "", "visitas_db");
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Registro de Visitas</title>
</head>
<body>
    <h2>Formulario de Ingreso</h2>
    <form action="registrar.php" method="post">
        <input type="text" name="nombre" placeholder="Nombre" required><br>
        <input type="text" name="apellido" placeholder="Apellido" required><br>
        <input type="text" name="dni" placeholder="DNI" required><br>
        <input type="text" name="motivo" placeholder="Motivo de la visita" required><br>
        <input type="text" name="persona" placeholder="Persona a la que visita" required><br>
        <input type="submit" value="Registrar entrada">
    </form>
    <br><a href="visitas.php">Ver visitas</a>
</body>
</html>
<?php
include 'conexion.php';

$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$dni = $_POST['dni'];
$motivo = $_POST['motivo'];
$persona = $_POST['persona'];
$hora_ingreso = date("Y-m-d H:i:s");

$sql = "INSERT INTO visitas (nombre, apellido, dni, motivo, persona_visita, hora_ingreso)
        VALUES ('$nombre', '$apellido', '$dni', '$motivo', '$persona', '$hora_ingreso')";

if ($conexion->query($sql)) {
    echo "Visita registrada. <a href='index.php'>Volver</a>";
} else {
    echo "Error: " . $conexion->error;
}
?>
<?php
include 'conexion.php';

$id = $_GET['id'];
$hora_egreso = date("Y-m-d H:i:s");

$sql = "UPDATE visitas SET hora_egreso = '$hora_egreso' WHERE id = $id";

if ($conexion->query($sql)) {
    echo "Salida registrada. <a href='visitas.php'>Volver</a>";
} else {
    echo "Error: " . $conexion->error;
}
?>
<?php
include 'conexion.php';

$filtro = isset($_GET['filtro']) ? $_GET['filtro'] : '';
$consulta = "SELECT * FROM visitas WHERE 
            nombre LIKE '%$filtro%' 
            OR apellido LIKE '%$filtro%' 
            OR DATE(hora_ingreso) = '$filtro'
            ORDER BY hora_ingreso DESC";

$resultado = $conexion->query($consulta);
?>

<form method="get">
    <input type="text" name="filtro" placeholder="Buscar por nombre o fecha (AAAA-MM-DD)" value="<?= $filtro ?>">
    <input type="submit" value="Filtrar">
</form>

<table border="1">
    <tr>
        <th>Nombre</th><th>Apellido</th><th>DNI</th><th>Motivo</th><th>Visita a</th><th>Ingreso</th><th>Egreso</th><th>Acción</th>
    </tr>
    <?php while($fila = $resultado->fetch_assoc()): ?>
        <tr>
            <td><?= $fila['nombre'] ?></td>
            <td><?= $fila['apellido'] ?></td>
            <td><?= $fila['dni'] ?></td>
            <td><?= $fila['motivo'] ?></td>
            <td><?= $fila['persona_visita'] ?></td>
            <td><?= $fila['hora_ingreso'] ?></td>
            <td><?= $fila['hora_egreso'] ?? '---' ?></td>
            <td>
                <?php if (!$fila['hora_egreso']): ?>
                    <a href="salida.php?id=<?= $fila['id'] ?>">Marcar salida</a>
                <?php else: ?>
                    Registrada
                <?php endif; ?>
            </td>
        </tr>
    <?php endwhile; ?>
</table>
CREATE DATABASE IF NOT EXISTS visitas_db;
USE visitas_db;

CREATE TABLE visitas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    apellido VARCHAR(50),
    dni VARCHAR(20),
    motivo VARCHAR(100),
    persona_visita VARCHAR(50),
    hora_ingreso DATETIME,
    hora_egreso DATETIME NULL
);
