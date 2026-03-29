<?php
// Configuración de tu proyecto (Extraída de tus datos)
$projectId = "informaticadesde0";
$collection = "noticiasRecientes";

// URL de la REST API de Firestore
$url = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents/$collection";

// Realizamos la petición GET
$json = file_get_contents($url);
$data = json_decode($json, true);

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Noticias Recientes - Firebase</title>
</head>
<body>

    <h1>Listado de Noticias (desde Firestore)</h1>

    <?php
    if (isset($data['documents'])) {
        echo "<ul>";
        foreach ($data['documents'] as $doc) {
            // Firestore devuelve los datos en un formato específico (campos con tipos)
            // Asumiendo que tus documentos tienen un campo llamado 'titulo' y 'contenido'
            $fields = $doc['fields'];
            
            $titulo = isset($fields['titulo']['stringValue']) ? $fields['titulo']['stringValue'] : 'Sin título';
            $contenido = isset($fields['contenido']['stringValue']) ? $fields['contenido']['stringValue'] : '';

            echo "<li>";
            echo "<strong>" . htmlspecialchars($titulo) . "</strong><br>";
            echo htmlspecialchars($contenido);
            echo "</li><br>";
        }
        echo "</ul>";
    } else {
        echo "<p>No se encontraron noticias o la colección está vacía.</p>";
        // Descomenta la línea de abajo para depurar si no ves nada:
        // print_r($data); 
    }
    ?>

</body>
</html>
