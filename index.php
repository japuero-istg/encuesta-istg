<?php require_once __DIR__ . '/config.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Encuesta EmprendeISTG</title>
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
                <header class="encuesta-header">
                    <h1>Encuesta EmprendeISTG</h1>
                    <p class="subtitle">Aplicación Web Progresiva — Evaluación de Usabilidad y Accesibilidad</p>
                </header>

                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <p class="progress-text" id="progressText">Paso 1 de 7: Consentimiento Informado</p>

                <form id="encuestaForm" novalidate>

                    <!-- PASO 0: Consentimiento -->
                    <section class="step active" data-step="0">
                        <h2>Consentimiento Informado</h2>
                        <div class="consent-text">
                            <p>Estimado(a) participante:</p>
                            <p>Esta encuesta tiene fines exclusivamente académicos, como parte del proyecto de investigación para la titulación en el Instituto Superior Tecnológico Guayaquil (ISTG).</p>
                            <p>Sus respuestas serán tratadas de manera <strong>anónima y confidencial</strong>. No se almacenarán datos personales identificables más allá de un correo electrónico para evitar respuestas duplicadas.</p>
                            <p>La encuesta consta de 7 pasos y tomará aproximadamente 3-5 minutos.</p>
                            <p>Al continuar, usted declara que ha leído y acepta participar voluntariamente en esta investigación.</p>
                        </div>
                        <label class="checkbox-label">
                            <input type="checkbox" id="consentimiento" required>
                            <span>He leído y acepto participar voluntariamente en esta encuesta</span>
                        </label>
                    </section>

                    <!-- PASO 1: Filtrado -->
                    <section class="step" data-step="1">
                        <h2>Preguntas de Filtrado</h2>
                        <p class="step-hint">Estas preguntas nos ayudan a identificar su perfil de uso.</p>

                        <div class="form-group">
                            <label>¿Tiene usted en su dispositivo la App EmprendeISTG instalada? *</label>
                            <div class="radio-group">
                                <label class="radio-card">
                                    <input type="radio" name="tiene_app" value="si" required>
                                    <span class="radio-label">Sí</span>
                                </label>
                                <label class="radio-card">
                                    <input type="radio" name="tiene_app" value="no">
                                    <span class="radio-label">No</span>
                                </label>
                            </div>
                            <span class="error-msg" id="tiene_app-error"></span>
                        </div>

                        <div class="form-group">
                            <label>¿Es usted emprendedor(a) activo(a) registrado(a) en EmprendeISTG? *</label>
                            <div class="radio-group">
                                <label class="radio-card">
                                    <input type="radio" name="es_emprendedor" value="si" required>
                                    <span class="radio-label">Sí</span>
                                </label>
                                <label class="radio-card">
                                    <input type="radio" name="es_emprendedor" value="no">
                                    <span class="radio-label">No</span>
                                </label>
                            </div>
                            <span class="error-msg" id="es_emprendedor-error"></span>
                        </div>
                    </section>

                    <!-- PASO 2: Datos Personales -->
                    <section class="step" data-step="2">
                        <h2>Datos Personales</h2>
                        <p class="step-hint">Su correo electrónico se usará únicamente para evitar respuestas duplicadas.</p>

                        <div class="form-group">
                            <label for="email">Correo electrónico *</label>
                            <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>
                            <span class="error-msg" id="email-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="profesion">Profesión / Rol *</label>
                            <select id="profesion" name="profesion" required>
                                <option value="">Seleccione una opción</option>
                                <?php foreach ($PROFESIONES as $p): ?>
                                    <option value="<?php echo $p; ?>"><?php echo $p; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="error-msg" id="profesion-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="edad_rango">Rango de edad *</label>
                            <select id="edad_rango" name="edad_rango" required>
                                <option value="">Seleccione una opción</option>
                                <?php foreach ($EDADES_RANGO as $e): ?>
                                    <option value="<?php echo $e; ?>"><?php echo $e; ?> años</option>
                                <?php endforeach; ?>
                            </select>
                            <span class="error-msg" id="edad_rango-error"></span>
                        </div>

                        <div class="form-group">
                            <label for="barrio">Barrio / Zona de Guayaquil *</label>
                            <select id="barrio" name="barrio" required>
                                <option value="">Seleccione su zona</option>
                                <?php foreach ($BARRIOS_GUAYAQUIL as $b): ?>
                                    <option value="<?php echo $b; ?>"><?php echo $b; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <span class="error-msg" id="barrio-error"></span>
                        </div>
                    </section>

                    <!-- PASO 3: Bloque A -->
                    <section class="step" data-step="3">
                        <h2>Bloque A: Diagnóstico de Accesibilidad</h2>
                        <p class="step-hint">Objetivo Específico 1 — Evalúe cada afirmación según su experiencia.</p>

                        <div class="likert-group" data-field="p1">
                            <p class="pregunta">Al intentar acceder a la plataforma AppEmprende ISTG desde un dispositivo que no cuenta con sistema operativo Android (ej. iPhone/iOS, laptop o computadoras de escritorio), experimenté dificultades o imposibilidad de acceso.</p>
                            <div class="likert-options">
                                <label><input type="radio" name="p1" value="1" required><span>1</span><small>Totalmente en desacuerdo</small></label>
                                <label><input type="radio" name="p1" value="2"><span>2</span><small>En desacuerdo</small></label>
                                <label><input type="radio" name="p1" value="3"><span>3</span><small>Neutral</small></label>
                                <label><input type="radio" name="p1" value="4"><span>4</span><small>De acuerdo</small></label>
                                <label><input type="radio" name="p1" value="5"><span>5</span><small>Totalmente de acuerdo</small></label>
                            </div>
                            <span class="error-msg"></span>
                        </div>

                        <div class="likert-group" data-field="p2">
                            <p class="pregunta">Considero indispensable que las aplicaciones institucionales de emprendimiento del ISTG sean accesibles desde cualquier navegador web sin obligar a la instalación de un archivo específico.</p>
                            <div class="likert-options">
                                <label><input type="radio" name="p2" value="1" required><span>1</span><small>Totalmente en desacuerdo</small></label>
                                <label><input type="radio" name="p2" value="2"><span>2</span><small>En desacuerdo</small></label>
                                <label><input type="radio" name="p2" value="3"><span>3</span><small>Neutral</small></label>
                                <label><input type="radio" name="p2" value="4"><span>4</span><small>De acuerdo</small></label>
                                <label><input type="radio" name="p2" value="5"><span>5</span><small>Totalmente de acuerdo</small></label>
                            </div>
                            <span class="error-msg"></span>
                        </div>

                        <div class="likert-group" data-field="p3">
                            <p class="pregunta">La falta de compatibilidad multiplataforma de la versión anterior limitaba mi interacción con los emprendimientos estudiantiles registrados.</p>
                            <div class="likert-options">
                                <label><input type="radio" name="p3" value="1" required><span>1</span><small>Totalmente en desacuerdo</small></label>
                                <label><input type="radio" name="p3" value="2"><span>2</span><small>En desacuerdo</small></label>
                                <label><input type="radio" name="p3" value="3"><span>3</span><small>Neutral</small></label>
                                <label><input type="radio" name="p3" value="4"><span>4</span><small>De acuerdo</small></label>
                                <label><input type="radio" name="p3" value="5"><span>5</span><small>Totalmente de acuerdo</small></label>
                            </div>
                            <span class="error-msg"></span>
                        </div>
                    </section>

                    <!-- PASO 4: Bloque B -->
                    <section class="step" data-step="4">
                        <h2>Bloque B: Usabilidad de la PWA</h2>
                        <p class="step-hint">Objetivo Específico 3 — Evalúe la interfaz y experiencia de la PWA.</p>

                        <div class="likert-group" data-field="p4">
                            <p class="pregunta">La interfaz de usuario de la nueva Webapp (PWA) de EmprendeISTG es visualmente atractiva y moderna.</p>
                            <div class="likert-options">
                                <label><input type="radio" name="p4" value="1" required><span>1</span><small>Totalmente en desacuerdo</small></label>
                                <label><input type="radio" name="p4" value="2"><span>2</span><small>En desacuerdo</small></label>
                                <label><input type="radio" name="p4" value="3"><span>3</span><small>Neutral</small></label>
                                <label><input type="radio" name="p4" value="4"><span>4</span><small>De acuerdo</small></label>
                                <label><input type="radio" name="p4" value="5"><span>5</span><small>Totalmente de acuerdo</small></label>
                            </div>
                            <span class="error-msg"></span>
                        </div>

                        <div class="likert-group" data-field="p5">
                            <p class="pregunta">El proceso de navegación dentro de la PWA es intuitivo y me permitió encontrar la información de los negocios con facilidad.</p>
                            <div class="likert-options">
                                <label><input type="radio" name="p5" value="1" required><span>1</span><small>Totalmente en desacuerdo</small></label>
                                <label><input type="radio" name="p5" value="2"><span>2</span><small>En desacuerdo</small></label>
                                <label><input type="radio" name="p5" value="3"><span>3</span><small>Neutral</small></label>
                                <label><input type="radio" name="p5" value="4"><span>4</span><small>De acuerdo</small></label>
                                <label><input type="radio" name="p5" value="5"><span>5</span><small>Totalmente de acuerdo</small></label>
                            </div>
                            <span class="error-msg"></span>
                        </div>

                        <div class="likert-group" data-field="p6">
                            <p class="pregunta">Las funcionalidades de la PWA (revisión de catálogos, ofertas en tiempo real y enlaces a WhatsApp) responden de manera rápida y eficiente.</p>
                            <div class="likert-options">
                                <label><input type="radio" name="p6" value="1" required><span>1</span><small>Totalmente en desacuerdo</small></label>
                                <label><input type="radio" name="p6" value="2"><span>2</span><small>En desacuerdo</small></label>
                                <label><input type="radio" name="p6" value="3"><span>3</span><small>Neutral</small></label>
                                <label><input type="radio" name="p6" value="4"><span>4</span><small>De acuerdo</small></label>
                                <label><input type="radio" name="p6" value="5"><span>5</span><small>Totalmente de acuerdo</small></label>
                            </div>
                            <span class="error-msg"></span>
                        </div>

                        <div class="likert-group" data-field="p7">
                            <p class="pregunta">El rendimiento general de la PWA (velocidad de carga y consumo de recursos) es adecuado, indistintamente del dispositivo o sistema operativo desde el cual ingresé.</p>
                            <div class="likert-options">
                                <label><input type="radio" name="p7" value="1" required><span>1</span><small>Totalmente en desacuerdo</small></label>
                                <label><input type="radio" name="p7" value="2"><span>2</span><small>En desacuerdo</small></label>
                                <label><input type="radio" name="p7" value="3"><span>3</span><small>Neutral</small></label>
                                <label><input type="radio" name="p7" value="4"><span>4</span><small>De acuerdo</small></label>
                                <label><input type="radio" name="p7" value="5"><span>5</span><small>Totalmente de acuerdo</small></label>
                            </div>
                            <span class="error-msg"></span>
                        </div>
                    </section>

                    <!-- PASO 5: Bloque C -->
                    <section class="step" data-step="5">
                        <h2>Bloque C: Adopción e Impacto</h2>
                        <p class="step-hint">Objetivos Específicos 3 y 4 — Viabilidad y potencial de la PWA.</p>

                        <div class="likert-group" data-field="p8">
                            <p class="pregunta">Prefiero utilizar el formato de Aplicación Web Progresiva (PWA) debido a la facilidad de no ocupar espacio de almacenamiento interno en mi dispositivo celular.</p>
                            <div class="likert-options">
                                <label><input type="radio" name="p8" value="1" required><span>1</span><small>Totalmente en desacuerdo</small></label>
                                <label><input type="radio" name="p8" value="2"><span>2</span><small>En desacuerdo</small></label>
                                <label><input type="radio" name="p8" value="3"><span>3</span><small>Neutral</small></label>
                                <label><input type="radio" name="p8" value="4"><span>4</span><small>De acuerdo</small></label>
                                <label><input type="radio" name="p8" value="5"><span>5</span><small>Totalmente de acuerdo</small></label>
                            </div>
                            <span class="error-msg"></span>
                        </div>

                        <div class="likert-group" data-field="p9">
                            <p class="pregunta">Considero que la migración a esta arquitectura web incrementará significativamente la visibilidad digital y el éxito comercial de los emprendedores del ISTG.</p>
                            <div class="likert-options">
                                <label><input type="radio" name="p9" value="1" required><span>1</span><small>Totalmente en desacuerdo</small></label>
                                <label><input type="radio" name="p9" value="2"><span>2</span><small>En desacuerdo</small></label>
                                <label><input type="radio" name="p9" value="3"><span>3</span><small>Neutral</small></label>
                                <label><input type="radio" name="p9" value="4"><span>4</span><small>De acuerdo</small></label>
                                <label><input type="radio" name="p9" value="5"><span>5</span><small>Totalmente de acuerdo</small></label>
                            </div>
                            <span class="error-msg"></span>
                        </div>
                    </section>

                    <!-- PASO 6: Cualitativa -->
                    <section class="step" data-step="6">
                        <h2>Comentarios Finales</h2>
                        <p class="step-hint">Retroalimentación cualitativa (opcional).</p>

                        <div class="form-group">
                            <label for="p10_mejoras">Describa brevemente qué aspectos técnicos, visuales o funcionales recomendaría mejorar en esta PWA antes de su implementación institucional definitiva.</label>
                            <textarea id="p10_mejoras" name="p10_mejoras" rows="5" maxlength="1000" placeholder="Ej: Notificaciones push, modo oscuro, filtros avanzados, panel 'Mis Negocios', mejoras en búsqueda..."></textarea>
                            <span class="char-count"><span id="charCount">0</span> / 1000</span>
                        </div>
                    </section>

                    <!-- NAVEGACIÓN -->
                    <div class="nav-buttons">
                        <button type="button" id="btnPrev" class="btn btn-secondary" style="display:none;">← Anterior</button>
                        <button type="button" id="btnNext" class="btn btn-primary">Siguiente →</button>
                        <button type="submit" id="btnSubmit" class="btn btn-submit" style="display:none;">Enviar Respuestas</button>
                    </div>

                </form>
            </div>
        </main>

        <footer class="site-footer">
            <div class="footer-counter">
                <span id="counterValue">-</span> respuestas registradas
            </div>
            <a href="https://pucusoft.pages.dev/" target="_blank" rel="noopener noreferrer" class="footer-link">
                <img src="static/img/pucusoft.png" alt="Pucusoft" class="footer-logo">
                <span>Desarrollado por Pucusoft</span>
            </a>
        </footer>
    </div>

    <script src="static/js/encuesta.js"></script>
</body>
</html>
