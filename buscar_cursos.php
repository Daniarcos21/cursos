<?php
// Verificar si se ha enviado la solicitud de búsqueda o de marcado de cursos como acreditados
if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['busqueda'])) {
    // Obtener el término de búsqueda ingresado por el usuario
    $busqueda = $_GET['busqueda'];

    // Establecer la conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cursos";

    $conexion = new mysqli($servername, $username, $password, $dbname);
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    // Consulta SQL para buscar los cursos asociados al trabajador
    $sql = "SELECT cursos.ID, cursos.NombreCurso, cursos.Horas, asociacion_trabajador_curso.proceso, asociacion_trabajador_curso.acreditacion, asociacion_trabajador_curso.pendiente, asociacion_trabajador_curso.programar
            FROM trabajadores 
            INNER JOIN asociacion_trabajador_curso ON trabajadores.ID = asociacion_trabajador_curso.id_trabajador 
            INNER JOIN cursos ON asociacion_trabajador_curso.id_curso = cursos.ID 
            WHERE trabajadores.Nombre LIKE '%$busqueda%' OR trabajadores.clave LIKE '%$busqueda%' " ;

    $result = $conexion->query($sql);

    $cursos = array();

    // Verificar si se encontraron resultados
    if ($result->num_rows > 0) {
        // Obtener los datos de los cursos y almacenarlos en un arreglo
        while ($row = $result->fetch_assoc()) {
            $cursos[] = array(
                'ID' => $row['ID'],
                'Curso' => $row['NombreCurso'],
                'Horas' => $row['Horas'], // Nueva línea para incluir las horas
                'Proceso' => $row['proceso'],
                'Acreditacion' => $row['acreditacion'],
                'Pendiente' => $row['pendiente'],
                'Programar' => $row['programar']
            );
        }
    } else {
        // No se encontraron resultados
        $cursos[] = array('mensaje' => 'No se encontraron cursos para el trabajador buscado.');
    }

    // Devolver la respuesta JSON
    echo json_encode($cursos);

    // Cerrar la conexión a la base de datos
    $conexion->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cursosAcreditados'])) {
    // Verificar si se ha enviado la solicitud para marcar cursos como acreditados
    $cursosAcreditados = $_POST['cursosAcreditados'];

    // Establecer la conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cursos";

    $conexion = new mysqli($servername, $username, $password, $dbname);
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    // Convertir los IDs de los cursos a acreditar en una cadena separada por comas
    $cursosAcreditadosStr = implode(",", $cursosAcreditados);

    // Consulta SQL para marcar los cursos como acreditados en la base de datos
    $sql = "UPDATE cursos SET acreditacion = 1 WHERE ID IN ($cursosAcreditadosStr)";

    if ($conexion->query($sql) === TRUE) {
        // La actualización se realizó correctamente
        echo json_encode(array("status" => "success", "message" => "Cursos acreditados correctamente"));
    } else {
        // Ocurrió un error al ejecutar la consulta
        echo json_encode(array("status" => "error", "message" => "Error al actualizar los cursos: " . $conexion->error));
    }

    // Cerrar la conexión a la base de datos
    $conexion->close();
} else {
    // No se recibió una solicitud válida
    http_response_code(400);
    echo json_encode(array("status" => "error", "message" => "Solicitud no válida"));
}
?>