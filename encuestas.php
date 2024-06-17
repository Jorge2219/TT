
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!----======== CSS ======== -->
    <link rel="stylesheet" href="css/style3.css">
    <link rel="stylesheet" href="css/estilos123.css"> 
    <!----===== Iconscout CSS ===== -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <style>
        @font-face {
            font-family: 'Montserrat';
            src: url('fonts/Montserrat/static/Montserrat-Regular.ttf') format('truetype');
            /* Agrega las otras variaciones de la fuente si las tienes, como Bold, Italic, etc. */
        }

        body {
            font-family: 'Montserrat', sans-serif;
        }
    </style>
    <title>Nueva encuesta</title> 
</head>
<body>
    <nav>
        <div class="logo-name">
            <div class="logo-image">
                <img src="img/candado.png" style="width: 50px; height: auto;" alt="">
            </div>
            <span class="logo_name">SisGenEnc</span>
            <div class="logo-image">
                <img src="img/upiiz.png" alt="">
            </div>
        </div>
        <div class="menu-items">
            <ul class="nav-links">
                <li><a href="index.php"><i class="uil uil-estate"></i><span class="link-name">Servicios</span></a></li>
                <li><a href="usua.php"><i class="uil uil-user"></i><span class="link-name">Usuarios</span></a></li>
                <li><a href="encuestas.php"><i class="uil uil-chart"></i><span class="link-name">Encuestas</span></a></li>
                <li><a href="reportes.php"><i class="uil uil-comments"></i><span class="link-name">Reportes</span></a></li>
            </ul>
            <ul class="logout-mode">
                <li><a href="cerrar_sesion.php"><i class="uil uil-signout"></i><span class="link-name">Salir</span></a></li>
            </ul>
            <li class="mode">
                <a href="#"><i class="uil uil-moon"></i><span class="link-name">Modo oscuro</span></a>
                <div class="mode-toggle">
                    <span class="switch"></span>
                </div>
            </li>
        </div>
    </nav>

    <section class="dashboard">
        <div class="top">
            <i class="uil uil-bars sidebar-toggle"></i>

            <div class="search-box">
                <i class="uil uil-search"></i>
                <input type="text" placeholder="Buscar">
            </div>
            
            <img src="img/IPN.png" alt="">
        </div>

        <div class="dash-content">
            <div class="overview">
                <div class="title">
                    <i class="uil uil-estate"></i>
                    <span class="text">Mis encuestas</span>
                </div>
                <div class="boxes" id="serviciosContainer">
                    
                </div>
                </div>
            </div>
            <button id="crearBtn" onclick="CrearNuevaEncuesta()">CREAR NUEVA</button>
    </section>
    
    <script src="js/script.js"></script>
    <script src="js/CrearNuevaEncuesta.js"></script>

    
    
</body>
</html>