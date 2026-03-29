<?php
// --- CONFIGURACIÓN OCULTA (Solo vive aquí en el servidor) ---
$googleConfig = [
    'apiKey' => 'AIzaSyBWvJyRrkACIJ0Aimjo9RGOIikAbeicHgQ',
    'authDomain' => 'informaticadesde0.firebaseapp.com',
    'projectId' => 'informaticadesde0',
    'appId' => '1:414751371702:web:419177d2307fc564e4d117'
];

if (isset($_POST['idToken'])) {
    header("Location: /loginPuesto/index.xlx?auth_token=" . $_POST['idToken']);
    exit;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Acceso</title>
    <style>
        body { font-family: Verdana; background: #f0f2f5; display: flex; justify-content: center; align-items: center; height: 100vh; }
        .card { background: #fff; padding: 40px; border-radius: 10px; text-align: center; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-top: 6px solid #4285F4; }
        .btn { background: #4285F4; color: white; border: none; padding: 12px 20px; cursor: pointer; font-family: Verdana; font-weight: bold; border-radius: 4px; }
    </style>
</head>
<body>

<div class="card">
    <h2>Identificarse</h2>
    <form id="authForm" method="POST">
        <input type="hidden" name="idToken" id="idToken">
        <button type="button" id="btnGoogle" class="btn">Entrar con Google</button>
    </form>
</div>

<script type="module">
  import { initializeApp } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-app.js";
  import { getAuth, signInWithPopup, GoogleAuthProvider } from "https://www.gstatic.com/firebasejs/10.8.0/firebase-auth.js";

  const _0x1a2b = <?php echo json_encode(array_values($googleConfig)); ?>;
  
  const config = {
    apiKey: _0x1a2b[0],
    authDomain: _0x1a2b[1],
    projectId: _0x1a2b[2],
    appId: _0x1a2b[3]
  };

  const app = initializeApp(config);
  const auth = getAuth(app);
  const provider = new GoogleAuthProvider();

  document.getElementById('btnGoogle').onclick = async () => {
    try {
        const res = await signInWithPopup(auth, provider);
        document.getElementById('idToken').value = await res.user.getIdToken();
        document.getElementById('authForm').submit();
    } catch (e) { alert(e.message); }
  };
</script>
</body>
</html>
