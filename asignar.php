<!DOCTYPE html>
<html>
<head>
    <title>Consulta de Pendientes</title>
</head>
<body>

<button onclick="cargarCursos();">Cargar Cursos Pendientes</button>
<div id="resultado">
    <!-- Los cursos pendientes se cargarán aquí -->
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="script.js"></script>

</body>
</html>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
script>
        document.getElementById("searchForm").addEventListener("submit", function(event) {
            event.preventDefault();
            var searchInput = document.getElementById("searchInput").value;

            // Realizar la petición AJAX para obtener los cursos asociados al trabajador
            var xhr = new XMLHttpRequest();
            xhr.open("GET", "consulta.php?asignar=" + encodeURIComponent(searchInput), true);
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

</script>

</body>
</html>
