<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header("Location: panel.php");
    exit;
}

require_once "conexion.php";

function validarPassword(string $password): string
{
    if (strlen($password) < 8) {
        return "La contraseña debe tener al menos 8 caracteres.";
    }
    if (strlen($password) > 72) {
        return "La contraseña no puede superar los 72 caracteres.";
    }
    if (!preg_match('/[A-Z]/', $password)) {
        return "La contraseña debe incluir al menos una letra mayúscula.";
    }
    if (!preg_match('/[a-z]/', $password)) {
        return "La contraseña debe incluir al menos una letra minúscula.";
    }
    if (!preg_match('/[0-9]/', $password)) {
        return "La contraseña debe incluir al menos un número.";
    }
    return "";
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombre = trim($_POST['nombre'] ?? '');
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $passwordConfirm = $_POST['password_confirm'] ?? '';

    if (empty($nombre) || empty($usuario) || empty($password) || empty($passwordConfirm)) {
        $error = "Por favor, completa todos los campos.";
    } elseif ($password !== $passwordConfirm) {
        $error = "Las contraseñas no coinciden.";
    } else {
        $error = validarPassword($password);
    }

    if (empty($error)) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE usuario = :usuario LIMIT 1");
        $stmt->execute(['usuario' => $usuario]);

        if ($stmt->fetch()) {
            $error = "El nombre de usuario ya está registrado.";
        } else {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $insertStmt = $pdo->prepare("INSERT INTO usuarios (nombre, usuario, password) VALUES (:nombre, :usuario, :password)");
            if ($insertStmt->execute(['nombre' => $nombre, 'usuario' => $usuario, 'password' => $passwordHash])) {
                $success = "¡Usuario registrado correctamente! Ya puedes iniciar sesión.";
            } else {
                $error = "Ocurrió un error al registrar el usuario.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background-color: #ffffff;
            width: 100%;
            max-width: 420px;
            padding: 35px 30px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        .card h2 { margin-bottom: 20px; text-align: center; color: #333333; font-size: 26px; }
        .alert { padding: 12px 15px; border-radius: 6px; margin-bottom: 18px; font-size: 14px; }
        .alert-error { background-color: #ffe5e5; color: #d63031; border: 1px solid #ff7675; }
        .alert-success { background-color: #e8f8f0; color: #00b894; border: 1px solid #55efc4; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; margin-bottom: 6px; color: #4b4b4b; font-size: 14px; font-weight: 600; }
        .form-group input {
            width: 100%; padding: 11px 14px; border: 1px solid #ced6e0; border-radius: 6px;
            font-size: 15px; outline: none; transition: border-color 0.2s, box-shadow 0.2s;
        }
        .form-group input:focus { border-color: #667eea; box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2); }
        .password-wrap { position: relative; }
        .password-wrap input { padding-right: 44px; }
        .toggle-password {
            position: absolute; top: 50%; right: 8px; transform: translateY(-50%);
            width: 34px; height: 34px; border: none; background: transparent; color: #6b7280;
            cursor: pointer; border-radius: 6px; display: flex; align-items: center; justify-content: center; padding: 0;
        }
        .toggle-password:hover { color: #667eea; background: rgba(102, 126, 234, 0.08); }
        .toggle-password svg { width: 20px; height: 20px; fill: none; stroke: currentColor; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .toggle-password .icon-hide { display: none; }
        .toggle-password.is-visible .icon-show { display: none; }
        .toggle-password.is-visible .icon-hide { display: block; }
        .password-rules { margin: -6px 0 16px; padding-left: 18px; color: #666666; font-size: 12px; line-height: 1.5; }
        .btn-submit {
            width: 100%; padding: 12px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff; border: none; border-radius: 6px; font-size: 16px; font-weight: 600;
            cursor: pointer; transition: opacity 0.2s; margin-top: 10px;
        }
        .btn-submit:hover { opacity: 0.92; }
        .footer-link { text-align: center; margin-top: 20px; font-size: 14px; color: #555555; }
        .footer-link a { color: #667eea; text-decoration: none; font-weight: 600; }
        .footer-link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="card">
        <h2>Crear Cuenta</h2>
        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        <form action="registro.php" method="POST">
            <div class="form-group">
                <label for="nombre">Nombre Completo</label>
                <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" placeholder="Ej: Pedro Morales" required>
            </div>
            <div class="form-group">
                <label for="usuario">Usuario</label>
                <input type="text" id="usuario" name="usuario" value="<?= htmlspecialchars($_POST['usuario'] ?? '') ?>" placeholder="Ej: pedro123" required>
            </div>
            <div class="form-group">
                <label for="password">Contraseña</label>
                <div class="password-wrap">
                    <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required minlength="8" maxlength="72">
                    <button type="button" class="toggle-password" data-target="password" aria-label="Mostrar contraseña" aria-pressed="false">
                        <svg class="icon-show" viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="icon-hide" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.77 21.77 0 0 1 5.06-5.94"/><path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 7 11 7a21.8 21.8 0 0 1-3.17 4.19"/><path d="M1 1l22 22"/><path d="M9.53 9.53a3 3 0 0 0 4.24 4.24"/></svg>
                    </button>
                </div>
            </div>
            <ul class="password-rules">
                <li>Mínimo 8 caracteres</li>
                <li>Al menos una mayúscula y una minúscula</li>
                <li>Al menos un número</li>
            </ul>
            <div class="form-group">
                <label for="password_confirm">Confirmar contraseña</label>
                <div class="password-wrap">
                    <input type="password" id="password_confirm" name="password_confirm" placeholder="Repite la contraseña" required minlength="8" maxlength="72">
                    <button type="button" class="toggle-password" data-target="password_confirm" aria-label="Mostrar contraseña" aria-pressed="false">
                        <svg class="icon-show" viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"/><circle cx="12" cy="12" r="3"/></svg>
                        <svg class="icon-hide" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.94 17.94A10.94 10.94 0 0 1 12 19c-7 0-11-7-11-7a21.77 21.77 0 0 1 5.06-5.94"/><path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 7 11 7a21.8 21.8 0 0 1-3.17 4.19"/><path d="M1 1l22 22"/><path d="M9.53 9.53a3 3 0 0 0 4.24 4.24"/></svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-submit">Registrarse</button>
        </form>
        <p class="footer-link">
            ¿Ya tienes una cuenta? <a href="index.php">Inicia sesión</a>
        </p>
    </div>
    <script>
        document.querySelectorAll('.toggle-password').forEach(function (button) {
            button.addEventListener('click', function () {
                var input = document.getElementById(button.getAttribute('data-target'));
                var visible = input.type === 'password';
                input.type = visible ? 'text' : 'password';
                button.classList.toggle('is-visible', visible);
                button.setAttribute('aria-pressed', visible ? 'true' : 'false');
                button.setAttribute('aria-label', visible ? 'Ocultar contraseña' : 'Mostrar contraseña');
            });
        });
    </script>
</body>
</html>
