(() => {
    let pasoActual = 0;
    const totalPasos = 7;
    const form = document.getElementById('encuestaForm');
    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    const btnSubmit = document.getElementById('btnSubmit');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    const charCount = document.getElementById('charCount');
    const inicioTiempo = Date.now();

    const titulos = [
        'Consentimiento Informado',
        'Preguntas de Filtrado',
        'Datos Personales',
        'Bloque A: Diagnóstico de Accesibilidad',
        'Bloque B: Usabilidad de la PWA',
        'Bloque C: Adopción e Impacto',
        'Comentarios Finales',
    ];

    const camposObligatoriosPaso = {
        0: ['consentimiento'],
        1: ['tiene_app', 'es_emprendedor'],
        2: ['email', 'profesion', 'edad_rango', 'barrio'],
        3: ['p1', 'p2', 'p3'],
        4: ['p4', 'p5', 'p6', 'p7', 'p7b'],
        5: ['p8', 'p9'],
        6: [],
    };

    function mostrarPaso(n) {
        document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
        document.querySelector(`.step[data-step="${n}"]`).classList.add('active');
        pasoActual = n;

        progressFill.style.width = `${((n + 1) / totalPasos) * 100}%`;
        progressText.textContent = `Paso ${n + 1} de ${totalPasos}: ${titulos[n]}`;

        btnPrev.style.display = n > 0 ? 'inline-block' : 'none';
        btnNext.style.display = n < totalPasos - 1 ? 'inline-block' : 'none';
        btnSubmit.style.display = n === totalPasos - 1 ? 'inline-block' : 'none';
    }

    function limpiarErrores() {
        document.querySelectorAll('.error-msg').forEach(e => e.textContent = '');
        document.querySelectorAll('.likert-group').forEach(g => g.classList.remove('has-error'));
    }

    function validarPaso(n) {
        limpiarErrores();
        const campos = camposObligatoriosPaso[n] || [];
        let valido = true;

        for (const campo of campos) {
            let valor = null;

            if (campo === 'consentimiento') {
                const cb = document.getElementById('consentimiento');
                valor = cb && cb.checked ? 'ok' : null;
            } else if (['email', 'profesion', 'edad_rango', 'barrio'].includes(campo)) {
                const el = document.getElementById(campo);
                valor = el.value.trim();
            } else {
                const checked = form.querySelector(`input[name="${campo}"]:checked`);
                valor = checked ? checked.value : null;
            }

            if (!valor || valor === '') {
                valido = false;
                const errEl = document.getElementById(`${campo}-error`);
                if (errEl) {
                    errEl.textContent = 'Esta pregunta es obligatoria';
                } else {
                    const group = form.querySelector(`.likert-group[data-field="${campo}"]`);
                    if (group) {
                        group.classList.add('has-error');
                        const span = group.querySelector('.error-msg');
                        if (span) span.textContent = 'Seleccione una opción';
                    } else {
                        const formGroup = document.getElementById(campo)?.closest('.form-group');
                        if (formGroup) {
                            const span = formGroup.querySelector('.error-msg');
                            if (span) span.textContent = 'Este campo es obligatorio';
                        }
                    }
                }
            }
        }

        if (n === 2) {
            const email = document.getElementById('email').value.trim();
            if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                valido = false;
                const errEl = document.getElementById('email-error');
                if (errEl) errEl.textContent = 'Ingrese un email válido';
            }
        }

        return valido;
    }

    btnNext.addEventListener('click', () => {
        if (validarPaso(pasoActual)) {
            mostrarPaso(pasoActual + 1);
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });

    btnPrev.addEventListener('click', () => {
        mostrarPaso(pasoActual - 1);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!validarPaso(pasoActual)) return;

        const duracion = Math.round((Date.now() - inicioTiempo) / 1000);

        const data = {
            email: document.getElementById('email').value.trim().toLowerCase(),
            tiene_app: form.querySelector('input[name="tiene_app"]:checked')?.value || '',
            es_emprendedor: form.querySelector('input[name="es_emprendedor"]:checked')?.value || '',
            profesion: document.getElementById('profesion').value,
            edad_rango: document.getElementById('edad_rango').value,
            barrio: document.getElementById('barrio').value,
            p1: parseInt(form.querySelector('input[name="p1"]:checked')?.value || 0),
            p2: parseInt(form.querySelector('input[name="p2"]:checked')?.value || 0),
            p3: parseInt(form.querySelector('input[name="p3"]:checked')?.value || 0),
            p4: parseInt(form.querySelector('input[name="p4"]:checked')?.value || 0),
            p5: parseInt(form.querySelector('input[name="p5"]:checked')?.value || 0),
            p6: parseInt(form.querySelector('input[name="p6"]:checked')?.value || 0),
            p7: parseInt(form.querySelector('input[name="p7"]:checked')?.value || 0),
            p7b: parseInt(form.querySelector('input[name="p7b"]:checked')?.value || 0),
            p8: parseInt(form.querySelector('input[name="p8"]:checked')?.value || 0),
            p9: parseInt(form.querySelector('input[name="p9"]:checked')?.value || 0),
            p10_mejoras: document.getElementById('p10_mejoras')?.value || '',
            duracion_segundos: duracion,
        };

        btnSubmit.disabled = true;
        btnSubmit.textContent = 'Enviando...';

        try {
            const resp = await fetch('/api/submit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data),
            });
            const result = await resp.json();
            if (result.ok) {
                window.location.href = '/gracias';
            } else {
                alert(result.error || 'Error al enviar. Intente de nuevo.');
                btnSubmit.disabled = false;
                btnSubmit.textContent = 'Enviar Respuestas';
            }
        } catch (err) {
            alert('Error de conexión. Intente de nuevo.');
            btnSubmit.disabled = false;
            btnSubmit.textContent = 'Enviar Respuestas';
        }
    });

    if (charCount) {
        const ta = document.getElementById('p10_mejoras');
        if (ta) {
            ta.addEventListener('input', () => { charCount.textContent = ta.value.length; });
        }
    }

    mostrarPaso(0);
})();
