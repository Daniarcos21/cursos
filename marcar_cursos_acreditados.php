<?php
// Verificar si se ha enviado la solicitud de actualización
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['idCurso'])) {
    // Obtener el ID del curso enviado desde el cliente
    $idCurso = $_POST['idCurso'];

    // Establecer la conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cursos";

    $conexion = new mysqli($servername, $username, $password, $dbname);
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    // Actualizar el estado del curso a acreditado en la base de datos
    $sql = "UPDATE asociacion_trabajador_curso SET programar = '2' WHERE id_curso = $idCurso";
    if ($conexion->query($sql) === TRUE) {
        echo "Cursos marcados como acreditados correctamente.";
    } else {
        echo "Error al marcar cursos como acreditados: " . $conexion->error;
    }

    // Cerrar la conexión a la base de datos
    $conexion->close();
}
?>