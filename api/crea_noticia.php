<?php
// 1. SEGURIDAD: Token de Google (Viene del login anterior)
$tokenUrl = $_GET['auth_token'] ?? null;
if (!$tokenUrl || strlen($tokenUrl) < 50) {
    header("Location: /index.xlx");
    exit;
}

$projectId = "informaticadesde0";
$mensaje = "";
$superUserAuth = false;

// 2. LOGIN DE SUPERUSUARIO (PHP PRIVADO)
if (isset($_POST['login_super'])) {
    $userEntry = $_POST['user_name'];
    $passEntry = $_POST['user_pass'];

    $url = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents/usuarios/" . urlencode($userEntry);
    $json = @file_get_contents($url);
    
    if ($json) {
        $data = json_decode($json, true);
        $realPass = $data['fields']['contrasena']['stringValue'] ?? '';

        if ($passEntry === $realPass) {
            $superUserAuth = true;
            $mensaje = "<div class='success'>MODO SUPERUSUARIO ACTIVADO. Puedes autorizar noticias.</div>";
        } else {
            $mensaje = "<div class='error'>Contraseña de Superusuario incorrecta.</div>";
        }
    }
}

// 3. ACCIÓN DE CREAR (APLICAR TOKEN SECRETO)
if (isset($_POST['crear_final'])) {
    $docId = $_POST['doc_id']; 
    $slug = $_POST['slug'];
    $tokenSecreto = "SECRETO_CREA_NOTICIA_HTML"; // EL TOKEN QUE ACTIVA MAESTRA.PHP

    $updateUrl = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents/solicitudNoticia/" . $docId . "?updateMask.fieldPaths=secreto&updateMask.fieldPaths=pendiente";
    
    $updateData = [
        "fields" => [
            "secreto" => ["stringValue" => $tokenSecreto],
            "pendiente" => ["booleanValue" => false]
        ]
    ];

    $ch = curl_init($updateUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PATCH");
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($updateData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_exec($ch);
    curl_close($ch);

    $mensaje = "<div class='success'><b>NOTICIA CREADA EXITOSAMENTE</b><br>Ya disponible en: /noticias/informaticadesdecero/$slug</div>";
    $superUserAuth = true; // Mantener sesión visual
}

// 4. CARGAR SOLICITUDES (Solo para Superusuario)
$solicitudes = [];
if ($superUserAuth) {
    $urlGet = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents/solicitudNoticia";
    $jsonGet = @file_get_contents($urlGet);
    if ($jsonGet) {
        $resGet = json_decode($jsonGet, true);
        $solicitudes = $resGet['documents'] ?? [];
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Creador de Noticias - Verdana Style</title>
    <style>
        body { font-family: Verdana, Geneva, sans-serif; background: #f0f0f0; margin: 0; padding: 0; }
        .header { background: #000; color: #fff; padding: 30px; text-align: center; border-bottom: 5px solid #333; }
        .container { max-width: 1000px; margin: 20px auto; background: #fff; padding: 40px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
        
        /* Estilos Superusuario */
        .super-login { background: #222; color: #fff; padding: 20px; border-radius: 5px; margin-bottom: 30px; }
        .super-login input { padding: 10px; margin-right: 10px; border: none; font-family: Verdana; }
        
        /* Tabla */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #eee; padding: 10px; text-align: left; border-bottom: 2px solid #000; }
        td { padding: 15px; border-bottom: 1px solid #ddd; }

        .btn-crear { background: #28a745; color: white; border: none; padding: 10px 20px; cursor: pointer; font-weight: bold; }
        .btn-crear:hover { background: #218838; }

        input[type="text"], textarea { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; font-family: Verdana; box-sizing: border-box; }
        .btn-enviar { background: #000; color: #fff; padding: 20px; border: none; width: 100%; font-weight: bold; cursor: pointer; font-size: 16px; }

        .success { background: #d4edda; color: #155724; padding: 20px; border-left: 6px solid #28a745; margin-bottom: 20px; }
        .error { background: #f8d7da; color: #721c24; padding: 20px; border-left: 6px solid #dc3545; margin-bottom: 20px; }
    </style>
</head>
<body>

<div class="header">
    <h1 style="margin:0; font-size:35px;">INFORMATICADESDECERO</h1>
    <span style="font-size:12px; letter-spacing:4px;">SISTEMA DE GESTIÓN DE NOTICIAS</span>
</div>

<div class="container">

    <?php if (!$superUserAuth): ?>
    <div class="super-login">
        <strong>⚡ ACCESO SUPERUSUARIO:</strong>
        <form method="POST" style="display:inline;">
            <input type="text" name="user_name" placeholder="Usuario" required>
            <input type="password" name="user_pass" placeholder="Password" required>
            <button type="submit" name="login_super" style="background:#ff0; color:#000; font-weight:bold; cursor:pointer; padding:10px; border:none;">ENTRAR</button>
        </form>
    </div>
    <?php endif; ?>

    <?php echo $mensaje; ?>

    <?php if ($superUserAuth): ?>
        <h2>Solicitudes Pendientes</h2>
        <table>
            <tr>
                <th>Identificador</th>
                <th>Título de Noticia</th>
                <th>Acción</th>
            </tr>
            <?php foreach ($solicitudes as $doc): 
                $f = $doc['fields'];
                $slug = $f['nombre']['stringValue'] ?? 'n/a';
                $tit = $f['titulo_grande']['stringValue'] ?? 'Sin título';
                // Extraer ID del documento
                $pathParts = explode('/', $doc['name']);
                $idDoc = end($pathParts);
                
                // Solo mostrar si sigue pendiente
                if (($f['pendiente']['booleanValue'] ?? true) == true):
            ?>
                <tr>
                    <td><code><?php echo $slug; ?></code></td>
                    <td><?php echo $tit; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="doc_id" value="<?php echo $idDoc; ?>">
                            <input type="hidden" name="slug" value="<?php echo $slug; ?>">
                            <button type="submit" name="crear_final" class="btn-crear">ACEPTAR Y CREAR NOTICIA</button>
                        </form>
                    </td>
                </tr>
            <?php endif; endforeach; ?>
        </table>
        <hr style="margin:40px 0;">
    <?php endif; ?>

    <h2>Nueva Solicitud de Noticia</h2>
    <form action="/api/loginPuesto/index.php?auth_token=<?php echo $tokenUrl; ?>" method="POST">
        <label>Nombre de la noticia (Slug para la URL):</label>
        <input type="text" name="nombre_noticia" placeholder="ej: mi-nueva-noticia" required>

        <label>Título Principal:</label>
        <input type="text" name="titulo_grande" placeholder="Escribe el titular..." required>

        <label>Contenido de la Noticia:</label>
        <textarea name="cuerpo_noticia" rows="8" placeholder="Escribe aquí el texto..." required></textarea>

        <label>URL de Imagen:</label>
        <input type="text" name="imagen_url" placeholder="https://...">

        <button type="submit" class="btn-enviar">ENVIAR SOLICITUD A REVISIÓN</button>
    </form>

</div>

</body>
</html>
