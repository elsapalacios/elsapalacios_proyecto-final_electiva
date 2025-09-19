<?php
session_start();
require 'conexion.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $email_or_phone = $_POST['email_or_phone'];
  $password_input = $_POST['password'];

  // Validar que ambos campos no estén vacíos
  if (!empty($email_or_phone) && !empty($password_input)) {
    $stmt = $pdo->prepare("SELECT Id_usuario, correo, contrasena FROM usuarios WHERE correo = :identificador OR telefono = :identificador");
    $stmt->execute(['identificador' => $email_or_phone]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
      // Verificar la contraseña hasheada
      if (password_verify($password_input, $user['contrasena'])) {
        // Credenciales válidas, establecer sesión
        $_SESSION['user_id'] = $user['Id_usuario'];
        $_SESSION['user_email'] = $user['correo'];

        // Si "Remember me" está marcado, establecera una cookie.
        // La duración de la cookie por el momento solo es un ejemplo (7 días).
        if (isset($_POST['remember_me']) && $_POST['remember_me']) {
          setcookie('user_email', $user['correo'], time() + (86400 * 7), "/"); // 86400 = 1 día
        }

        header("Location: index.html");
        exit;
      } else {
        $error = "Credenciales incorrectas (contraseña).";
      }
    } else {
      $error = "Credenciales incorrectas (usuario no encontrado).";
    }
  } else {
    $error = "Por favor, complete todos los campos.";
  }
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EducaVial - Iniciar Sesión</title>
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
      /* Dark gray for Google button */
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
      /* Hide overflow to prevent scrollbar from background image */
    }

    .login-container {
      display: flex;
      width: 100vw;
      /* Full viewport width */
      height: 100vh;
      /* Full viewport height */
    }

    .image-section {
      flex: 1;
      background-image: url('assets/img/Fondo_2.jpg');
      /* Ensure this path is correct */
      background-size: cover;
      background-position: center;
      position: relative;
      /* Crucial for positioning the photo-credit */
      display: flex;
      /* Keep flex for potential future content */
      align-items: flex-end;
      /* Align items to the bottom */
      justify-content: flex-start;
      /* Align items to the start */
      padding: 200px;
      /* Adjust padding as needed */
      color: var(--white);
      font-size: 0.9em;
    }

    .image-section .photo-credit {
      position: absolute;
      /* Position relative to .image-section */
      bottom: 20px;
      /* Distance from the bottom */
      right: 20px;
      /* Distance from the right */
      color: var(--white);
      font-size: 0.85em;
      text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);
      /* Optional: add shadow for readability */
    }


    .login-form-section {
      flex: 1;
      display: flex;
      flex-direction: column;
      /* Use column to stack login-card and footer-credits */
      justify-content: center;
      align-items: center;
      background-color: var(--white);
      padding: 40px;
      /* Increased padding */
      box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
      position: relative;
      /* For absolute positioning of footer-credits if needed */
    }

    .login-card {
      width: 100%;
      max-width: 400px;
      /* Adjust max-width as needed */
      padding: 30px;
      border-radius: 15px;
      /* No explicit shadow here, as it's part of the login-form-section shadow */
      display: flex;
      /* Make login-card a flex container */
      flex-direction: column;
      /* Stack its children vertically */
      align-items: center;
      /* Center content horizontally */
      text-align: center;
      /* Center text within login-card, affects children like signup-link */
    }

    .header-logo {
      display: flex;
      align-items: center;
      justify-content: center;
      /* Center the logo and text */
      margin-bottom: 30px;
      width: 100%;
      /* Ensure it takes full width to center effectively */
    }

    /*.header-logo .logo-circle {
      width: 40px;
      height: 40px;
      background-color: #f7a400;
      /* Example color for the circle *COMENTARIO
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      margin-right: 10px;
      color: white;
      font-weight: bold;
      font-size: 1.2em;
    }*/

    .header-logo .logo-circle {
      width: 40px;
      height: 40px;
      background-color: transparent;
      /* O el color que desees para el fondo del círculo */
      background-image: url('assets/img/Logo_2.png');
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
      /* Ensure heading takes full width for centering */
    }

    .form-group {
      margin-bottom: 20px;
      width: 100%;
      /* Ensure form groups take full width */
      text-align: left;
      /* Align labels to the left */
    }

    /* Common style for input fields with icons */
    .input-with-icon {
      display: flex;
      align-items: center;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      padding: 0 15px;
      /* Adjust horizontal padding */
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .input-with-icon:focus-within {
      border-color: var(--primary-blue);
      box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
    }

    .input-with-icon .form-control {
      border: none;
      flex-grow: 1;
      /* Allow input to take remaining space */
      padding: 12px 0;
      /* Adjust vertical padding */
      font-size: 1rem;
      box-shadow: none !important;
      /* Override Bootstrap focus shadow */
    }

    .input-with-icon .form-control:focus {
      outline: none;
      /* Remove default outline */
    }

    .input-prefix-icon {
      color: var(--secondary-gray);
      margin-right: 10px;
      /* Space between icon and text */
      font-size: 1.1rem;
      /* Adjust icon size */
    }

    /* Specific styles for password input group (with eye icon on the right) */
    .password-input-group {
      position: relative;
      /* Needed for positioning the eye icon */
      display: flex;
      align-items: center;
      border: 1px solid var(--border-color);
      border-radius: 8px;
      padding: 0 15px;
      /* Adjust horizontal padding */
      transition: border-color 0.3s ease, box-shadow 0.3s ease;
    }

    .password-input-group:focus-within {
      border-color: var(--primary-blue);
      box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.25);
    }

    .password-input-group .form-control {
      border: none;
      flex-grow: 1;
      padding: 12px 0;
      /* Adjust vertical padding */
      font-size: 1rem;
      padding-right: 40px;
      /* Make space for the eye icon */
      box-shadow: none !important;
    }

    .password-input-group .form-control:focus {
      outline: none;
    }

    .password-toggle-icon {
      position: absolute;
      right: 15px;
      /* Position from the right edge of the input group */
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      color: var(--secondary-gray);
      font-size: 1.1rem;
      /* Adjust icon size */
      z-index: 2;
      /* Ensure it's above the input */
    }


    .form-options {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
      width: 100%;
      /* Ensure form options take full width */
    }

    .form-check-input {
      margin-right: 8px;
    }

    .form-check-label {
      color: var(--secondary-gray);
      font-size: 0.95em;
    }

    .forgot-password {
      color: var(--primary-blue);
      text-decoration: none;
      font-size: 0.95em;
      font-weight: 500;
    }

    .forgot-password:hover {
      text-decoration: underline;
    }

    .btn-sign-in {
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
    }

    .btn-sign-in:hover {
      background-color: var(--button-blue-hover);
      box-shadow: 0 8px 20px rgba(0, 123, 255, 0.3);
    }

    .or-divider {
      text-align: center;
      margin: 25px 0;
      position: relative;
      color: var(--secondary-gray);
      width: 100%;
      /* Ensure divider takes full width */
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

    .btn-google-sign-in {
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

    .btn-google-sign-in:hover {
      background-color: var(--google-button-hover);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    }

    .btn-google-sign-in .fab {
      margin-right: 10px;
      font-size: 1.2em;
    }

    .signup-link {
      text-align: center;
      margin-top: 30px;
      margin-bottom: 15px;
      /* Added margin to separate from new centered social handle */
      font-size: 0.95em;
      color: var(--dark-text);
      width: 100%;
      /* Ensure it takes full width for centering */
    }

    .signup-link a {
      color: var(--primary-blue);
      text-decoration: none;
      font-weight: 500;
    }

    .signup-link a:hover {
      text-decoration: underline;
    }

    .centered-social-handle {
      text-align: center;
      font-size: 0.85em;
      color: var(--secondary-gray);
      margin-top: 10px;
      /* Space from signup-link */
      width: 100%;
      /* Ensure it takes full width for centering */
    }

    .centered-social-handle .profile-pic {
      width: 24px;
      height: 24px;
      border-radius: 50%;
      background-color: #ccc;
      /* Placeholder for actual profile picture */
      margin-right: 5px;
      vertical-align: middle;
      /* Align with text */
    }

    .footer-credits {
      position: absolute;
      bottom: 20px;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      justify-content: center;
      /* Center the copyright text */
      width: 100%;
      max-width: 400px;
      padding: 0 30px;
      font-size: 0.85em;
      color: var(--secondary-gray);
    }

    /* No longer need .footer-credits .social-handle, it's moved */


    /* Responsive adjustments */
    @media (max-width: 768px) {
      .login-container {
        flex-direction: column;
        height: auto;
      }

      .image-section {
        height: 30vh;
        /* Shorter height on smaller screens */
        width: 100%;
        padding: 15px;
      }

      .login-form-section {
        width: 100%;
        padding: 30px 20px;
        box-shadow: none;
        /* Remove shadow on mobile */
      }

      .login-card {
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
        /* Adjust spacing for mobile */
      }
    }
  </style>
</head>

<body>
  <div class="login-container">
    <div class="image-section">
      <span class="photo-credit"><a href="https://losafrolatinos.com/2017/08/11/hablemos-de-quibdo-lets-talk-about-quibdo/">Foto cortesía de Maruja Uribe</a></span>
    </div>
    <div class="login-form-section">
      <div class="login-card">
        <div class="header-logo">
          <!--<div class="logo-circle">
            <i class="fas fa-hat-wizard"></i>
          </div>-->
          <img src="assets/img/Logo_1.jpg" alt="Logo EducaVial" class="header-logo-img">
          <span class="logo-text">EducaVial</span>
        </div>
        <h1>Iniciar Sesión</h1>

        <?php if (!empty($error)): ?>
          <div class="alert alert-danger" role="alert">
            <?php echo $error; ?>
          </div>
        <?php endif; ?>

        <form action="login.php" method="POST">
          <div class="form-group">
            <label for="email_or_phone" class="form-label">Correo Electrónico</label>
            <div class="input-with-icon">
              <span class="input-prefix-icon">
                <i class="far fa-envelope"></i>
              </span>
              <input type="text" id="email_or_phone" name="email_or_phone" class="form-control" placeholder="Introduce tu correo electrónico" required>
            </div>
          </div>
          <div class="form-group">
            <label for="password" class="form-label">Contraseña</label>
            <div class="password-input-group">
              <span class="input-prefix-icon">
                <i class="fas fa-lock"></i>
              </span>
              <input type="password" id="password" name="password" class="form-control" placeholder="Introduce tu contraseña" required>
              <span class="password-toggle-icon" onclick="togglePasswordVisibility()">
                <i class="far fa-eye" id="password-eye-icon"></i>
              </span>
            </div>
          </div>
          <div class="form-options">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" id="remember_me" name="remember_me">
              <label class="form-check-label" for="remember_me">
                Recordar
              </label>
            </div>
            <a href="olvido_contrasena.html" class="forgot-password">¿Has olvidado tu contraseña?</a>
          </div>
          <button type="submit" class="btn-sign-in">Iniciar Sesión</button>
        </form>

        <div class="signup-link">
          ¿No tienes cuenta? <a href="registro.php">Registrar ahora</a>
        </div>

        <div class="centered-social-handle">
          <img src="assets/img/Logo_3.png" alt="Profile" class="profile-pic" /> <span>@infoeducavial</span>
        </div>
      </div>
      <div class="footer-credits">
        <!-- <span class="copyright">© Login 2025</span> -->
      </div>
    </div>
  </div>

  <script>
    function togglePasswordVisibility() {
      const passwordField = document.getElementById('password');
      const eyeIcon = document.getElementById('password-eye-icon');
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