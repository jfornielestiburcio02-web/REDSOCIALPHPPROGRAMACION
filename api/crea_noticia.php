<?php
// 1. SEGURIDAD DE ACCESO (Token de Google)
$tokenUrl = $_GET['auth_token'] ?? null;
if (!$tokenUrl || strlen($tokenUrl) < 50) {
    header("Location: /index.xlx");
    exit;
}

// --- CONFIGURACIÓN DE GITHUB ---
$repoOwner = "TU_USUARIO_GITHUB"; // CAMBIA ESTO
$repoName = "TU_NOMBRE_REPO";   // CAMBIA ESTO
$githubToken = "SECRETO_CREA_NOTICIA_HTML"; // Tu secreto configurado

$mensaje = "";

// SOLUCIÓN: Solo ejecutar si se ha pulsado el botón (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_final'])) {
    
    // Recogemos los datos de forma segura
    $slug = $_POST['slug'] ?? 'sin-nombre';
    $titulo = $_POST['titulo_grande'] ?? 'Sin Titulo';
    $cuerpo = $_POST['cuerpo_noticia'] ?? '';
    $imagen = $_POST['imagen_url'] ?? '';

    $path = "noticias/informaticadesdecero/" . $slug . ".php";

    // CONTENIDO DEL PHP QUE SE GENERARÁ
    $contenidoPHP = "<?php
\$titulo = '" . addslashes($titulo) . "';
\$cuerpo = '" . addslashes($cuerpo) . "';
\$imagen = '" . addslashes($imagen) . "';
?>
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title><?php echo \$titulo; ?></title>
    <style>
        body { font-family: Verdana; padding: 50px; line-height: 1.6; }
        .cont { max-width: 800px; margin: auto; border-top: 10px solid #000; padding-top: 20px; }
        img { max-width: 100%; border-radius: 8px; margin: 20px 0; }
        .coments { margin-top: 50px; background: #eee; padding: 20px; }
    </style>
</head>
<body>
    <div class='cont'>
        <h1><?php echo \$titulo; ?></h1>
        <?php if(\$imagen): ?><img src='<?php echo \$imagen; ?>'><?php endif; ?>
        <p><?php echo nl2br(\$cuerpo); ?></p>
        
        <div class='coments'>
            <h3>Comentarios</h3>
            <form><input type='text' placeholder='Tu nombre'><br><textarea placeholder='Opinión'></textarea><br><button type='button'>Publicar</button></form>
        </div>
    </div>
</body>
</html>";

    $encodedContent = base64_encode($contenidoPHP);

    // LLAMADA A LA API DE GITHUB
    $url = "https://api.github.com/repos/$repoOwner/$repoName/contents/$path";
    
    $data = json_encode([
        "message" => "Nueva noticia: $slug",
        "content" => $encodedContent,
        "branch" => "main"
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: token $githubToken",
        "User-Agent: Vercel-PHP-Script",
        "Content-Type: application/json"
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode == 201) {
        $mensaje = "<div style='color:green; font-weight:bold; border:2px solid green; padding:15px;'>✔ ¡COJONES! Noticia creada en GitHub. Espera 1 min a que Vercel termine el deploy.</div>";
    } else {
        $errorArr = json_decode($response, true);
        $mensaje = "<div style='color:red; font-weight:bold; padding:15px;'>❌ Error: " . ($errorArr['message'] ?? 'Desconocido') . " (Código: $httpCode)</div>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Panel de Creación</title>
    <style>
        body { font-family: Verdana; background: #f4f4f4; padding: 40px; }
        .box { background: #fff; max-width: 700px; margin: auto; padding: 30px; border-top: 8px solid #000; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        input, textarea { width: 100%; padding: 12px; margin: 10px 0; border: 1px solid #ccc; box-sizing: border-box; font-family: Verdana; }
        .btn { background: #000; color: #fff; padding: 20px; width: 100%; border: none; font-weight: bold; cursor: pointer; font-size: 16px; }
    </style>
</head>
<body>

<div class="box">
    <h1>CREAR NOTICIA REAL</h1>
    <?php echo $mensaje; ?>

    <form method="POST">
        <label>Nombre de URL (slug):</label>
        <input type="text" name="slug" placeholder="ej: noticia-tecnologia-2024" required>

        <label>Título de la Noticia:</label>
        <input type="text" name="titulo_grande" placeholder="Titular impactante" required>

        <label>Contenido:</label>
        <textarea name="cuerpo_noticia" rows="10" placeholder="Escribe la noticia aquí..." required></textarea>

        <label>URL de Imagen:</label>
        <input type="text" name="imagen_url" placeholder="https://...">

        <button type="submit" name="crear_final" class="btn">PUBLICAR Y CREAR ARCHIVO .PHP</button>
    </form>
</div>

</body>
</html>
