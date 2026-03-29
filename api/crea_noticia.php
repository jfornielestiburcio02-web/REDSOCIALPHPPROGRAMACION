<?php
// 1. CONFIGURACIÓN DE SEGURIDAD (Token de Google para entrar al panel)
$tokenUrl = $_GET['auth_token'] ?? null;
if (!$tokenUrl || strlen($tokenUrl) < 50) {
    header("Location: /index.xlx");
    exit;
}

// 2. TUS DATOS DE GITHUB (Cámbialos por los tuyos)
$repoOwner = "TU_USUARIO_DE_GITHUB"; 
$repoName = "TU_NOMBRE_DE_REPO";
$githubToken = "SECRETO_CREA_NOTICIA_HTML"; // <--- AQUÍ ESTÁ TU SECRETO

$mensaje = "";

if (isset($_POST['crear_final'])) {
    $slug = $_POST['slug'];
    $titulo = $_POST['titulo_grande'];
    $cuerpo = $_POST['cuerpo_noticia'];
    $imagen = $_POST['imagen_url'];

    // RUTA DONDE SE CREARÁ EL ARCHIVO FÍSICO
    $path = "noticias/informaticadesdecero/" . $slug . ".php";

    // EL CONTENIDO DEL ARCHIVO PHP QUE SE VA A GENERAR
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
    <style>body{font-family:Verdana; padding:50px;} .cont{max-width:800px; margin:auto;}</style>
</head>
<body>
    <div class='cont'>
        <h1><?php echo \$titulo; ?></h1>
        <?php if(\$imagen): ?><img src='<?php echo \$imagen; ?>' style='width:100%'><?php endif; ?>
        <p><?php echo nl2br(\$cuerpo); ?></p>
        <hr>
        <h3>Comentarios</h3>
        <form><input type='text' placeholder='Nombre'><br><textarea></textarea><br><button>Enviar</button></form>
    </div>
</body>
</html>";

    $encodedContent = base64_encode($contenidoPHP);

    // LLAMADA A LA API DE GITHUB PARA CREAR EL ARCHIVO FÍSICO
    $url = "https://api.github.com/repos/$repoOwner/$repoName/contents/$path";
    
    $data = json_encode([
        "message" => "Creando noticia: $slug",
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
        $mensaje = "<div style='color:green; padding:20px; border:2px solid green;'><b>¡CONSEGUIDO!</b> El archivo se ha creado en GitHub. Vercel se está actualizando (espera 1 minuto).</div>";
    } else {
        $mensaje = "<div style='color:red;'>Error al crear: " . $response . "</div>";
    }
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
    <title>Panel de Creación Real</title>
    <style>body{font-family:Verdana; background:#f4f4f4; padding:20px;} .box{background:#fff; padding:40px; max-width:600px; margin:auto; border-top:8px solid #000;}</style>
</head>
<body>
    <div class="box">
        <h1>CREAR NOTICIA FÍSICA</h1>
        <?php echo $mensaje; ?>
        
        <form method="POST">
            <label>Slug (nombre-archivo):</label>
            <input type="text" name="slug" style="width:100%; padding:10px;" required>
            
            <label>Título:</label>
            <input type="text" name="titulo_grande" style="width:100%; padding:10px;" required>
            
            <label>Cuerpo:</label>
            <textarea name="cuerpo_noticia" style="width:100%; height:150px;" required></textarea>
            
            <label>URL Imagen:</label>
            <input type="text" name="imagen_url" style="width:100%; padding:10px;">
            
            <button type="submit" name="crear_final" style="width:100%; background:#000; color:#fff; padding:20px; font-weight:bold; margin-top:20px; cursor:pointer;">
                CREAR ARCHIVO .PHP EN GITHUB
            </button>
        </form>
    </div>
</body>
</html>
