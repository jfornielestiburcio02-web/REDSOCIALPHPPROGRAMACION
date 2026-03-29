<?php
// 1. PROTECCIÓN DE RUTA (Lógica Privada por URL)
// Extraemos el token largo que viene en la URL (?auth_token=...)
$tokenUrl = $_GET['auth_token'] ?? null;

// Si el token no existe o es demasiado corto para ser real, denegamos el acceso
if (!$tokenUrl || strlen($tokenUrl) < 50) {
    header("Location: /index.xlx"); // Te expulsa al inicio si no hay token
    exit;
}

// 2. PROCESAMIENTO DEL FORMULARIO (PHP)
$mensaje = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nombre_noticia'])) {
    $projectId = "informaticadesde0";
    $url = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents/solicitudNoticia";

    // Datos que enviamos a Firebase (REST API)
    $postData = [
        "fields" => [
            "nombre" => ["stringValue" => $_POST['nombre_noticia']],
            "titulo_grande" => ["stringValue" => $_POST['titulo_grande']],
            "noticia" => ["stringValue" => $_POST['cuerpo_noticia']],
            "imagen" => ["stringValue" => $_POST['imagen_url']],
            "pendiente" => ["booleanValue" => true],
            "secreto" => ["stringValue" => "SECRETO_CREA_NOTICIA_HTML"]
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
        $mensaje = "<div class='msg-success'>✔ Solicitud enviada correctamente. Se procesará pronto.</div>";
    } else {
        $mensaje = "<div class='msg-error'>Error al conectar con Firebase (Code: $httpCode)</div>";
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Panel Privado - INFORMATICADESDECERO</title>
    <style>
        body { font-family: Verdana, Geneva, sans-serif; background-color: #f0f2f5; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: auto; background: #fff; padding: 30px; border-radius: 12px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); }
        
        h1 { color: #000; border-bottom: 4px solid #000; padding-bottom: 15px; text-transform: uppercase; font-size: 24px; text-align: center; }
        h2 { font-size: 18px; margin-top: 30px; color: #444; border-left: 5px solid #000; padding-left: 10px; }

        .token-display { font-size: 9px; color: #bbb; word-break: break-all; margin-bottom: 20px; text-align: center; }
        
        .search-box { background: #f8f9fa; border: 1px solid #ddd; padding: 15px; border-radius: 8px; margin-bottom: 20px; }
        input[type="text"], textarea { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; border-radius: 6px; font-family: Verdana; box-sizing: border-box; font-size: 14px; }
        
        .btn-ok { background-color: #000; color: #fff; border: none; padding: 18px; cursor: pointer; font-weight: bold; width: 100%; font-size: 16px; border-radius: 6px; transition: 0.3s; margin-top: 10px; }
        .btn-ok:hover { background-color: #333; transform: translateY(-1px); }

        .msg-success { background: #d4edda; color: #155724; padding: 15px; border-radius: 6px; margin-bottom: 20px; font-weight: bold; text-align: center; }
        .msg-error { background: #f8d7da; color: #721c24; padding: 15px; border-radius: 6px; margin-bottom: 20px; text-align: center; }

        .noticia-item { border: 1px dashed #ccc; padding: 15px; background: #fafafa; margin-bottom: 10px; font-size: 13px; }
        .footer-note { font-size: 11px; color: #888; margin-top: 20px; text-align: center; }
    </style>
</head>
<body>

<div class="container">
    <h1>INFORMATICADESDECERO</h1>
    
    <div class="token-display">Sesión verificada: <?php echo substr($tokenUrl, 0, 30); ?>...</div>
    
    <?php echo $mensaje; ?>

    <div class="search-box">
        <strong>Busqueda de noticias</strong>
        <input type="text" placeholder="Escribe el nombre de una noticia existente...">
    </div>

    <h2>Recientes:</h2>
    <div class="noticia-item">
        📌 <i>No hay noticias publicadas recientemente.</i>
    </div>

    <h2>Solicitud de crear noticia</h2>
    <form method="POST" action="">
        <label><b>Nombre de la noticia (Slug):</b></label>
        <input type="text" name="nombre_noticia" placeholder="ej: gran-avance-tecnologico" required>

        <label><b>TÍTULO GRANDE:</b></label>
        <input type="text" name="titulo_grande" placeholder="El titular principal..." required>

        <label><b>NOTICIA (Contenido):</b></label>
        <textarea name="cuerpo_noticia" rows="8" placeholder="Escribe aquí el texto completo..." required></textarea>

        <label><b>Agregar imagen (URL):</b></label>
        <input type="text" name="imagen_url" placeholder="https://dominio.com/imagen.jpg">

        <button type="submit" class="btn-ok">OK - ENVIAR A REVISIÓN</button>
    </form>

    <p class="footer-note">Estado actual: <b>PENDIENTE</b> hasta validación por secreto del repo.</p>
</div>

<div class="container" style="margin-top: 20px; border-top: 6px solid #4285F4; padding-top: 10px;">
    <h3 style="font-size: 16px;">Comentarios de la comunidad</h3>
    <p style="font-size: 12px; color: #777;">Próximamente: Sistema de opiniones con imagen y nombre de usuario.</p>
</div>

</body>
</html>
