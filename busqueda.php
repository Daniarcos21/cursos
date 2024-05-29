<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Cursos por Trabajador</title>
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css">
    <style>
        /* Contenedor de fila */
        .row {
            margin-top: 20px;
        }

        /* Estilo para el formulario */
        form {
            margin-bottom: 20px;
        }

        /* Estilo para los contenedores de las tablas */
        .table-container {
            margin-bottom: 20px;
        }
        .small-text {
         font-size: 10px; /* Puedes ajustar el valor según tus necesidades */
         text-align: justify;
        }
       

        /* Estilo para el contenedor del porcentaje */
        #porcentaje-container {
            margin-top: 20px;
            font-size: 18px;
        }
      
    </style>
</head>
<body>
    <div class="container">
        <h5>Buscar Colaborador por Nombre o RP</h5>
            <form id="searchForm">

            <div class="form-row">
                <div class="col-3"> 
                <input type="text" class="form-control" id="searchInput" name="searchInput" required>
                </div>

                <div class="col-1">
                <button type="submit" class="btn btn-primary">Buscar</button>
                </div>

                <div class="col-2">
                <button type="button" onclick="guardarCambios()" class="btn btn-success">Guardar Cambios</button>
                </div>

                <div class="col-2">
                
                <button onclick="descargarComoExcel()" class="btn btn-success">Descargar  Excel</button>
                </div>  
            
                <div class="col-4">
                <div class="table-container">
                   
                    <table class="table table-bordered" id="perfil">
                        <thead class="thead table-success">
                            <tr>
                                <th >Nombre y Bateria</th>                        
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </form>
        
        <div class="row">
        <div id="infoTrabajador"></div>      

            <!-- Contenedor de la primera tabla -->
            <div class="col-md-6">
                <div class="table-container">
                    <h5>Cursos Acreditados</h5>
                    <table class="table table-bordered" id="acreditados">
                        <thead class="thead table-success">
                            <tr>
                                <th>Curso</th>
                                <th>Proceso</th>
                                <th>Acredit</th>
                                <th>Horas</th>
                                
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
            
            <!-- Contenedor de la segunda tabla -->
            <div class="col-md-6">
                <div class="table-container">

                    <h5>Cursos pendientes</h5>
                    <table class="table table-bordered" id="noAcreditados">
                        <thead class="thead table-success">
                            <tr>
                                <th>Selec</th>
                              
                                <th>Curso</th>
                                <th>Proceso</th>
                                <th>Acredit</th>
                                <th>Horas</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        
                    </table>
                    <div id="totalHorasSeleccionadas">Total Horas Seleccionadas: 0</div>
             </div>
            </div>
            <div class="col-md-6">
            <div class="table-container">         
            
                <h5>Cursos Programados</h5>
                <table class="table table-bordered" id="programados">
                    <thead class="thead table-success">
                        <tr>
                           <th>Curso</th>
                          
                            <th>Proceso</th>
                            <th>Acredit</th>
                            <th>Horas</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
                
            </div>
        </div>
        </div>
        
        <!-- Contenedor para mostrar el porcentaje -->
        <div id="porcentaje-container"></div>
    </div>

    <!-- Bootstrap JS y jQuery (requeridos para el funcionamiento de Bootstrap) -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/js/bootstrap.bundle.min.js"></script>
    
  <!-- html2pdf -->
  <script src="https://unpkg.com/html2pdf.js"></script>

  

    <script>
        document.getElementById("searchForm").addEventListener("submit", function(event) {
            event.preventDefault();
            var searchInput = document.getElementById("searchInput").value;

            // Realizar la petición AJAX para obtener los cursos asociados al trabajador
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "buscar_cursos.php?busqueda=" + encodeURIComponent(searchInput), true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    var response = JSON.parse(xhr.responseText);
                    displaySearchResults(response);
                } else {
                    console.error("Error al buscar cursos:", xhr.statusText);
                }
            };
            xhr.onerror = function() {
                console.error("Error al buscar cursos.");
            };
            xhr.send();
        });

        function displaySearchResults(cursos) {
            var acreditadosTable = document.getElementById("acreditados").getElementsByTagName('tbody')[0];
            var noAcreditadosTable = document.getElementById("noAcreditados").getElementsByTagName('tbody')[0];
            var programadosTable = document.getElementById("programados").getElementsByTagName('tbody')[0];
            var perfilTable = document.getElementById("perfil").getElementsByTagName('tbody')[0];

            var totalHorasAcreditados = 0;
            var totalHorasNoAcreditados = 0;
            var totalHorasProgramadas = 0;

            perfilTable.innerHTML = ""; // Limpiar resultados anteriores
            acreditadosTable.innerHTML = ""; // Limpiar resultados anteriores
            noAcreditadosTable.innerHTML = "";
            programadosTable.innerHTML = "";

            if (cursos.length === 0) {
                // Si no se encontraron cursos, mostrar un mensaje en ambas tablas
                perfilTable.innerHTML = "<tr><td colspan='2'>No se encontraron cursos para el trabajador buscado.</td></tr>";
                acreditadosTable.innerHTML = "<tr><td colspan='4'>No se encontraron cursos acreditados para el trabajador buscado.</td></tr>";
                noAcreditadosTable.innerHTML = "<tr><td colspan='5'>No se encontraron cursos no acreditados para el trabajador buscado.</td></tr>";
                programadosTable.innerHTML = "<tr><td colspan='5'>No se encontraron cursos programados para el trabajador buscado.</td></tr>";
                document.getElementById("porcentaje-container").innerHTML = ""; // Limpiar el porcentaje
                return;
            }
            
           
            var totalCursos = cursos.length;
            var cursosAcreditados = cursos.filter(curso => curso.Acreditacion === '1');
            var cursosProgramados = cursos.filter(curso => curso.Programar === '2');

            var nombreMostrado = false;
           var bateriaMostrada = false;

            cursos.forEach(function (curso, index) {
                var tableRow = document.createElement("tr");
                var cellCurso = document.createElement("td");
                var cellProceso = document.createElement("td");
                var cellAcreditacion = document.createElement("td");
                var cellHoras = document.createElement("td"); // Nueva celda para las horas
                var cellProgramar = document.createElement("td"); // Nueva celda para la programación
            
                
                cellCurso.textContent = curso.Curso;
                cellProceso.textContent = curso.Proceso;
                cellAcreditacion.textContent = curso.Acreditacion;
                cellHoras.textContent = curso.Horas; // Agregar las horas a la celda   
                cellProgramar.textContent = curso.Programar; // Agregar la programación a la celda         

                
                tableRow.appendChild(cellCurso);
                tableRow.appendChild(cellProceso);
                tableRow.appendChild(cellAcreditacion);
                tableRow.appendChild(cellProgramar); // Agregar la celda de programación a la fila
                tableRow.appendChild(cellHoras); // Agregar la celda de horas a la fila
                 
                // Aplicar la clase "small-text" a las celdas de los cursos
                tableRow.classList.add("small-text");
            
       // Mostrar el nombre y la batería del trabajador solo una vez
       if (!nombreMostrado) {
            perfilTable.innerHTML += "<tr><td style='font-size: smaller;'>" + curso.Nombretrabajador + "</td>";
            nombreMostrado = true;
        }

        if (!bateriaMostrada) {
            if (index === cursos.length - 1 || cursos[index + 1].Bateria !== curso.Bateria) {
                perfilTable.innerHTML += "<td style='font-size: smaller;'>" + curso.Bateria + "</td></tr>";
                bateriaMostrada = true;
            }
        }

            
                if (curso.Acreditacion === '1') {

                    var tableRow = document.createElement("tr");
                    var cellCurso = document.createElement("td");
                    var cellProceso = document.createElement("td");
                    var cellAcreditacion = document.createElement("td");
                    var cellHoras = document.createElement("td"); // Nueva celda para las horas

                    cellCurso.textContent = curso.Curso;
                    cellProceso.textContent = curso.Proceso;
                    cellAcreditacion.textContent = curso.Acreditacion;
                    cellHoras.textContent = curso.Horas; // Agregar las horas a la celda

                    tableRow.appendChild(cellCurso);
                    tableRow.appendChild(cellProceso);
                    tableRow.appendChild(cellAcreditacion);
                    tableRow.appendChild(cellHoras); // Agregar la celda de horas a la fila
                    
                    // Aplicar la clase "small-text" a las celdas de los cursos
                    tableRow.classList.add("small-text");

                    acreditadosTable.appendChild(tableRow);
                    totalHorasAcreditados += parseInt(curso.Horas);

                } else if (curso.Programar === '2') {
                        var tableRow = document.createElement("tr");
                        
                        // Crear y asignar valores a las celdas
                        var cellCurso = createCell(curso.Curso);
                        var cellProceso = createCell(curso.Proceso);
                        var cellAcreditacion = createCell(curso.Acreditacion);
                       
                        var cellHoras = createCell(curso.Horas);

                        // Agregar celdas a la fila
                        tableRow.appendChild(cellCurso);
                        tableRow.appendChild(cellProceso);
                        tableRow.appendChild(cellAcreditacion);
                        
                        tableRow.appendChild(cellHoras);

                        // Aplicar la clase "small-text" a las celdas de los cursos
                        tableRow.classList.add("small-text");

                        // Agregar fila a la tabla
                        programadosTable.appendChild(tableRow);

                        // Sumar las horas al total
                        totalHorasProgramadas += parseInt(curso.Horas);

// Función auxiliar para crear una celda con texto
function createCell(text) {
    var cell = document.createElement("td");
    cell.textContent = text;
    return cell;
}

                } else {
                   
                        var tableRow = document.createElement("tr");
                        var checkboxCell = document.createElement("td");
                        var checkbox = document.createElement("input");
                        checkbox.type = "checkbox";
                        checkbox.dataset.idCurso = curso.ID; // Usamos un atributo data- para almacenar el ID del curso.
                        checkbox.dataset.horas = curso.Horas; // Usamos un atributo data- para almacenar las horas.
                        checkbox.addEventListener("change", calcularHorasSeleccionadas); // Agregar evento para cuando se cambie la selección
                        
                        checkboxCell.appendChild(checkbox);
                        tableRow.appendChild(checkboxCell); // Agregamos la celda del checkbox a la fila

                        var cellCurso = document.createElement("td");
                        var cellProceso = document.createElement("td");
                        var cellAcreditacion = document.createElement("td");
                        var cellHoras = document.createElement("td"); // Nueva celda para las horas

                        cellCurso.textContent = curso.Curso;
                        cellProceso.textContent = curso.Proceso;
                        cellAcreditacion.textContent = curso.Acreditacion;
                        cellHoras.textContent = curso.Horas; // Agregar las horas a la celda

                        tableRow.appendChild(cellCurso);
                        tableRow.appendChild(cellProceso);
                        tableRow.appendChild(cellAcreditacion);
                        tableRow.appendChild(cellHoras); // Agregar la celda de horas a la fila
                        
                        // Aplicar la clase "small-text" a las celdas de los cursos
                        tableRow.classList.add("small-text");

                        noAcreditadosTable.appendChild(tableRow);
                        totalHorasNoAcreditados += parseInt(curso.Horas);                
                
                }
            });

            // Agregar la fila de total de horas al final de cada tabla
            appendTotalHoursRow(acreditadosTable, totalHorasAcreditados);           
            appendTotalHoursRow(programadosTable, totalHorasProgramadas);

            // Calcular y mostrar el porcentaje de cursos acreditados
            var porcentajeAcreditados = (cursosAcreditados.length / totalCursos) * 100;
            var porcentajeContainer = document.getElementById("porcentaje-container");
            

            // Mostrar el porcentaje
            var progressBar = document.createElement("div");
            progressBar.classList.add("progress");
            progressBar.classList.add("mt-3");

            var progressBarInner = document.createElement("div");
            progressBarInner.classList.add("progress-bar");
            progressBarInner.classList.add("bg-success");
            progressBarInner.setAttribute("role", "progressbar");
            progressBarInner.setAttribute("aria-valuenow", porcentajeAcreditados.toFixed(2));
            progressBarInner.setAttribute("aria-valuemin", "0");
            progressBarInner.setAttribute("aria-valuemax", "100");
            progressBarInner.style.width = porcentajeAcreditados.toFixed(2) + "%";
            progressBarInner.textContent = porcentajeAcreditados.toFixed(2) + "%";

            progressBar.appendChild(progressBarInner);

            var porcentajeParagraph = document.createElement("p");
            porcentajeParagraph.textContent = "Porcentaje de cursos acreditados: ";

            porcentajeContainer.appendChild(porcentajeParagraph);
            porcentajeContainer.appendChild(progressBar);
        }

        // Función para agregar la fila de total de horas
        function appendTotalHoursRow(table, totalHours) {
            var totalRow = table.insertRow(-1);
            var cellEmpty = totalRow.insertCell(0);
            var cellEmpty2 = totalRow.insertCell(1);
            var cellTotalLabel = totalRow.insertCell(2);
            var cellTotalHours = totalRow.insertCell(3);

            cellTotalLabel.textContent = "Total de Horas";
            cellTotalHours.textContent = totalHours;
        }

        function calcularHorasSeleccionadas() {
            var checkboxes = document.querySelectorAll("#noAcreditados input[type='checkbox']:checked");
            var totalHorasSeleccionadas = 0;

            checkboxes.forEach(function(checkbox) {
                totalHorasSeleccionadas += parseInt(checkbox.dataset.horas);
            });

            document.getElementById("totalHorasSeleccionadas").textContent = "Total Horas Seleccionadas: " + totalHorasSeleccionadas;
        }

        function guardarCambios() {
            var tablaPendientes = document.getElementById("noAcreditados");
            var filasAEliminar = [];

            for (var i = 1; i < tablaPendientes.rows.length; i++) {
                var fila = tablaPendientes.rows[i];

                if (fila.cells[0].querySelector('input[type="checkbox"]').checked) {
                    var idCurso = fila.cells[0].querySelector('input[type="checkbox"]').dataset.idCurso; // Obtener el ID del curso
                    actualizarCursoAcreditado(idCurso); // Llamar a la función para actualizar el curso acreditado
                    filasAEliminar.push(i);
                }
            }

            for (var k = filasAEliminar.length - 1; k >= 0; k--) {
                tablaPendientes.deleteRow(filasAEliminar[k]);
            }
        }   

        function actualizarCursoAcreditado(idCurso) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "marcar_cursos_acreditados.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    if (xhr.status === 200) {
                        alert("Cursos marcados como acreditados correctamente.");
                        var tablaAcreditados = document.getElementById("acreditados").getElementsByTagName("tbody")[0];
                        var tablaPendientes = document.getElementById("noAcreditados").getElementsByTagName("tbody")[0];
                        var tablaProgramados = document.getElementById("programados").getElementsByTagName("tbody")[0];
                        var cursosAcreditados = JSON.parse(xhr.responseText);
                        actualizarTabla(tablaAcreditados, cursosAcreditados);
                        var cursosPendientes = obtenerCursosPendientes();
                        actualizarTabla(tablaPendientes, cursosPendientes);
                        var cursosProgramados = obtenerCursosProgramados();
                        actualizarTabla(tablaProgramados, cursosProgramados);
                    } else {
                        console.error("Error al marcar cursos como acreditados:", xhr.statusText);
                    }
                }
            };
            xhr.onerror = function() {
                console.error("Error al marcar cursos como acreditados.");
            };
            xhr.send("idCurso=" + idCurso);
        }

        function obtenerCursosPendientes() {
            var tablaPendientes = document.getElementById("noAcreditados").getElementsByTagName("tbody")[0];
            var cursosPendientes = [];

            for (var i = 1; i < tablaPendientes.rows.length; i++) {
                var fila = tablaPendientes.rows[i];
                var curso = {
                    ID: fila.cells[0].querySelector("input[type='checkbox']").dataset.idCurso,
                    Curso: fila.cells[1].textContent,
                    Proceso: fila.cells[2].textContent,
                    Acreditacion: fila.cells[3].textContent,
                    Horas: fila.cells[4].textContent
                };
                cursosPendientes.push(curso);
            }

            return cursosPendientes;
        }

        function obtenerCursosProgramados() {
            var tablaProgramados = document.getElementById("programados").getElementsByTagName("tbody")[0];
            var cursosProgramados = [];

            for (var i = 1; i < tablaProgramados.rows.length; i++) {
                var fila = tablaProgramados.rows[i];
                var curso = {
                    ID: fila.cells[0].querySelector("input[type='checkbox']").dataset.idCurso,
                    Curso: fila.cells[1].textContent,
                    Proceso: fila.cells[2].textContent,
                    Programar: fila.cells[3].textContent,
                    Horas: fila.cells[4].textContent
                };
                cursosProgramados.push(curso);
            }

            return cursosProgramados;
        }

        function actualizarTabla(tabla, cursos) {
            tabla.innerHTML = ""; // Limpiar tabla

            cursos.forEach(function(curso) {
                var fila = tabla.insertRow();
                var checkboxCell = fila.insertCell();
                var cursoCell = fila.insertCell();
                var procesoCell = fila.insertCell();
                var acreditacionCell = fila.insertCell();
                var horasCell = fila.insertCell();

                checkboxCell.innerHTML = '<input type="checkbox" data-id="' + curso.ID + '" data-horas="' + curso.Horas + '" onchange="calcularHorasSeleccionadas()">';
                cursoCell.textContent = curso.Curso;
                procesoCell.textContent = curso.Proceso;
                acreditacionCell.textContent = curso.Acreditacion;
                horasCell.textContent = curso.Horas;
            });
        }

   
        function descargarComoExcel() {
            var dataType = 'data:application/vnd.ms-excel';
            // Asegúrate de que estas líneas capturen las tablas correctas por su ID.
            var tableSelectPerfil = document.getElementById("perfil").outerHTML.replace(/ /g, '%20');
            var tableSelectProgramados = document.getElementById("programados").outerHTML.replace(/ /g, '%20');
        

            var tab_text = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
            tab_text += '<head><xml><x:ExcelWorkbook><x:ExcelWorksheets>';
            tab_text += '<x:ExcelWorksheet><x:Name>Hoja1</x:Name><x:WorksheetOptions><x:Panes></x:Panes></x:WorksheetOptions></x:ExcelWorksheet>';
            tab_text += '</x:ExcelWorksheets></x:ExcelWorkbook></xml></head><body>';
            tab_text += "<table border='1'>";

             // Agregar texto antes de la tabla
            tab_text += "<p> </p>";

            tab_text += "<table border='1'>";


            // Tabla Perfil
            tab_text += "<tr><td colspan='4'>Perfil</td></tr>";
            tab_text += tableSelectPerfil;
            tab_text += "<tr><td colspan='6'></td></tr>"; // Separador entre tablas

        
                
            // Tabla Programados
            tab_text += "<tr><td colspan='5'>Programados</td></tr>";
            tab_text += tableSelectProgramados;
            tab_text += "<tr><td colspan='5'></td></tr>"; // Separador entre tablas
            tab_text += '</table>';

            // Agregar texto después de las tablas
            tab_text += "<p> ING. Ramon de Jesus</p>";


            tab_text += '</table></body></html>';

            var downloadLink = document.createElement("a");
            document.body.appendChild(downloadLink);

            downloadLink.href = dataType + ', ' + tab_text;
            downloadLink.download = 'Cursos.xls';
            downloadLink.click();
            document.body.removeChild(downloadLink);

}


    </script>
    <br>
</body>
</html>

