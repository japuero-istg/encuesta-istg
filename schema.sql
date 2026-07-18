-- =============================================
-- Encuesta EmprendeISTG — Schema PostgreSQL
-- Vistas y Triggers
-- =============================================

-- Tabla principal (ya existe, pero por si acaso)
CREATE TABLE IF NOT EXISTS respuestas (
    id UUID PRIMARY KEY DEFAULT gen_random_uuid(),
    created_at TIMESTAMPTZ DEFAULT NOW(),
    email VARCHAR(255) NOT NULL,
    tiene_app VARCHAR(2) NOT NULL CHECK (tiene_app IN ('si','no')),
    es_emprendedor VARCHAR(2) NOT NULL CHECK (es_emprendedor IN ('si','no')),
    profesion VARCHAR(100) NOT NULL,
    edad_rango VARCHAR(20) NOT NULL CHECK (edad_rango IN ('18-25','26-35','36-45','46+')),
    barrio VARCHAR(100) NOT NULL,
    p1 INT NOT NULL CHECK (p1 BETWEEN 1 AND 5),
    p2 INT NOT NULL CHECK (p2 BETWEEN 1 AND 5),
    p3 INT NOT NULL CHECK (p3 BETWEEN 1 AND 5),
    p4 INT NOT NULL CHECK (p4 BETWEEN 1 AND 5),
    p5 INT NOT NULL CHECK (p5 BETWEEN 1 AND 5),
    p6 INT NOT NULL CHECK (p6 BETWEEN 1 AND 5),
    p7 INT NOT NULL CHECK (p7 BETWEEN 1 AND 5),
    p8 INT NOT NULL CHECK (p8 BETWEEN 1 AND 5),
    p9 INT NOT NULL CHECK (p9 BETWEEN 1 AND 5),
    p10_mejoras TEXT DEFAULT '',
    user_agent TEXT,
    duracion_segundos INT DEFAULT 0,
    version_encuesta VARCHAR(5) DEFAULT '1.0'
);

CREATE UNIQUE INDEX IF NOT EXISTS idx_email_unico ON respuestas(email);
CREATE INDEX IF NOT EXISTS idx_respuestas_created ON respuestas(created_at);
CREATE INDEX IF NOT EXISTS idx_respuestas_profesion ON respuestas(profesion);
CREATE INDEX IF NOT EXISTS idx_respuestas_barrio ON respuestas(barrio);
CREATE INDEX IF NOT EXISTS idx_respuestas_edad ON respuestas(edad_rango);

-- =============================================
-- VISTAS
-- =============================================

-- Vista: resumen demográfico por profesión
CREATE OR REPLACE VIEW v_demografia_profesion AS
SELECT profesion, COUNT(*) AS total,
       ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM respuestas), 1) AS porcentaje
FROM respuestas
GROUP BY profesion
ORDER BY total DESC;

-- Vista: resumen demográfico por edad
CREATE OR REPLACE VIEW v_demografia_edad AS
SELECT edad_rango, COUNT(*) AS total,
       ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM respuestas), 1) AS porcentaje
FROM respuestas
GROUP BY edad_rango
ORDER BY total DESC;

-- Vista: resumen demográfico por barrio
CREATE OR REPLACE VIEW v_demografia_barrio AS
SELECT barrio, COUNT(*) AS total,
       ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM respuestas), 1) AS porcentaje
FROM respuestas
GROUP BY barrio
ORDER BY total DESC;

-- Vista: filtrado (tiene_app, es_emprendedor)
CREATE OR REPLACE VIEW v_filtrado AS
SELECT
    SUM(CASE WHEN tiene_app = 'si' THEN 1 ELSE 0 END) AS tiene_app_si,
    SUM(CASE WHEN tiene_app = 'no' THEN 1 ELSE 0 END) AS tiene_app_no,
    SUM(CASE WHEN es_emprendedor = 'si' THEN 1 ELSE 0 END) AS emprendedor_si,
    SUM(CASE WHEN es_emprendedor = 'no' THEN 1 ELSE 0 END) AS emprendedor_no,
    COUNT(*) AS total
FROM respuestas;

-- Vista: frecuencias por ítem Likert (1-5)
CREATE OR REPLACE VIEW v_frecuencias_likert AS
SELECT
    COUNT(*) AS total_encuestados,
    -- P1
    SUM(CASE WHEN p1=1 THEN 1 ELSE 0 END) AS p1_f1, SUM(CASE WHEN p1=2 THEN 1 ELSE 0 END) AS p1_f2,
    SUM(CASE WHEN p1=3 THEN 1 ELSE 0 END) AS p1_f3, SUM(CASE WHEN p1=4 THEN 1 ELSE 0 END) AS p1_f4,
    SUM(CASE WHEN p1=5 THEN 1 ELSE 0 END) AS p1_f5,
    -- P2
    SUM(CASE WHEN p2=1 THEN 1 ELSE 0 END) AS p2_f1, SUM(CASE WHEN p2=2 THEN 1 ELSE 0 END) AS p2_f2,
    SUM(CASE WHEN p2=3 THEN 1 ELSE 0 END) AS p2_f3, SUM(CASE WHEN p2=4 THEN 1 ELSE 0 END) AS p2_f4,
    SUM(CASE WHEN p2=5 THEN 1 ELSE 0 END) AS p2_f5,
    -- P3
    SUM(CASE WHEN p3=1 THEN 1 ELSE 0 END) AS p3_f1, SUM(CASE WHEN p3=2 THEN 1 ELSE 0 END) AS p3_f2,
    SUM(CASE WHEN p3=3 THEN 1 ELSE 0 END) AS p3_f3, SUM(CASE WHEN p3=4 THEN 1 ELSE 0 END) AS p3_f4,
    SUM(CASE WHEN p3=5 THEN 1 ELSE 0 END) AS p3_f5,
    -- P4
    SUM(CASE WHEN p4=1 THEN 1 ELSE 0 END) AS p4_f1, SUM(CASE WHEN p4=2 THEN 1 ELSE 0 END) AS p4_f2,
    SUM(CASE WHEN p4=3 THEN 1 ELSE 0 END) AS p4_f3, SUM(CASE WHEN p4=4 THEN 1 ELSE 0 END) AS p4_f4,
    SUM(CASE WHEN p4=5 THEN 1 ELSE 0 END) AS p4_f5,
    -- P5
    SUM(CASE WHEN p5=1 THEN 1 ELSE 0 END) AS p5_f1, SUM(CASE WHEN p5=2 THEN 1 ELSE 0 END) AS p5_f2,
    SUM(CASE WHEN p5=3 THEN 1 ELSE 0 END) AS p5_f3, SUM(CASE WHEN p5=4 THEN 1 ELSE 0 END) AS p5_f4,
    SUM(CASE WHEN p5=5 THEN 1 ELSE 0 END) AS p5_f5,
    -- P6
    SUM(CASE WHEN p6=1 THEN 1 ELSE 0 END) AS p6_f1, SUM(CASE WHEN p6=2 THEN 1 ELSE 0 END) AS p6_f2,
    SUM(CASE WHEN p6=3 THEN 1 ELSE 0 END) AS p6_f3, SUM(CASE WHEN p6=4 THEN 1 ELSE 0 END) AS p6_f4,
    SUM(CASE WHEN p6=5 THEN 1 ELSE 0 END) AS p6_f5,
    -- P7
    SUM(CASE WHEN p7=1 THEN 1 ELSE 0 END) AS p7_f1, SUM(CASE WHEN p7=2 THEN 1 ELSE 0 END) AS p7_f2,
    SUM(CASE WHEN p7=3 THEN 1 ELSE 0 END) AS p7_f3, SUM(CASE WHEN p7=4 THEN 1 ELSE 0 END) AS p7_f4,
    SUM(CASE WHEN p7=5 THEN 1 ELSE 0 END) AS p7_f5,
    -- P8
    SUM(CASE WHEN p8=1 THEN 1 ELSE 0 END) AS p8_f1, SUM(CASE WHEN p8=2 THEN 1 ELSE 0 END) AS p8_f2,
    SUM(CASE WHEN p8=3 THEN 1 ELSE 0 END) AS p8_f3, SUM(CASE WHEN p8=4 THEN 1 ELSE 0 END) AS p8_f4,
    SUM(CASE WHEN p8=5 THEN 1 ELSE 0 END) AS p8_f5,
    -- P9
    SUM(CASE WHEN p9=1 THEN 1 ELSE 0 END) AS p9_f1, SUM(CASE WHEN p9=2 THEN 1 ELSE 0 END) AS p9_f2,
    SUM(CASE WHEN p9=3 THEN 1 ELSE 0 END) AS p9_f3, SUM(CASE WHEN p9=4 THEN 1 ELSE 0 END) AS p9_f4,
    SUM(CASE WHEN p9=5 THEN 1 ELSE 0 END) AS p9_f5
FROM respuestas;

-- Vista: medias por ítem
CREATE OR REPLACE VIEW v_medias_likert AS
SELECT
    ROUND(AVG(p1),2) AS p1_media, ROUND(AVG(p2),2) AS p2_media,
    ROUND(AVG(p3),2) AS p3_media, ROUND(AVG(p4),2) AS p4_media,
    ROUND(AVG(p5),2) AS p5_media, ROUND(AVG(p6),2) AS p6_media,
    ROUND(AVG(p7),2) AS p7_media, ROUND(AVG(p8),2) AS p8_media,
    ROUND(AVG(p9),2) AS p9_media,
    COUNT(*) AS total
FROM respuestas;

-- Vista: porcentaje de acuerdo por ítem (respuestas 4 y 5)
CREATE OR REPLACE VIEW v_acuerdo_likert AS
SELECT
    ROUND(SUM(CASE WHEN p1>=4 THEN 1 ELSE 0 END)*100.0/COUNT(*),1) AS p1_acuerdo,
    ROUND(SUM(CASE WHEN p2>=4 THEN 1 ELSE 0 END)*100.0/COUNT(*),1) AS p2_acuerdo,
    ROUND(SUM(CASE WHEN p3>=4 THEN 1 ELSE 0 END)*100.0/COUNT(*),1) AS p3_acuerdo,
    ROUND(SUM(CASE WHEN p4>=4 THEN 1 ELSE 0 END)*100.0/COUNT(*),1) AS p4_acuerdo,
    ROUND(SUM(CASE WHEN p5>=4 THEN 1 ELSE 0 END)*100.0/COUNT(*),1) AS p5_acuerdo,
    ROUND(SUM(CASE WHEN p6>=4 THEN 1 ELSE 0 END)*100.0/COUNT(*),1) AS p6_acuerdo,
    ROUND(SUM(CASE WHEN p7>=4 THEN 1 ELSE 0 END)*100.0/COUNT(*),1) AS p7_acuerdo,
    ROUND(SUM(CASE WHEN p8>=4 THEN 1 ELSE 0 END)*100.0/COUNT(*),1) AS p8_acuerdo,
    ROUND(SUM(CASE WHEN p9>=4 THEN 1 ELSE 0 END)*100.0/COUNT(*),1) AS p9_acuerdo,
    COUNT(*) AS total
FROM respuestas;

-- Vista: tiempos de respuesta
CREATE OR REPLACE VIEW v_tiempos AS
SELECT
    ROUND(AVG(duracion_segundos),1) AS tiempo_promedio_seg,
    MIN(duracion_segundos) AS tiempo_min_seg,
    MAX(duracion_segundos) AS tiempo_max_seg,
    COUNT(*) AS total
FROM respuestas;

-- =============================================
-- TRIGGER: validar email duplicado (por seguridad adicional)
-- =============================================
CREATE OR REPLACE FUNCTION fn_check_email_duplicado()
RETURNS TRIGGER AS $$
BEGIN
    IF EXISTS (SELECT 1 FROM respuestas WHERE email = NEW.email AND id != NEW.id) THEN
        RAISE EXCEPTION 'El email % ya fue registrado', NEW.email;
    END IF;
    RETURN NEW;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_check_email ON respuestas;
CREATE TRIGGER trg_check_email
    BEFORE INSERT ON respuestas
    FOR EACH ROW
    EXECUTE FUNCTION fn_check_email_duplicado();

-- =============================================
-- TRIGGER: log de actividad (tabla auxiliar)
-- =============================================
CREATE TABLE IF NOT EXISTS log_actividad (
    id SERIAL PRIMARY KEY,
    tabla VARCHAR(50),
    operacion VARCHAR(10),
    registro_id VARCHAR(100),
    fecha TIMESTAMPTZ DEFAULT NOW(),
    detalle TEXT
);

CREATE OR REPLACE FUNCTION fn_log_respuestas()
RETURNS TRIGGER AS $$
BEGIN
    INSERT INTO log_actividad (tabla, operacion, registro_id, detalle)
    VALUES (
        'respuestas',
        TG_OP,
        COALESCE(NEW.id::text, OLD.id::text),
        CASE TG_OP
            WHEN 'INSERT' THEN 'Nueva respuesta de ' || NEW.email
            WHEN 'DELETE' THEN 'Respuesta eliminada: ' || OLD.email
            ELSE 'Actualización'
        END
    );
    RETURN COALESCE(NEW, OLD);
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER IF EXISTS trg_log_respuestas ON respuestas;
CREATE TRIGGER trg_log_respuestas
    AFTER INSERT OR DELETE ON respuestas
    FOR EACH ROW
    EXECUTE FUNCTION fn_log_respuestas();
