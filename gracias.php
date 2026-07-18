<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gracias — Encuesta EmprendeISTG</title>
    <link rel="stylesheet" href="static/css/encuesta.css">
</head>
<body>
    <div class="site-wrapper">
        <header class="site-header">
            <div class="header-content">
                <div class="header-left">
                    <img src="static/img/logo_istg_instituto.png" alt="ISTG" class="header-logo">
                    <div class="header-text">
                        <span class="header-instituto">Instituto Superior Tecnológico Guayaquil</span>
                    </div>
                </div>
                <div class="header-right">
                    <img src="static/img/logo_istg.png" alt="EmprendeISTG" class="header-logo">
                </div>
            </div>
        </header>

        <main class="site-main">
            <div class="encuesta-container">
                <div class="gracias-card">
                    <div class="gracias-icon">✓</div>
                    <h1>¡Gracias por participar!</h1>
                    <p>Su respuesta ha sido registrada exitosamente.</p>
                    <p class="gracias-sub">Sus datos serán utilizados exclusivamente con fines académicos de investigación en el ISTG.</p>
                    <a href="index.php" class="btn btn-primary" style="margin-top:1.5rem;display:inline-block;text-decoration:none;">Nueva respuesta</a>
                </div>
            </div>
        </main>

        <footer class="site-footer">
            <a href="https://pucusoft.pages.dev/" target="_blank" rel="noopener noreferrer" class="footer-link">
                <img src="static/img/pucusoft.png" alt="Pucusoft" class="footer-logo">
                <span>Desarrollado por Pucusoft</span>
            </a>
        </footer>
    </div>
</body>
</html>
