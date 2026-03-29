<?php
$slug = $_GET['slug'] ?? '';
$projectId = "informaticadesde0";

// Buscamos la noticia en Firestore
$url = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents/solicitudNoticia";
$json = file_get_contents($url);
$data = json_decode($json, true);

$noticiaActual = null;
foreach ($data['documents'] as $doc) {
    if ($doc['fields']['nombre']['stringValue'] == $slug) {
        $noticiaActual = $doc['fields'];
        break;
    }
}

if (!$noticiaActual) { die("Noticia no encontrada."); }

// Verificamos el TOKEN SECRETO
$secreto = $noticiaActual['secreto']['stringValue'] ?? '';
if ($secreto !== "SECRETO_CREA_NOTICIA_HTML") {
    die("Esta noticia aún no ha sido validada por el Superusuario.");
}
?>
<!DOCTYPE HTML>
<html>
<head>
    <title><?php echo $noticiaActual['titulo_grande']['stringValue']; ?></title>
    <style>
        body { font-family: Verdana; line-height: 1.6; padding: 40px; background: #f4f4f4; }
        .noticia-wrap { max-width: 800px; margin: auto; background: #fff; padding: 40px; border-top: 10px solid #000; }
        img { max-width: 100%; border-radius: 5px; }
        .comentarios { margin-top: 50px; border-top: 2px solid #eee; padding-top: 20px; }
        .comentario-item { background: #fafafa; padding: 15px; margin-bottom: 10px; border-left: 3px solid #000; }
        .comentario-form input, .comentario-form textarea { width: 100%; padding: 10px; margin: 5px 0; font-family: Verdana; }
    </style>
</head>
<body>

<div class="noticia-wrap">
    <h1><?php echo $noticiaActual['titulo_grande']['stringValue']; ?></h1>
    <?php if(isset($noticiaActual['imagen']['stringValue'])): ?>
        <img src="<?php echo $noticiaActual['imagen']['stringValue']; ?>">
    <?php endif; ?>
    <p><?php echo nl2br($noticiaActual['noticia']['stringValue']); ?></p>

    <div class="comentarios">
        <h3>Comentarios de la Comunidad</h3>
        
        <div class="comentario-form">
            <input type="text" placeholder="Tu Nombre">
            <input type="text" placeholder="URL de tu Imagen/Avatar">
            <textarea placeholder="Escribe tu opinión..."></textarea>
            <button style="background:#000; color:#fff; padding:10px; border:none; cursor:pointer;">Publicar Comentario</button>
        </div>

        <div class="comentario-item">
            <strong>Ejemplo Usuario</strong>
            <p>¡Excelente noticia! Muy bien explicada.</p>
        </div>
    </div>
</div>

</body>
</html>
