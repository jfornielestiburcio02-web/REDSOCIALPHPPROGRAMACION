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
        .header { 
            text-align: center; 
            border-bottom: 4px double #000; 
            padding: 20px 0; 
            margin-bottom: 30px; 
            background: #fff; 
            position: relative; /* Para posicionar el botón de login */
        }
        .header h1 { font-size: 60px; margin: 0; text-transform: uppercase; letter-spacing: -2px; }
        .sub-header { border-top: 1px solid #000; border-bottom: 1px solid #000; padding: 5px; margin-top: 10px; font-weight: bold; }

        /* Botón Login */
        .btn-login {
            position: absolute;
            top: 20px;
            right: 30px;
            padding: 8px 15px;
            background-color: #000;
            color: #fff;
            text-decoration: none;
            text-transform: uppercase;
            font-size: 14px;
            font-family: Arial, sans-serif;
            font-weight: bold;
            border: 1px solid #000;
            transition: background 0.3s;
        }
        .btn-login:hover {
            background-color: #444;
        }

        .container { width: 80%; max-width: 900px; margin: auto; }
        
        /* Estilo de la Noticia */
        .noticia { margin-bottom: 50px; background: white; padding: 20px; box-shadow: 2px 2px 10px rgba(0,0,0,0.1); border: 1px solid #ddd; }
        .noticia h2 { font-size: 36px; margin-top: 0; border-bottom: 1px solid #000; padding-bottom: 10px; }
        .noticia p { font-size: 19px; line-height: 1.6; text-align: justify; }
        
        /* Estilo para las etiquetas de imágenes */
        .img-container { display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap; }
        .img-tag { background: #e0e0e0; padding: 5px 12px; font-style: italic; font-size: 13px; border: 1px solid #999; border-radius: 3px; }
    </style>
</head>
<body>

    <div class="header">
        <a href="/informaticadesdecero/IdenUsu.xlx" class="btn-login">Login</a>
        
        <h1>informaticadesdecero</h1>
        <div class="sub-header">
            EDICIÓN DIGITAL - PHP & FIREBASE - <?php echo date('d/m/Y'); ?>
        </div>
    </div>

    <div class="container">
        <?php
        if (isset($data['documents']) && count($data['documents']) > 0) {
            foreach ($data['documents'] as $doc) {
                $f = $doc['fields'];

                // Mapeo según tu captura de Firebase
                $titulo = isset($f['titulo_grande']['stringValue']) ? $f['titulo_grande']['stringValue'] : 'Sin título';
                $textoNoticia = isset($f['noticia']['stringValue']) ? $f['noticia']['stringValue'] : 'Contenido no disponible';
                
                echo "<div class='noticia'>";
                echo "<h2>" . htmlspecialchars($titulo) . "</h2>";
                echo "<p>" . nl2br(htmlspecialchars($textoNoticia)) . "</p>";

                // Renderizar nombres de imágenes si existen en el array
                if (isset($f['imagenes']['arrayValue']['values'])) {
                    echo "<div class='img-container'>";
                    foreach ($f['imagenes']['arrayValue']['values'] as $v) {
                        if (isset($v['stringValue'])) {
                            echo "<span class='img-tag'>🖼️ " . htmlspecialchars($v['stringValue']) . "</span>";
                        }
                    }
                    echo "</div>";
                }
                echo "</div>";
            }
        } else {
            echo "<div style='text-align:center;'><h3>No hay noticias publicadas en la colección 'noticiasRecientes'.</h3></div>";
        }
        ?>
    </div>

</body>
</html>
