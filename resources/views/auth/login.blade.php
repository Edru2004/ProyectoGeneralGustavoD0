<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso - Sistema Bachillerato</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/Login.css') }}">
</head>
<body>
    <div class="carousel-background">
        <img src="{{ asset('imagenes/fondo1.jpg') }}" class="carousel-img active">
        <img src="{{ asset('imagenes/fondo2.jpg') }}" class="carousel-img">
    </div>
    <div class="overlay"></div>

    <div class="main-box {{ $errors->any() ? '' : 'active' }}" id="mainBox">
        <div class="panel-wrapper">
            
            <div class="panel login-panel">
                <div class="login-content">
                    <img src="{{ asset('imagenes/PNGLOGO.png') }}" class="logo-school">
                    <h2>Acceso Estudiantes</h2>
                    
                    @if($errors->any())
                        <div class="error-banner">
                            <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                        </div>
                    @endif

                    <form action="{{ route('login.post') }}" method="POST" class="login-form">
                        @csrf
                        <div class="input-group">
                            <label>Correo Institucional</label>
                            <div class="input-wrapper">
                                <i class="fas fa-envelope"></i>
                                <input type="email" name="usuario" value="{{ old('usuario') }}" placeholder="ejemplo@correo.com" required autofocus>
                            </div>
                        </div>
                        <div class="input-group">
                            <label>Contraseña</label>
                            <div class="input-wrapper">
                                <i class="fas fa-lock"></i>
                                <input type="password" name="contrasena" placeholder="••••••••" required>
                            </div>
                        </div>
                        <button type="submit" class="btn-submit">Entrar al Sistema</button>
                    </form>
                    <button class="btn-back" id="showWelcomeBtn">Volver</button>
                </div>
            </div>

            <div class="panel welcome-panel">
                <div class="icon-box"><i class="fas fa-graduation-cap"></i></div>
                <h3>¡Bienvenido de nuevo!</h3>
                <p>Inicia sesión para revisar tu avance académico.</p>
                <button class="btn-slide" id="showLoginBtn">Iniciar Sesión</button>
            </div>
        </div>
    </div>

    <script>
        // Lógica de paneles
        const mainBox = document.getElementById('mainBox');
        const showLoginBtn = document.getElementById('showLoginBtn');
        const showWelcomeBtn = document.getElementById('showWelcomeBtn');

        showLoginBtn.addEventListener('click', () => {
            mainBox.classList.remove('active');
        });

        showWelcomeBtn.addEventListener('click', () => {
            mainBox.classList.add('active');
        });

        // Lógica de carrusel (Cambia cada 5 segundos)
        const images = document.querySelectorAll('.carousel-img');
        let currentImg = 0;

        setInterval(() => {
            images[currentImg].classList.remove('active');
            currentImg = (currentImg + 1) % images.length;
            images[currentImg].classList.add('active');
        }, 5000);
    </script>
</body>
</html>