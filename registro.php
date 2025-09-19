<?php
session_start();
require 'conexion.php';

$error = '';
$success = '';

$nombre_completo = '';
$correo = '';
$telefono = '';
$tipo_usuario = '';
$fecha_nacimiento = '';
$direccion = '';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre_completo = $_POST['nombre_completo'] ?? '';
    $correo = $_POST['correo'] ?? '';
    $telefono = $_POST['telefono'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $tipo_usuario = $_POST['tipo_usuario'] ?? '';
    $fecha_nacimiento = $_POST['fecha_nacimiento'] ?? '';
    $direccion = $_POST['direccion'] ?? '';

    // 1. Validaciones básicas
    if (empty($nombre_completo) || empty($correo) || empty($telefono) || empty($password) || empty($confirm_password) || empty($tipo_usuario)) {
        $error = "Todos los campos obligatorios deben ser completados.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El formato del correo electrónico no es válido.";
    } elseif ($password !== $confirm_password) {
        $error = "Las contraseñas no coinciden.";
    } elseif (strlen($password) < 6) {
        $error = "La contraseña debe tener al menos 6 caracteres.";
    } else {
        // 2. Verificar si el correo o el teléfono ya existen
        $stmt_check = $pdo->prepare("SELECT Id_usuario FROM usuarios WHERE correo = :correo OR telefono = :telefono LIMIT 1");
        $stmt_check->execute(['correo' => $correo, 'telefono' => $telefono]);
        if ($stmt_check->fetch()) {
            $error = "El correo electrónico o el número de teléfono ya están registrados.";
        } else {
            // 3. Hashear la contraseña de forma segura
            $password_hashed = password_hash($password, PASSWORD_DEFAULT);

            // 4. Insertar el nuevo usuario en la base de datos
            try {
                $stmt_insert = $pdo->prepare("INSERT INTO usuarios (nombre_completo, correo, telefono, contrasena, tipo_usuario, fecha_nacimiento, direccion) VALUES (:nombre_completo, :correo, :telefono, :contrasena, :tipo_usuario, :fecha_nacimiento, :direccion)");

                // Asignar null a campos opcionales si están vacíos
                $fecha_nacimiento_db = !empty($fecha_nacimiento) ? $fecha_nacimiento : null;
                $direccion_db = !empty($direccion) ? $direccion : null;

                $stmt_insert->execute([
                    'nombre_completo' => $nombre_completo,
                    'correo' => $correo,
                    'telefono' => $telefono,
                    'contrasena' => $password_hashed,
                    'tipo_usuario' => $tipo_usuario,
                    'fecha_nacimiento' => $fecha_nacimiento_db,
                    'direccion' => $direccion_db
                ]);

                $success = "¡Registro exitoso! Ahora puedes iniciar sesión.";
            } catch (PDOException $e) {
                $error = "Error al registrar el usuario: " . $e->getMessage();
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
    <title>EducaVial - Registro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-blue: #007bff;
            --secondary-gray: #6c757d;
            --light-gray: #f8f9fa;
            --dark-text: #343a40;
            --border-color: #ced4da;
            --button-blue: #007bff;
            --button-blue-hover: #0056b3;
            --google-button-bg: #343a40;
            --google-button-hover: #495057;
            --white: #ffffff;
            --font-inter: 'Inter', sans-serif;
        }

        body {
            font-family: var(--font-inter);
            line-height: 1.6;
            color: var(--dark-text);
            background-color: var(--light-gray);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        .register-container {
            display: flex;
            width: 100vw;
            height: 100vh;
        }

        .image-section {
            flex: 1;
            /* La imagen de fondo debe ser relevante para educación vial, por ejemplo:
               una intersección con señales, niños cruzando la calle de forma segura, etc. */
            background-image: url('assets/img/Fondo_2.jpg');
            background-size: cover;
            background-position: center;
            position: relative;
            display: flex;
            align-items: flex-end;
            justify-content: flex-start;
            padding: 110px;
            color: var(--white);
            font-size: 0.9em;
        }

        .image-section .photo-credit {
            position: absolute;
            bottom: 20px;
            right: 20px;
            color: var(--white);
            font-size: 0.85em;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
        }

        .register-form-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            background-color: var(--white);
            padding: 40px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow-y: auto;
            /* Permite scroll si el formulario es muy largo */
        }

        .register-card {
            width: 100%;
            max-width: 600px;
            /* Ajustado para acomodar dos columnas */
            padding: 30px;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .header-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 30px;
            width: 100%;
        }

        /*.header-logo .logo-circle {
            width: 40px;
            height: 40px;
            background-color: transparent;
            /* Fondo transparente para la imagen del logo* 
            background-image: url('assets/img/logo_educavial.png');
            /* **RUTA A TU LOGO DEL ESCUDO AZUL** 
            background-size: contain;
            background-repeat: no-repeat;
            background-position: center;
            border-radius: 50%;
            margin-right: 10px;
        }*/

        /* Si usas la imagen del logo directamente sin un círculo de fondo, puedes eliminar lo anterior y usar: */
        /*
        .header-logo img.logo-img {
            height: 40px;
            width: auto;
            margin-right: 10px;
        }
        */

        .header-logo .logo-circle {
            width: 40px;
            height: 40px;
            background-color: transparent;
            /* O el color que desees para el fondo del círculo */
            background-image: url('assets/img/Logo_1.jpg');
            /* Ruta a tu nuevo logo */
            background-size: contain;
            /* Ajusta la imagen para que quepa dentro del círculo */
            background-repeat: no-repeat;
            /* Evita que la imagen se repita */
            background-position: center;
            /* Centra la imagen dentro del círculo */
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 10px;
            /* Puedes eliminar las propiedades 'color', 'font-weight', 'font-size' si el logo es una imagen */
        }

        .header-logo .logo-circle {
            /* Elimina o comenta todos los estilos de .logo-circle ya que ya no existe */
            display: none;
            /* Para asegurarte de que no haya efectos residuales si no lo borraste del HTML */
        }

        .header-logo-img {
            height: 40px;
            /* Ajusta la altura de tu logo */
            width: auto;
            /* Mantiene la proporción */
            margin-right: 10px;
            /* Espacio entre el logo y el texto */
        }

        .header-logo .logo-text {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-text);
        }

        h1 {
            font-size: 2.2rem;
            font-weight: 700;
            margin-bottom: 25px;
            color: var(--dark-text);
            width: 100%;
        }

        .form-group {
            margin-bottom: 20px;
            width: 100%;
            text-align: left;
        }

        .input-with-icon,
        .password-input-group,
        .select-with-icon {
            display: flex;
            align-items: center;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 0 15px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            /* Necesario para posicionar el ojo de la contraseña */
        }

        .input-with-icon:focus-within,
        .password-input-group:focus-within,
        .select-with-icon:focus-within {
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
        }

        .input-with-icon .form-control,
        .password-input-group .form-control,
        .select-with-icon .form-select {
            border: none;
            flex-grow: 1;
            padding: 12px 0;
            font-size: 1rem;
            box-shadow: none !important;
            background-color: transparent;
            /* Asegura que el fondo del input/select sea transparente */
        }

        .input-with-icon .form-control:focus,
        .password-input-group .form-control:focus,
        .select-with-icon .form-select:focus {
            outline: none;
        }

        .input-prefix-icon {
            color: var(--secondary-gray);
            margin-right: 10px;
            font-size: 1.1rem;
        }

        .password-toggle-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--secondary-gray);
            font-size: 1.1rem;
            z-index: 2;
        }

        .btn-register {
            background-color: var(--button-blue);
            color: var(--white);
            border: none;
            border-radius: 8px;
            padding: 12px 0;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.2);
            margin-top: 20px;
            /* Espacio superior después de las columnas */
        }

        .btn-register:hover {
            background-color: var(--button-blue-hover);
            box-shadow: 0 8px 20px rgba(0, 123, 255, 0.3);
        }

        .or-divider {
            text-align: center;
            margin: 25px 0;
            position: relative;
            color: var(--secondary-gray);
            width: 100%;
        }

        .or-divider::before,
        .or-divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background-color: var(--border-color);
        }

        .or-divider::before {
            left: 0;
        }

        .or-divider::after {
            right: 0;
        }

        .btn-google-sign-up {
            /* Renombrado para registro */
            background-color: var(--google-button-bg);
            color: var(--white);
            border: none;
            border-radius: 8px;
            padding: 12px 0;
            font-size: 1.1rem;
            font-weight: 600;
            width: 100%;
            cursor: pointer;
            transition: background-color 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .btn-google-sign-up:hover {
            /* Renombrado para registro */
            background-color: var(--google-button-hover);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .btn-google-sign-up .fab {
            margin-right: 10px;
            font-size: 1.2em;
        }

        .login-link {
            /* Renombrado para el enlace de login */
            text-align: center;
            margin-top: 30px;
            margin-bottom: 15px;
            font-size: 0.95em;
            color: var(--dark-text);
            width: 100%;
        }

        .login-link a {
            color: var(--primary-blue);
            text-decoration: none;
            font-weight: 500;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .centered-social-handle {
            text-align: center;
            font-size: 0.85em;
            color: var(--secondary-gray);
            margin-top: 10px;
            width: 100%;
        }

        .centered-social-handle .profile-pic {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: #ccc;
            margin-right: 5px;
            vertical-align: middle;
        }

        .footer-credits {
            position: absolute;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            justify-content: center;
            width: 100%;
            max-width: 600px;
            /* Ajusta este valor para que coincida con max-width de .register-card */
            padding: 0 30px;
            font-size: 0.85em;
            color: var(--secondary-gray);
        }

        /* Ajustes para las columnas de Bootstrap */
        .row {
            --bs-gutter-x: 1.5rem;
            /* Espacio horizontal entre columnas de Bootstrap */
            --bs-gutter-y: 1rem;
            /* Espacio vertical entre filas de Bootstrap */
            margin-left: calc(var(--bs-gutter-x) * -.5);
            margin-right: calc(var(--bs-gutter-x) * -.5);
            /* Eliminar margin-top: calc(var(--bs-gutter-y) * -1); en el .row si los form-group tienen su propio margin-bottom */
            margin-top: 0;
            /* Asegurar que no haya un margen superior extra en la fila */
        }

        .row>* {
            padding-left: calc(var(--bs-gutter-x) * .5);
            padding-right: calc(var(--bs-gutter-x) * .5);
            margin-top: 0;
            /* Los form-group ya tienen su margen, evitar duplicados */
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .register-container {
                flex-direction: column;
                height: auto;
            }

            .image-section {
                height: 30vh;
                width: 100%;
                padding: 15px;
            }

            .register-form-section {
                width: 100%;
                padding: 30px 20px;
                box-shadow: none;
            }

            .register-card {
                padding: 20px;
            }

            .image-section .photo-credit {
                bottom: 15px;
                right: 15px;
            }

            .footer-credits {
                position: static;
                transform: none;
                margin-top: 30px;
                padding: 0;
                flex-direction: column;
                text-align: center;
            }

            .centered-social-handle {
                margin-top: 20px;
            }

            /* En pantallas pequeñas, las columnas se apilan automáticamente debido a col-md-6 */
            .row>* {
                padding-left: var(--bs-gutter-x);
                /* Restaurar padding completo para campos individuales en móvil */
                padding-right: var(--bs-gutter-x);
            }

            .form-group {
                margin-bottom: 15px;
                /* Ajustar margen para móviles */
            }
        }
    </style>
</head>

<body>
    <div class="register-container">
        <div class="image-section">
            <span class="photo-credit"><a href="https://losafrolatinos.com/2017/08/11/hablemos-de-quibdo-lets-talk-about-quibdo/">Foto cortesía de Maruja Uribe</a></span>
        </div>
        <div class="register-form-section">
            <div class="register-card">
                <div class="header-logo">
                    <!--<div class="logo-circle">
                    </div>-->
                    <img src="assets/img/Logo_1.jpg" alt="Logo EducaVial" class="header-logo-img">
                    <span class="logo-text">EducaVial</span>
                </div>
                <h1>Crear una Cuenta</h1>

                <form action="registro.php" method="POST">
                    <?php if (!empty($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $success; ?>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="nombre_completo" class="form-label">Nombre Completo</label>
                                <div class="input-with-icon">
                                    <span class="input-prefix-icon">
                                        <i class="fas fa-user"></i>
                                    </span>
                                    <input type="text" id="nombre_completo" name="nombre_completo" class="form-control" placeholder="Introduce tu nombre completo" value="<?php echo htmlspecialchars($nombre_completo); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="correo" class="form-label">Correo Electrónico</label>
                                <div class="input-with-icon">
                                    <span class="input-prefix-icon">
                                        <i class="far fa-envelope"></i>
                                    </span>
                                    <input type="email" id="correo" name="correo" class="form-control" placeholder="Introduce tu correo electrónico" value="<?php echo htmlspecialchars($correo); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <div class="input-with-icon">
                                    <span class="input-prefix-icon">
                                        <i class="fas fa-phone"></i>
                                    </span>
                                    <input type="tel" id="telefono" name="telefono" class="form-control" placeholder="Introduce tu número de teléfono" value="<?php echo htmlspecialchars($telefono); ?>" required>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="password" class="form-label">Contraseña</label>
                                <div class="password-input-group">
                                    <span class="input-prefix-icon">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" id="password" name="password" class="form-control" placeholder="Crea tu contraseña" required>
                                    <span class="password-toggle-icon" onclick="togglePasswordVisibility('password')">
                                        <i class="far fa-eye" id="password-eye-icon"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="confirm_password" class="form-label">Confirmar Contraseña</label>
                                <div class="password-input-group">
                                    <span class="input-prefix-icon">
                                        <i class="fas fa-lock"></i>
                                    </span>
                                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirma tu contraseña" required>
                                    <span class="password-toggle-icon" onclick="togglePasswordVisibility('confirm_password')">
                                        <i class="far fa-eye" id="confirm-password-eye-icon"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="tipo_usuario" class="form-label">Tipo de Usuario</label>
                                <div class="select-with-icon">
                                    <span class="input-prefix-icon">
                                        <i class="fas fa-users"></i>
                                    </span>
                                    <select id="tipo_usuario" name="tipo_usuario" class="form-select" required>
                                        <option value="">Selecciona tu tipo de usuario</option>
                                        <option value="estudiante" <?php echo ($tipo_usuario == 'estudiante') ? 'selected' : ''; ?>> Estudiante</option>
                                        <option value="conductor" <?php echo ($tipo_usuario == 'conductor') ? 'selected' : ''; ?>> Conductor</option>
                                        <option value="peatón" <?php echo ($tipo_usuario == 'peatón') ? 'selected' : ''; ?>> Peatón</option>
                                        <option value="ciclista" <?php echo ($tipo_usuario == 'ciclista') ? 'selected' : ''; ?>> Ciclista</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento</label>
                                <div class="input-with-icon">
                                    <span class="input-prefix-icon">
                                        <i class="fas fa-calendar-alt"></i>
                                    </span>
                                    <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" value="<?php echo htmlspecialchars($fecha_nacimiento); ?>">
                                </div>
                            </div>
                        </div>
                        <!--<div class="col-md-6">
                            <div class="form-group">
                                <label for="institucion" class="form-label">Institución (Opcional)</label>
                                <div class="input-with-icon">
                                    <span class="input-prefix-icon">
                                        <i class="fas fa-building"></i>
                                    </span>
                                    <input type="text" id="institucion" name="institucion" class="form-control" placeholder="Nombre de tu institución" value="<?php echo htmlspecialchars($institucion); ?>">
                                </div>
                            </div>
                        </div>-->
                        <div class="col-6">
                            <div class="form-group">
                                <label for="direccion" class="form-label">Dirección</label>
                                <div class="input-with-icon">
                                    <span class="input-prefix-icon">
                                        <i class="fas fa-map-marker-alt"></i>
                                    </span>
                                    <input type="text" id="direccion" name="direccion" class="form-control" placeholder="Tu dirección" value="<?php echo htmlspecialchars($direccion); ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn-register">Registrar</button>
                </form>

                <!--<div class="or-divider">O</div>

                <button type="button" class="btn-google-sign-up">
                    <i class="fab fa-google"></i>Registrarse con Google
                </button>-->

                <div class="login-link">
                    ¿Ya tienes una cuenta? <a href="login.php">Iniciar Sesión</a>
                </div>

                <div class="centered-social-handle">
                    <img src="assets/img/Logo_3.png" alt="Profile" class="profile-pic" /> <span>@infoeducavial</span>
                </div>
            </div>
            <!--<div class="footer-credits">
                <span class="copyright">© EducaVial 2025</span>
            </div>-->
        </div>
    </div>

    <script>
        function togglePasswordVisibility(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const eyeIcon = document.getElementById(fieldId + '-eye-icon');
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>