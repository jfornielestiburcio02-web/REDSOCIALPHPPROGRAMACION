<?php
$projectId = "informaticadesde0";
$collection = "noticiasRecientes";
$url = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents/$collection";

$json = file_get_contents($url);
$data = json_decode($json, true);
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Informática desde Cero - Periódico Digital</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; background-color: #f4f1ea; color: #333; margin: 0; padding: 0; }
        
        /* Cabecera estilo Periódico */
        .header { text-align: center; border-bottom: 4px double #000; padding: 20px 0; margin-bottom: 30px; background: #fff; }
        .header h1 { font-size: 60px; margin: 0; text-transform: uppercase; letter-spacing: -2px; }
        .sub-header { border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 5px; margin-top: 10px; font-weight: bold; }

        .container { width: 80%; max-width: 900px; margin: auto; }
        
        /* Estilo de la Noticia */
        .noticia { margin-bottom: 50px; background: white; padding: 20px; box-shadow: 2px 2px 10px rgba(0,0,0,0.1); }
        .noticia h2 { font-size: 36px; margin-top: 0; border-bottom: 1px solid #ddd; }
        .noticia p { font-size: 18px; line-height: 1.6; text-align: justify; }
        
        /* Estilo para las imágenes si las hubiera */
        .img-container { display: flex; gap: 10px; margin-top: 15px; }
        .img-container span { background: #eee; padding: 5px 10px; font-style: italic; font-size: 12px; border: 1px solid #ccc; }
    </style>
</head>
<body>

    <div class="header">
        <h1>informaticadesdecero</h1>
        <div class="sub-header">
            EDICIÓN DIGITAL - PHP & FIREBASE ESTRUCTURA - <?php echo date('d/m/Y'); ?>
        </div>
    </div>

    <div class="container">
        <?php
        if (isset($data['documents'])) {
            foreach ($data['documents'] as $doc) {
                $f = $doc['fields'];

                // Mapeo según tu captura de pantalla:
                $titulo = $f['titulo_grande']['stringValue'] ?? 'Sin título';
                $textoNoticia = $f['noticia']['stringValue'] ?? 'Sin contenido';
                
                // Extraer imágenes (es un array en tu captura)
                $imgs = [];
                if (isset($f['imagenes']['arrayValue']['values'])) {
                    foreach ($f['imagenes']['arrayValue']['values'] as $v) {
                        $imgs[] = $v['stringValue'];
                    }
                }

                echo "<div class='noticia'>";
                echo "<h2>" . htmlspecialchars($titulo) . "</h2>";
                echo "<p>" . nl2br(htmlspecialchars($textoNoticia)) . "</p>";

                if (!empty($imgs)) {
                    echo "<div class='img-container'>";
                    foreach ($imgs as $img) {
                        echo "<span>📍 " . htmlspecialchars($img) . "</span>";
                    }
                    echo "</div>";
                }
                echo "</div>";
            }
        } else {
            echo "<p>No hay noticias que mostrar.</p>";
        }
        ?>
    </div>

</body>
</html>
