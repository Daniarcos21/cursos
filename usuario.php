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
            margin-bottom: 10px;
        }

        /* Estilo para los contenedores de las tablas */
        .table-container {
            margin-bottom: 20px;
        }
        .small-text {
         font-size: 08px; /* Puedes ajustar el valor según tus necesidades */
        }
        #searchInput {
    width: 500px; /* Tamaño deseado */
        }
    .table-container table td,
    .table-container table th {
        font-size: 10px; /* Establecer el tamaño de fuente pequeño para todas las celdas */
    }

        /* Estilo para el contenedor del porcentaje */
        #porcentaje-container {
            margin-top: 20px;
            font-size: 18px;
        }
        /* Estilo de la tabla de cursos no acreditados */
        

    </style>
</head>
<body>
    <div class="container">
        <h5>Ingrese el Nombre o RP</h5>
        <form id="searchForm">
            
    <div class="form-row">
        <div class="col-6">
            <input type="text" class="form-control" id="searchInput" name="searchInput" required>
        </div>
        <div class="col-1">
            <button type="submit" class="btn btn-primary">Buscar</button>
        </div>
        <div class="col-3">
       
        <button onclick="descargarComoExcel()" class="btn btn-success">Descargar como Excel</button>

        </div>
    </div>
</form>


        <div class="row">
            <!-- Contenedor de la primera tabla -->
            <div class="col-md-6">
                <div class="table-container">
                    <h5>Cursos Acreditados</h5>
                    <table class="table table-bordered" id="acreditados">
                        <thead class="thead-dark">
                            <tr>
                                <th>Curso</th>
                                <th>Proceso</th>
                                <th>Acred</th>
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
                        <thead class="thead-dark">
                            <tr>
                               
                                <th>Curso</th>
                                <th>Proceso</th>
                                <th>Pendte</th>
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
    <!-- Agrega esto en la sección head de tu HTML -->
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>




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
            var totalHorasAcreditados = 0;
            var totalHorasNoAcreditados = 0;

            acreditadosTable.innerHTML = ""; // Limpiar resultados anteriores
            noAcreditadosTable.innerHTML = "";

            if (cursos.length === 0) {
                // Si no se encontraron cursos, mostrar un mensaje en ambas tablas
                acreditadosTable.innerHTML = "<tr><td colspan='4'>No se encontraron cursos acreditados para el trabajador buscado.</td></tr>";
                noAcreditadosTable.innerHTML = "<tr><td colspan='3'>No se encontraron cursos no acreditados para el trabajador buscado.</td></tr>";
                document.getElementById("porcentaje-container").innerHTML = ""; // Limpiar el porcentaje
                return;
            }

            var totalCursos = cursos.length;
            var cursosAcreditados = cursos.filter(curso => curso.Acreditacion === '1');

            cursos.forEach(function (curso) {
            var tableRow = document.createElement("tr");
            var cellCurso = document.createElement("td");
            var cellProceso = document.createElement("td");
            var cellAcreditacion = document.createElement("td");
            var cellHoras = document.createElement("td"); // Nueva celda para las horas

            cellCurso.textContent = curso.Curso;
            cellProceso.textContent = curso.Proceso;
            cellAcreditacion.textContent = curso.Acreditacion;
            cellHoras.textContent = curso.Horas; // Agregar las horas a la celda

            // Aplicar la clase "small-text" a las celdas de los cursos
            cellCurso.classList.add("small-text");

            tableRow.appendChild(cellCurso);
            tableRow.appendChild(cellProceso);
            tableRow.appendChild(cellAcreditacion);
            tableRow.appendChild(cellHoras); // Agregar la celda de horas a la fila
            
            if (curso.Acreditacion === '1') {
                acreditadosTable.appendChild(tableRow);
                totalHorasAcreditados += parseInt(curso.Horas);
            } else {
                noAcreditadosTable.appendChild(tableRow);
                totalHorasNoAcreditados += parseInt(curso.Horas);
            }
        });

            // Agregar la fila de total de horas al final de cada tabla
            appendTotalHoursRow(acreditadosTable, totalHorasAcreditados);
            appendTotalHoursRow(noAcreditadosTable, totalHorasNoAcreditados);

            // Calcular y mostrar el porcentaje de cursos acreditados
            var porcentajeAcreditados = (cursosAcreditados.length / totalCursos) * 100;
            var porcentajeContainer = document.getElementById("porcentaje-container");
            porcentajeContainer.innerHTML = "Porcentaje de Cursos Acreditados: " + porcentajeAcreditados.toFixed(2) + "%";
        

        // Función para agregar la fila de total de horas
        function appendTotalHoursRow(table, totalHours) {
            var totalRow = table.insertRow(-1);
            var cellEmpty = totalRow.insertCell(0);
            var cellEmpty2 = totalRow.insertCell(1);
            var cellTotalLabel = totalRow.insertCell(2);
            var cellTotalHours = totalRow.insertCell(3);

            cellTotalLabel.textContent = "Total Horas";
            cellTotalHours.textContent = totalHours;
        }

    
 
     // Mostrar el porcentaje         
     // Crear un elemento de barra de progreso
        var progressBar = document.createElement("div");
        progressBar.classList.add("progress");
        progressBar.classList.add("mt-3");

        // Crear un elemento de barra de progreso interior
        var progressBarInner = document.createElement("div");
        progressBarInner.classList.add("progress-bar");
        progressBarInner.classList.add("bg-success");
        progressBarInner.setAttribute("role", "progressbar");
        progressBarInner.setAttribute("aria-valuenow", porcentajeAcreditados.toFixed(2));
        progressBarInner.setAttribute("aria-valuemin", "0");
        progressBarInner.setAttribute("aria-valuemax", "100");
        progressBarInner.style.width = porcentajeAcreditados.toFixed(2) + "%";
        progressBarInner.textContent = porcentajeAcreditados.toFixed(2) + "%";

        // Agregar la barra de progreso interior al contenedor de la barra de progreso
       progressBar.appendChild(progressBarInner);

       // Obtener el contenedor del porcentaje
        var porcentajeContainer = document.getElementById("porcentaje-container");

        // Limpiar el contenido existente
        porcentajeContainer.innerHTML = "";

        // Crear un elemento de párrafo para mostrar el porcentaje de cursos acreditados
        var porcentajeParagraph = document.createElement("p");
        porcentajeParagraph.textContent = "Porcentaje de cursos acreditados: ";

        // Agregar el párrafo y la barra de progreso al contenedor del porcentaje
        porcentajeContainer.appendChild(porcentajeParagraph);
        porcentajeContainer.appendChild(progressBar);

    
    }
    
    </script>
    <script>
   
  

function descargarComoExcel() {
    var dataType = 'data:application/vnd.ms-excel';
    var tableSelectAcreditados = document.getElementById("acreditados").outerHTML.replace(/ /g, '%20');
    var tableSelectNoAcreditados = document.getElementById("noAcreditados").outerHTML.replace(/ /g, '%20');

    var tab_text = '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    tab_text += '<head><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet>';
    tab_text += '<x:Name>Cursos Acreditados</x:Name>';
    tab_text += '<x:WorksheetOptions><x:Panes></x:Panes></x:WorksheetOptions></x:ExcelWorksheet>';
    tab_text += '<x:ExcelWorksheet>';
    tab_text += '<x:Name>Cursos No Acreditados</x:Name>';
    tab_text += '<x:WorksheetOptions><x:Panes></x:Panes></x:WorksheetOptions></x:ExcelWorksheet>';
    tab_text += '</x:ExcelWorksheets></x:ExcelWorkbook></xml></head><body>';
    tab_text += "<table border='1'>";
    tab_text += tableSelectAcreditados + tableSelectNoAcreditados;
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
