CREATE TABLE IF NOT EXISTS respuestas (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    created_at TIMESTAMPTZ DEFAULT NOW(),

    -- Datos demográficos
    email VARCHAR(255) NOT NULL,

    -- Preguntas de filtrado
    tiene_app VARCHAR(2) NOT NULL CHECK (tiene_app IN ('si','no')),
    es_emprendedor VARCHAR(2) NOT NULL CHECK (es_emprendedor IN ('si','no')),

    profesion VARCHAR(100) NOT NULL,
    edad_rango VARCHAR(20) NOT NULL CHECK (edad_rango IN ('18-25','26-35','36-45','46+')),
    barrio VARCHAR(100) NOT NULL,

    -- Bloque A: Diagnóstico de Accesibilidad (OE1)
    p1 INT NOT NULL CHECK (p1 BETWEEN 1 AND 5),
    p2 INT NOT NULL CHECK (p2 BETWEEN 1 AND 5),
    p3 INT NOT NULL CHECK (p3 BETWEEN 1 AND 5),

    -- Bloque B: Usabilidad y Aceptación Tecnológica (OE3)
    p4 INT NOT NULL CHECK (p4 BETWEEN 1 AND 5),
    p5 INT NOT NULL CHECK (p5 BETWEEN 1 AND 5),
    p6 INT NOT NULL CHECK (p6 BETWEEN 1 AND 5),
    p7 INT NOT NULL CHECK (p7 BETWEEN 1 AND 5),

    -- Bloque C: Adopción e Impacto (OE3, OE4)
    p8 INT NOT NULL CHECK (p8 BETWEEN 1 AND 5),
    p9 INT NOT NULL CHECK (p9 BETWEEN 1 AND 5),

    -- Cualitativa
    p10_mejoras TEXT DEFAULT '',

    -- Metadatos
    user_agent TEXT,
    duracion_segundos INT DEFAULT 0,
    version_encuesta VARCHAR(5) DEFAULT '1.0'
);

-- Un email = una respuesta
CREATE UNIQUE INDEX IF NOT EXISTS idx_email_unico ON respuestas(email);

-- Índices para dashboard
CREATE INDEX IF NOT EXISTS idx_respuestas_created ON respuestas(created_at);
CREATE INDEX IF NOT EXISTS idx_respuestas_profesion ON respuestas(profesion);
CREATE INDEX IF NOT EXISTS idx_respuestas_barrio ON respuestas(barrio);
CREATE INDEX IF NOT EXISTS idx_respuestas_edad ON respuestas(edad_rango);
CREATE INDEX IF NOT EXISTS idx_respuestas_tiene_app ON respuestas(tiene_app);
CREATE INDEX IF NOT EXISTS idx_respuestas_es_emprendedor ON respuestas(es_emprendedor);
