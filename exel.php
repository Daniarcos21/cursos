<?php
// Incluye la biblioteca PhpSpreadsheet
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["excel_file"])) {
    // Establece la conexión a la base de datos
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "cursos";

    $conexion = new mysqli($servername, $username, $password, $dbname);
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    $inputFileName = $_FILES["excel_file"]["tmp_name"];

    // Crea un lector para el archivo Excel
    $reader = new Xlsx();
    $spreadsheet = $reader->load($inputFileName);
    $sheet = $spreadsheet->getActiveSheet();

    // Itera sobre las filas del archivo Excel
    foreach ($sheet->getRowIterator() as $row) {
        // Obtiene todas las celdas de la fila
        $cellIterator = $row->getCellIterator();

        // Obtiene la primera celda de la fila
        $firstCell = $cellIterator->current();

        // Verifica si la primera celda está vacía
        if (empty($firstCell->getValue())) {
            // Si la primera celda está vacía, sal del bucle
            break;
        }

        // Array para almacenar los datos de la fila
        $rowData = [];

        // Itera sobre las celdas de la fila
        foreach ($cellIterator as $cell) {
            // Obtiene el valor de la celda y agrega al array de datos de la fila
            $rowData[] = $cell->getValue();
        }

        // Obtiene los datos del Excel
        $nombre = $rowData[0]; // Nombre del trabajador
        $bateria = $rowData[1]; // Batería del trabajador
        $rpe = $rowData[2]; // RP del trabajador
        $claveCurso = $rowData[3]; // Clave del curso
        $nombreCurso = $rowData[4]; // Nombre del curso
        $horas = $rowData[5]; // Horas del curso
        $proceso = $rowData[6]; // Proceso del curso
        $acreditacion = $rowData[7]; // Acreditación del curso
        $pendiente = $rowData[8]; // Pendiente del curso

        // Verifica si el trabajador ya existe en la base de datos
        $sql_trabajador = "SELECT * FROM trabajadores WHERE clave = '$rpe'";
        $result_trabajador = $conexion->query($sql_trabajador);
        if ($result_trabajador->num_rows == 0) {
            // Si el trabajador no existe, lo insertamos en la base de datos
            $sql_insert_trabajador = "INSERT INTO trabajadores (Nombre, Bateria, clave) VALUES ('$nombre', '$bateria', '$rpe')";
            if ($conexion->query($sql_insert_trabajador) === TRUE) {
                echo "Trabajador '$nombre' insertado correctamente.<br>";
            } else {
                echo "Error al insertar trabajador '$nombre': " . $conexion->error . "<br>";
            }
        }

        // Verifica si el curso ya existe en la base de datos
        $sql_curso = "SELECT * FROM cursos WHERE Clave = '$claveCurso'";
        $result_curso = $conexion->query($sql_curso);
        if ($result_curso->num_rows == 0) {
            // Si el curso no existe, lo insertamos en la base de datos
            $sql_insert_curso = "INSERT INTO cursos (Clave, NombreCurso, Horas) VALUES ('$claveCurso', '$nombreCurso', '$horas')";
            if ($conexion->query($sql_insert_curso) === TRUE) {
                echo "Curso '$nombreCurso' insertado correctamente.<br>";
            } else {
                echo "Error al insertar curso '$nombreCurso': " . $conexion->error . "<br>";
            }
        }

        // Obtener ID del trabajador
        $sql_id_trabajador = "SELECT ID FROM trabajadores WHERE clave = '$rpe'";
        $result_id_trabajador = $conexion->query($sql_id_trabajador);
        if ($result_id_trabajador->num_rows > 0) {
            $row_trabajador = $result_id_trabajador->fetch_assoc();
            $id_trabajador = $row_trabajador["ID"];

            // Obtener ID del curso
            $sql_id_curso = "SELECT ID FROM cursos WHERE Clave = '$claveCurso'";
            $result_id_curso = $conexion->query($sql_id_curso);
            if ($result_id_curso->num_rows > 0) {
                $row_curso = $result_id_curso->fetch_assoc();
                $id_curso = $row_curso["ID"];

                // Verificar si ya existe una asociación entre el trabajador y el curso
                $sql_asociacion = "SELECT * FROM asociacion_trabajador_curso WHERE id_trabajador = '$id_trabajador' AND id_curso = '$id_curso'";
                $result_asociacion = $conexion->query($sql_asociacion);
                if ($result_asociacion->num_rows > 0) {
                    // Si ya existe una asociación, actualizamos los datos
                    $sql_update_asociacion = "UPDATE asociacion_trabajador_curso SET proceso = '$proceso', acreditacion = '$acreditacion', pendiente = '$pendiente' WHERE id_trabajador = '$id_trabajador' AND id_curso = '$id_curso'";
                    if ($conexion->query($sql_update_asociacion) === TRUE) {
                        echo "Datos asociados actualizados correctamente.<br>";
                    } else {
                        echo "Error al actualizar datos asociados: " . $conexion->error . "<br>";
                    }
                } else {
                    // Si no existe una asociación, la insertamos en la base de datos
                    $sql_insert_asociacion = "INSERT INTO asociacion_trabajador_curso (id_trabajador, id_curso, proceso, acreditacion, pendiente) VALUES ('$id_trabajador', '$id_curso', '$proceso', '$acreditacion', '$pendiente')";
                    if ($conexion->query($sql_insert_asociacion) === TRUE) {
                        echo "Datos asociados insertados correctamente.<br>";
                    } else {
                        echo "Error al insertar datos asociados: " . $conexion->error . "<br>";
                    }
                }
            } else {
                echo "Error: No se encontró el ID del curso.<br>";
            }
        } else {
            echo "Error: No se encontró el ID del trabajador.<br>";
        }
    }


    // Cierra la conexión a la base de datos
    $conexion->close();
}

