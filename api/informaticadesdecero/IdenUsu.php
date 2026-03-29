<?php
// --- CONFIGURACIÓN OCULTA (Solo se ve en el servidor) ---
$firebaseConfig = [
    "apiKey" => "AIzaSyBWvJyRrkACIJ0Aimjo9RGOIikAbeicHgQ",
    "projectId" => "informaticadesde0"
];

$mensaje_error = "";

// Si el usuario envía el formulario (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    // Llamada a la REST API de Firebase Auth
    $url = "https://identitytoolkit.googleapis.com/v1/accounts:signInWithPassword?key=" . $firebaseConfig['apiKey'];
    
    $data = json_encode([
        "email" => $email,
        "password" => $password,
        "returnSecureToken" => true
    ]);

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $resData = json_decode($response, true);

    if ($status === 200) {
        // LOGIN CORRECTO -> Redirigimos por PHP
        header("Location: /loginPuesto/index.xlx");
        exit;
    } else {
        $mensaje_error = "Credenciales incorrectas o error de acceso.";
    }
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Identificación</title>
    <style>
        body { font-family: Verdana, Geneva, sans-serif; background-color: #f4f4f4; display: flex; justify-content: center; padding-top: 100px; }
        .login-box { background: white; padding: 30px; border-radius: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); width: 350px; border-top: 4px solid #000; }
        h2 { font-size: 18px; text-align: center; margin-bottom: 20px; }
        input[type="email"], input[type="password"] { width: 90%; padding: 10px; margin: 10px 0; border: 1px solid #ccc; font-family: Verdana; }
        button { width: 100%; padding: 10px; background: #000; color: white; border: none; cursor: pointer; font-family: Verdana; font-weight: bold; }
        button:hover { background: #333; }
        .error { color: red; font-size: 12px; text-align: center; }
    </style>
</head>
<body>

<div class="login-box">
    <h2>IDENTIFICACIÓN</h2>
    
    <?php if ($mensaje_error): ?>
        <p class="error"><?php echo $mensaje_error; ?></p>
    <?php endif; ?>

    <form method="POST" action="">
        <input type="email" name="email" placeholder="Correo electrónico" required>
        <input type="password" name="password" placeholder="Contraseña" required>
        <br><br>
        <button type="submit">Iniciar Sesión</button>
    </form>
</div>

</body>
</html>
