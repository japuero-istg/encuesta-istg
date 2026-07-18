# Encuesta EmprendeISTG

Encuesta web de evaluación de usabilidad y accesibilidad de la plataforma **EmprendeISTG** — Proyecto de titulación del Instituto Superior Tecnológico Guayaquil (ISTG).

## Stack

- **Backend:** PHP 8.x
- **Base de datos:** MySQL 8
- **Frontend:** HTML/CSS/JS + Plotly.js (gráficos)
- **Hosting:** Byet.host (free tier)

## Preguntas de la encuesta

La encuesta consta de **7 pasos** y **9 preguntas tipo Likert** (escala 1-5):

| Paso | Sección | Preguntas |
|------|---------|-----------|
| 1 | Consentimiento informado | — |
| 2 | Filtrado | Tiene app, es emprendedor |
| 3 | Datos personales | Email, profesión, edad, barrio |
| 4 | Bloque A — Accesibilidad | P1, P2, P3 |
| 5 | Bloque B — Usabilidad PWA | P4, P5, P6, P7 |
| 6 | Bloque C — Adopción | P8, P9 |
| 7 | Comentarios | Texto libre |

## Estructura del proyecto

```
├── static/
│   ├── css/encuesta.css
│   ├── js/encuesta.js
│   └── img/                    ← logos ISTG y Pucusoft
├── config.php                  ← credenciales DB + auth admin
├── db.php                      ← conexión MySQL (mysqli)
├── schema.sql                  ← esquema MySQL
├── index.php                   ← encuesta
├── submit.php                  ← POST respuesta
├── gracias.php                 ← agradecimiento
├── dashboard.php               ← panel admin (Plotly.js)
├── export.php                  ← CSV/JSON
├── count.php                   ← API contador
├── analysis.php                ← media, DE, α de Cronbach
├── .gitignore
└── README.md
```

## Análisis estadístico

- Media y desviación estándar por ítem
- Frecuencias y porcentajes por opción Likert
- Porcentaje de acuerdo (respuestas 4 y 5)
- Alpha de Cronbach por bloque (A, B, C)
- Matriz de frecuencias ConsolidarEscalaLikert

## Admin

- Acceso al dashboard: `/dashboard.php`
- Autenticación HTTP Basic (usuario/contraseña en `config.php`)
- Exportación CSV y JSON

## Desarrollo

```bash
# Ejecutar localmente (desde la carpeta del proyecto)
php -S localhost:8000

# Importar esquema en MySQL
mysql -u root -p < schema.sql
```

## Licencia

Proyecto académico — ISTG 2026
