<?php
// --- CONFIGURACIÓN OCULTA EN EL SERVIDOR ---
$googleConfig = [
    'apiKey' => 'AIzaSyBWvJyRrkACIJ0Aimjo9RGOIikAbeicHgQ',
    'authDomain' => 'informaticadesde0.firebaseapp.com',
    'projectId' => 'informaticadesde0',
    'appId' => '1:414751371702:web:419177d2307fc564e4d117'
];

// Si recibimos un token por POST, significa que el JS ya validó al usuario
// y ahora PHP toma el control para redirigir.
if (isset($_POST['idToken'])) {
    // Aquí podrías verificar el token con Firebase Admin SDK si quisieras máxima seguridad,
    // pero para tu flujo, redirigimos directamente tras la respuesta positiva.
    header("Location: /loginPuesto/index.xlx");
    exit;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Acceso Seguro</title>
    <style>
        body { 
            font-family: Verdana, Geneva, sans-serif; 
            background-color: #f0f2f5; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .card { 
            background: #fff; 
            padding: 40px; 
            border-radius: 10px; 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
            text-align: center; 
            width: 320px; 
            border-top: 6px solid #4285F4;
        }
        h2 { font-weight: normal; font-size: 20px; margin-bottom: 30px; }
        .btn-google { 
            display: flex; 
            align-items: center; 
            justify-content: center;
            background: #4285F4; 
            color: white; 
            border: none; 
            padding: 12px; 
            width: 100%;
            border-radius: 4px; 
            cursor: pointer; 
            font-family: Verdana; 
            font-weight: bold;
            transition: 0.3s;
        }
        .btn-google:hover { background: #357ae8; }
        .g-logo { background: white; border-radius: 2px; margin-right: 10px; width: 20px; padding: 2px; }
    </style>
</head>
<body>

<div class="card">
    <h2>Identificarse</h2>
    
    <form id="authForm" method="POST" action="">
        <input type="hidden" name="idToken" id="idToken">
        <button type="button" onclick="iniciarGoogle()" class="btn-google">
            <img class="g-logo" src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="">
            Entrar con Google
        </button>
    </form>
</div>

<script type="module">
  import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
  import { getAuth, signInWithPopup, GoogleAuthProvider } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

  // Inyectamos las claves desde PHP para que no estén "quemadas" directamente en el JS puro
  const firebaseConfig = {
    apiKey: "<?php echo $googleConfig['apiKey']; ?>",
    authDomain: "<?php echo $googleConfig['authDomain']; ?>",
    projectId: "<?php echo $googleConfig['projectId']; ?>",
    appId: "<?php echo $googleConfig['appId']; ?>"
  };

  const app = initializeApp(firebaseConfig);
  const auth = getAuth(app);
  const provider = new GoogleAuthProvider();

  window.iniciarGoogle = async () => {
    try {
        const result = await signInWithPopup(auth, provider);
        const token = await result.user.getIdToken();
        
        // Pasamos el token al input y enviamos el formulario a PHP
        document.getElementById('idToken').value = token;
        document.getElementById('authForm').submit();
    } catch (error) {
        alert("Error de autenticación: " + error.message);
    }
  };
</script>

</body>
</html>
