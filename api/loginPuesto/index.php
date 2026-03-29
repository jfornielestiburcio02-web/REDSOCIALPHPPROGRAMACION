<?php
// 1. PROTECCIÓN DE RUTA
$tokenUrl = $_GET['auth_token'] ?? null;

// Si no hay token o es inválido, expulsar
if (!$tokenUrl || strlen($tokenUrl) < 50) {
    header("Location: /index.xlx");
    exit;
}

// 2. LÓGICA DE CERRAR SESIÓN
if (isset($_GET['logout'])) {
    // Redirigimos al inicio sin el token, limpiando la sesión visual
    header("Location: /index.xlx");
    exit;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Panel de Control - INFORMATICADESDECERO</title>
    <style>
        /* Pantalla Completa y Reset */
        html, body { 
            height: 100%; 
            margin: 0; 
            padding: 0; 
            font-family: Verdana, Geneva, sans-serif; 
            background-color: #f0f0f0; 
        }

        /* Cabecera de Siempre (Mejorada) */
        .header-full {
            background-color: #000;
            color: #fff;
            padding: 40px 0;
            text-align: center;
            position: relative;
            border-bottom: 8px solid #333;
        }
        .header-full h1 {
            margin: 0;
            font-size: 50px;
            letter-spacing: -2px;
            text-transform: uppercase;
        }
        .header-full .sub {
            font-size: 14px;
            letter-spacing: 5px;
            color: #aaa;
            margin-top: 10px;
        }

        /* Botón Cerrar Sesión */
        .btn-logout {
            position: absolute;
            top: 20px;
            right: 20px;
            background: transparent;
            color: #ff4444;
            border: 1px solid #ff4444;
            padding: 8px 15px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            transition: 0.3s;
        }
        .btn-logout:hover {
            background: #ff4444;
            color: #fff;
        }

        /* Contenido Principal */
        .main-content {
            padding: 40px;
            max-width: 1200px;
            margin: auto;
            min-height: 60vh;
        }

        .section-box {
            background: #fff;
            padding: 30px;
            border-radius: 4px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
            margin-bottom: 30px;
        }

        h2 { border-bottom: 2px solid #000; padding-bottom: 10px; margin-top: 0; }

        /* Buscador */
        .search-container input {
            width: 100%;
            padding: 20px;
            font-size: 18px;
            border: 2px solid #eee;
            font-family: Verdana;
            box-sizing: border-box;
        }

        /* Noticia Reciente */
        .noticia-card {
            background: #fff;
            border: 1px solid #ddd;
            padding: 20px;
            margin-top: 10px;
        }

        /* Botón Crear Nueva (Abajo del todo) */
        .footer-actions {
            text-align: center;
            padding: 50px 0;
            background: #fff;
            border-top: 1px solid #ddd;
        }
        .btn-crear-nueva {
            background-color: #000;
            color: #fff;
            padding: 20px 50px;
            text-decoration: none;
            font-size: 20px;
            font-weight: bold;
            display: inline-block;
            border-radius: 50px;
            transition: 0.3s;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        .btn-crear-nueva:hover {
            transform: scale(1.05);
            background-color: #333;
        }

        .token-info {
            text-align: center;
            font-size: 10px;
            color: #ccc;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="header-full">
        <a href="?logout=true" class="btn-logout">Cerrar Sesión</a>
        <h1>INFORMATICADESDECERO</h1>
        <div class="sub">PANEL DE ADMINISTRACIÓN PRIVADO</div>
    </div>

    <div class="main-content">
        
        <div class="section-box">
            <h2>Busqueda de noticias</h2>
            <div class="search-container">
                <input type="text" placeholder="¿Qué noticia estás buscando?">
            </div>
        </div>

        <div class="section-box">
            <h2>Recientes:</h2>
            <div class="noticia-card">
                <strong>No hay actividad reciente.</strong>
                <p style="color: #888;">Las noticias que solicites aparecerán aquí una vez sean procesadas.</p>
            </div>
        </div>

    </div>

    <div class="footer-actions">
        <a href="/crea_noticia.xlx?auth_token=<?php echo $tokenUrl; ?>" class="btn-crear-nueva">
            + SOLICITAR CREAR NOTICIA
        </a>
        <div class="token-info">Sesión Activa: <?php echo substr($tokenUrl, 0, 40); ?>...</div>
    </div>

</body>
</html>
