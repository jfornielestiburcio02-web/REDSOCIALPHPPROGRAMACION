<?php
// 1. PROTECCIÓN DE RUTA (Lógica Privada)
// Si no existe la cookie que pusimos en el login, lo expulsa
if (!isset($_COOKIE['fb_token'])) {
    header("Location: /index.xlx");
    exit;
}

// 2. PROCESAMIENTO DEL FORMULARIO (PHP)
$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_noticia'])) {
    $projectId = "informaticadesde0";
    $url = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents/solicitudNoticia";

    // Datos que enviamos a Firebase
    $postData = [
        "fields" => [
            "nombre" => ["stringValue" => $_POST['nombre_noticia']],
            "titulo_grande" => ["stringValue" => $_POST['titulo_grande']],
            "noticia" => ["stringValue" => $_POST['cuerpo_noticia']],
            "imagen" => ["stringValue" => $_POST['imagen_url']],
            "pendiente" => ["booleanValue" => true],
            "secreto" => ["stringValue" => "SECRETO_CREA_NOTICIA_HTML"] // Tu clave del repo
        ]
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode === 200) {
        $mensaje = "<p style='color:green; font-weight:bold;'>✔ Solicitud enviada correctamente como PENDIENTE.</p>";
    } else {
        $mensaje = "<p style='color:red;'>Error al enviar a Firebase. Código: $httpCode</p>";
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Panel Privado - INFORMATICADESDECERO</title>
    <style>
        body { font-family: Verdana, Geneva, sans-serif; background-color: #f8f9fa; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        
        h1 { color: #000; border-bottom: 3px solid #000; padding-bottom: 10px; text-transform: uppercase; font-size: 22px; }
        h2 { font-size: 18px; margin-top: 30px; color: #555; }

        .search-box { background: #e9ecef; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        input[type="text"], textarea { width: 100%; padding: 12px; margin: 8px 0; border: 1px solid #ccc; border-radius: 4px; font-family: Verdana; box-sizing: border-box; }
        
        .btn-ok { background-color: #000; color: #fff; border: none; padding: 15px 25px; cursor: pointer; font-weight: bold; width: 100%; font-size: 16px; transition: 0.3s; }
        .btn-ok:hover { background-color: #333; }

        .noticia-item { border-left: 4px solid #ffc107; padding: 10px; background: #fff9e6; margin-bottom: 10px; font-size: 14px; }
        .footer-note { font-size: 11px; color: #777; margin-top: 15px; }
    </style>
</head>
<body>

<div class="container">
    <h1>INFORMATICADESDECERO - PANEL</h1>
    
    <?php echo $mensaje; ?>

    <div class="search-box">
        <strong>Busqueda de noticias</strong>
        <input type="text" placeholder="Escribe para buscar...">
    </div>

    <h2>Recientes:</h2>
    <div class="noticia-item">
        ⚠️ <i>No hay noticias aprobadas hoy. Revisa las solicitudes pendientes abajo.</i>
    </div>

    <hr style="margin: 40px 0; border: 0; border-top: 1px double #ccc;">

    <h2>Solicitud de crear noticia</h2>
    <form method="POST" action="">
        <label>Nombre de la noticia (Identificador):</label>
        <input type="text" name="nombre_noticia" placeholder="ej: noticia-nueva-ia" required>

        <label>TÍTULO GRANDE:</label>
        <input type="text" name="titulo_grande" placeholder="Escribe el titular aquí..." required>

        <label>NOTICIA (Cuerpo):</label>
        <textarea name="cuerpo_noticia" rows="6" placeholder="Escribe el contenido de la noticia..." required></textarea>

        <label>Agregar imagen (URL):</label>
        <input type="text" name="imagen_url" placeholder="https://enlace-a-tu-imagen.jpg">

        <button type="submit" class="btn-ok">OK - ENVIAR A REVISIÓN</button>
    </form>

    <p class="footer-note">Al pulsar OK, la noticia se enviará a la colección <b>solicitudNoticia</b> con estado pendiente.</p>
</div>

<div class="container" style="margin-top: 20px; border-top: 4px solid #4285F4;">
    <h3>Vista previa de Comentarios</h3>
    <div style="font-size: 13px; color: #666;">
        <p>Los usuarios podrán comentar con su <b>Imagen, Nombre y Opinión</b> una vez la noticia sea publicada.</p>
    </div>
</div>

</body>
</html>
