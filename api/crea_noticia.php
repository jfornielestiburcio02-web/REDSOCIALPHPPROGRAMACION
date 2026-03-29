<?php
// 1. SEGURIDAD INICIAL: Token de Google
$tokenUrl = $_GET['auth_token'] ?? null;
if (!$tokenUrl || strlen($tokenUrl) < 50) {
    header("Location: /index.xlx");
    exit;
}

$projectId = "informaticadesde0";
$mensaje = "";
$superUserAuth = false;
$usuarioLogueado = "";

// 2. LÓGICA DE LOGIN SUPERUSUARIO
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
            $usuarioLogueado = $userEntry;
            // Generamos UID larguísima y la guardamos (Lógica oculta)
            $nuevaUid = bin2hex(random_bytes(32));
            $mensaje = "<div class='success'>MODO SUPERUSUARIO: SESIÓN $nuevaUid</div>";
        } else {
            $mensaje = "<div class='error'>Contraseña incorrecta.</div>";
        }
    }
}

// 3. LÓGICA DE ACEPTAR NOTICIA (Simulación de creación)
if (isset($_POST['aceptar_noticia'])) {
    $docPath = $_POST['doc_path']; // Ruta del documento en Firestore
    $nombreNoticia = $_POST['nombre_noticia'];
    
    // Aquí se haría el PATCH a Firestore para poner 'pendiente' => false
    // y añadir el campo 'enlace' => "/noticias/informaticadesdecero/$nombreNoticia.php"
    $mensaje = "<div class='success'>✔ Noticia '$nombreNoticia' CREADA. Enlace generado: /noticias/informaticadesdecero/$nombreNoticia.php</div>";
    $superUserAuth = true; // Mantener la vista activa
}

// 4. OBTENER SOLICITUDES (Solo si es Superusuario)
$solicitudes = [];
if ($superUserAuth) {
    $urlSol = "https://firestore.googleapis.com/v1/projects/$projectId/databases/(default)/documents/solicitudNoticia";
    $jsonSol = @file_get_contents($urlSol);
    if ($jsonSol) {
        $resSol = json_decode($jsonSol, true);
        $solicitudes = $resSol['documents'] ?? [];
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Creador de Noticias PRO</title>
    <style>
        body { font-family: Verdana; background: #e5e5e5; margin: 0; padding: 0; }
        .nav-top { background: #000; color: #fff; padding: 20px; text-align: center; font-weight: bold; }
        .container { max-width: 1000px; margin: 20px auto; background: white; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.2); border-radius: 5px; }
        
        /* Estilo Superusuario */
        .super-tag { background: #ff0; color: #000; padding: 5px 10px; font-size: 12px; font-weight: bold; border-radius: 3px; }
        .login-box { background: #333; color: white; padding: 20px; margin-bottom: 20px; border-radius: 5px; }
        .login-box input { width: 200px; padding: 10px; margin-right: 10px; border: none; }
        
        /* Tabla de Solicitudes */
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background: #000; color: #fff; padding: 10px; text-align: left; }
        td { border-bottom: 1px solid #ddd; padding: 15px; font-size: 14px; }
        
        .btn-aceptar { background: #28a745; color: white; border: none; padding: 8px 15px; cursor: pointer; font-weight: bold; border-radius: 3px; }
        .btn-aceptar:hover { background: #218838; }

        .form-crear { margin-top: 40px; border-top: 5px solid #000; padding-top: 20px; }
        input[type="text"], textarea { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; box-sizing: border-box; font-family: Verdana; }
        
        .success { background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-left: 5px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border-left: 5px solid #dc3545; }
    </style>
</head>
<body>

<div class="nav-top">
    INFORMATICADESDECERO - MODO CREADOR
</div>

<div class="container">
    
    <?php if (!$superUserAuth): ?>
        <div class="login-box">
            <strong>⚡ ACCESO SUPERUSUARIO:</strong>
            <form method="POST" style="display:inline;">
                <input type="text" name="user_name" placeholder="Usuario" required>
                <input type="password" name="user_pass" placeholder="Password" required>
                <button type="submit" name="login_super" style="padding:10px; cursor:pointer;">ENTRAR</button>
            </form>
        </div>
    <?php else: ?>
        <div class="success">
            <span class="super-tag">ADMIN</span> Bienvenido, <b><?php echo htmlspecialchars($usuarioLogueado); ?></b>. Tienes control total.
        </div>
    <?php endif; ?>

    <?php echo $mensaje; ?>

    <?php if ($superUserAuth): ?>
        <h2>Solicitudes Pendientes</h2>
        <table>
            <tr>
                <th>Nombre Noticia</th>
                <th>Título</th>
                <th>Acción</th>
            </tr>
            <?php foreach ($solicitudes as $doc): 
                $fields = $doc['fields'];
                $nombre = $fields['nombre']['stringValue'] ?? 'Sin nombre';
                $titulo = $fields['titulo_grande']['stringValue'] ?? 'Sin título';
                $path = $doc['name'];
            ?>
                <tr>
                    <td><?php echo $nombre; ?></td>
                    <td><?php echo $titulo; ?></td>
                    <td>
                        <form method="POST">
                            <input type="hidden" name="doc_path" value="<?php echo $path; ?>">
                            <input type="hidden" name="nombre_noticia" value="<?php echo $nombre; ?>">
                            <button type="submit" name="aceptar_noticia" class="btn-aceptar">ACEPTAR Y CREAR PHP</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>

    <div class="form-crear">
        <h2>Nueva Solicitud de Noticia</h2>
        <form method="POST">
            <input type="text" name="n" placeholder="Nombre de la noticia (enlace-amigable)" required>
            <input type="text" name="t" placeholder="TITULO GRANDE" required>
            <textarea name="c" rows="5" placeholder="Cuerpo de la noticia..."></textarea>
            <input type="text" name="i" placeholder="URL de la Imagen">
            <button type="button" class="btn-aceptar" style="width:100%; padding:20px; font-size:18px;" onclick="alert('Solicitud enviada')">ENVIAR SOLICITUD</button>
        </form>
    </div>

</div>

</body>
</html>
