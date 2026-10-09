<?php
session_start();

// Control de sesión: Si no existe la sesión, redirigir al login
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$nombre = $_SESSION['usuario_nombre'] ?? 'Usuario';
$usuario = $_SESSION['usuario_username'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        body {
            background-color: #f4f6f9;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 16px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        header h1 {
            font-size: 20px;
            font-weight: 600;
        }
        .btn-logout {
            background-color: rgba(255, 255, 255, 0.2);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.5);
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: background-color 0.2s ease;
        }
        .btn-logout:hover {
            background-color: rgba(255, 255, 255, 0.35);
        }
        main {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .card {
            background-color: #ffffff;
            width: 100%;
            max-width: 500px;
            padding: 35px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            text-align: center;
        }
        .avatar {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
            margin: 0 auto 20px auto;
        }
        .card h2 {
            color: #2d3436;
            font-size: 24px;
            margin-bottom: 10px;
        }
        .card p {
            color: #636e72;
            font-size: 15px;
            margin-bottom: 25px;
            line-height: 1.5;
        }
        .user-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 25px;
            text-align: left;
            font-size: 14px;
        }
        .user-details div {
            margin-bottom: 8px;
            color: #555;
        }
        .user-details div:last-child {
            margin-bottom: 0;
        }
        .user-details strong {
            color: #333;
        }
        .btn-action {
            display: inline-block;
            background-color: #e74c3c;
            color: #ffffff;
            text-decoration: none;
            padding: 10px 22px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 14px;
            transition: background-color 0.2s ease;
        }
        .btn-action:hover {
            background-color: #c0392b;
        }
    </style>
</head>
<body>
    <header>
        <h1>Sistema de Autenticación</h1>
        <a href="logout.php" class="btn-logout">Cerrar Sesión</a>
    </header>

    <main>
        <div class="card">
            <div class="avatar">
                <?= strtoupper(substr($nombre, 0, 1)) ?>
            </div>
            <h2>¡Bienvenido(a), <?= htmlspecialchars($nombre) ?>!</h2>
            <p>Has iniciado sesión correctamente en el sistema protegido.</p>

            <div class="user-details">
                <div><strong>Nombre:</strong> <?= htmlspecialchars($nombre) ?></div>
                <div><strong>Usuario:</strong> <?= htmlspecialchars($usuario) ?></div>
                <div><strong>ID de sesión:</strong> <?= htmlspecialchars((string)$_SESSION['usuario_id']) ?></div>
            </div>

            <a href="logout.php" class="btn-action">Cerrar Sesión</a>
        </div>
    </main>
</body>
</html>
