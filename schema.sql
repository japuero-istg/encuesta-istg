-- =============================================
-- Encuesta EmprendeISTG — Schema MySQL
-- Vistas y Triggers
-- =============================================

CREATE TABLE IF NOT EXISTS respuestas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    email VARCHAR(255) NOT NULL,
    tiene_app VARCHAR(2) NOT NULL,
    es_emprendedor VARCHAR(2) NOT NULL,
    profesion VARCHAR(100) NOT NULL,
    edad_rango VARCHAR(20) NOT NULL,
    barrio VARCHAR(100) NOT NULL,
    p1 INT NOT NULL,
    p2 INT NOT NULL,
    p3 INT NOT NULL,
    p4 INT NOT NULL,
    p5 INT NOT NULL,
    p6 INT NOT NULL,
    p7 INT NOT NULL,
    p8 INT NOT NULL,
    p9 INT NOT NULL,
    p10_mejoras TEXT DEFAULT '',
    user_agent TEXT,
    duracion_segundos INT DEFAULT 0,
    version_encuesta VARCHAR(5) DEFAULT '1.0',

    CHECK (tiene_app IN ('si','no')),
    CHECK (es_emprendedor IN ('si','no')),
    CHECK (edad_rango IN ('18-25','26-35','36-45','46+')),
    CHECK (p1 BETWEEN 1 AND 5),
    CHECK (p2 BETWEEN 1 AND 5),
    CHECK (p3 BETWEEN 1 AND 5),
    CHECK (p4 BETWEEN 1 AND 5),
    CHECK (p5 BETWEEN 1 AND 5),
    CHECK (p6 BETWEEN 1 AND 5),
    CHECK (p7 BETWEEN 1 AND 5),
    CHECK (p8 BETWEEN 1 AND 5),
    CHECK (p9 BETWEEN 1 AND 5),

    UNIQUE KEY idx_email_unico (email),
    INDEX idx_respuestas_created (created_at),
    INDEX idx_respuestas_profesion (profesion),
    INDEX idx_respuestas_barrio (barrio),
    INDEX idx_respuestas_edad (edad_rango)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- VISTAS
-- =============================================

CREATE OR REPLACE VIEW v_demografia_profesion AS
SELECT profesion, COUNT(*) AS total,
       ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM respuestas), 1) AS porcentaje
FROM respuestas
GROUP BY profesion
ORDER BY total DESC;

CREATE OR REPLACE VIEW v_demografia_edad AS
SELECT edad_rango, COUNT(*) AS total,
       ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM respuestas), 1) AS porcentaje
FROM respuestas
GROUP BY edad_rango
ORDER BY total DESC;

CREATE OR REPLACE VIEW v_demografia_barrio AS
SELECT barrio, COUNT(*) AS total,
       ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM respuestas), 1) AS porcentaje
FROM respuestas
GROUP BY barrio
ORDER BY total DESC;

CREATE OR REPLACE VIEW v_filtrado AS
SELECT
    SUM(CASE WHEN tiene_app = 'si' THEN 1 ELSE 0 END) AS tiene_app_si,
    SUM(CASE WHEN tiene_app = 'no' THEN 1 ELSE 0 END) AS tiene_app_no,
    SUM(CASE WHEN es_emprendedor = 'si' THEN 1 ELSE 0 END) AS emprendedor_si,
    SUM(CASE WHEN es_emprendedor = 'no' THEN 1 ELSE 0 END) AS emprendedor_no,
    COUNT(*) AS total
FROM respuestas;

CREATE OR REPLACE VIEW v_frecuencias_likert AS
SELECT
    COUNT(*) AS total_encuestados,
    SUM(CASE WHEN p1=1 THEN 1 ELSE 0 END) AS p1_f1,
    SUM(CASE WHEN p1=2 THEN 1 ELSE 0 END) AS p1_f2,
    SUM(CASE WHEN p1=3 THEN 1 ELSE 0 END) AS p1_f3,
    SUM(CASE WHEN p1=4 THEN 1 ELSE 0 END) AS p1_f4,
    SUM(CASE WHEN p1=5 THEN 1 ELSE 0 END) AS p1_f5,
    SUM(CASE WHEN p2=1 THEN 1 ELSE 0 END) AS p2_f1,
    SUM(CASE WHEN p2=2 THEN 1 ELSE 0 END) AS p2_f2,
    SUM(CASE WHEN p2=3 THEN 1 ELSE 0 END) AS p2_f3,
    SUM(CASE WHEN p2=4 THEN 1 ELSE 0 END) AS p2_f4,
    SUM(CASE WHEN p2=5 THEN 1 ELSE 0 END) AS p2_f5,
    SUM(CASE WHEN p3=1 THEN 1 ELSE 0 END) AS p3_f1,
    SUM(CASE WHEN p3=2 THEN 1 ELSE 0 END) AS p3_f2,
    SUM(CASE WHEN p3=3 THEN 1 ELSE 0 END) AS p3_f3,
    SUM(CASE WHEN p3=4 THEN 1 ELSE 0 END) AS p3_f4,
    SUM(CASE WHEN p3=5 THEN 1 ELSE 0 END) AS p3_f5,
    SUM(CASE WHEN p4=1 THEN 1 ELSE 0 END) AS p4_f1,
    SUM(CASE WHEN p4=2 THEN 1 ELSE 0 END) AS p4_f2,
    SUM(CASE WHEN p4=3 THEN 1 ELSE 0 END) AS p4_f3,
    SUM(CASE WHEN p4=4 THEN 1 ELSE 0 END) AS p4_f4,
    SUM(CASE WHEN p4=5 THEN 1 ELSE 0 END) AS p4_f5,
    SUM(CASE WHEN p5=1 THEN 1 ELSE 0 END) AS p5_f1,
    SUM(CASE WHEN p5=2 THEN 1 ELSE 0 END) AS p5_f2,
    SUM(CASE WHEN p5=3 THEN 1 ELSE 0 END) AS p5_f3,
    SUM(CASE WHEN p5=4 THEN 1 ELSE 0 END) AS p5_f4,
    SUM(CASE WHEN p5=5 THEN 1 ELSE 0 END) AS p5_f5,
    SUM(CASE WHEN p6=1 THEN 1 ELSE 0 END) AS p6_f1,
    SUM(CASE WHEN p6=2 THEN 1 ELSE 0 END) AS p6_f2,
    SUM(CASE WHEN p6=3 THEN 1 ELSE 0 END) AS p6_f3,
    SUM(CASE WHEN p6=4 THEN 1 ELSE 0 END) AS p6_f4,
    SUM(CASE WHEN p6=5 THEN 1 ELSE 0 END) AS p6_f5,
    SUM(CASE WHEN p7=1 THEN 1 ELSE 0 END) AS p7_f1,
    SUM(CASE WHEN p7=2 THEN 1 ELSE 0 END) AS p7_f2,
    SUM(CASE WHEN p7=3 THEN 1 ELSE 0 END) AS p7_f3,
    SUM(CASE WHEN p7=4 THEN 1 ELSE 0 END) AS p7_f4,
    SUM(CASE WHEN p7=5 THEN 1 ELSE 0 END) AS p7_f5,
    SUM(CASE WHEN p8=1 THEN 1 ELSE 0 END) AS p8_f1,
    SUM(CASE WHEN p8=2 THEN 1 ELSE 0 END) AS p8_f2,
    SUM(CASE WHEN p8=3 THEN 1 ELSE 0 END) AS p8_f3,
    SUM(CASE WHEN p8=4 THEN 1 ELSE 0 END) AS p8_f4,
    SUM(CASE WHEN p8=5 THEN 1 ELSE 0 END) AS p8_f5,
    SUM(CASE WHEN p9=1 THEN 1 ELSE 0 END) AS p9_f1,
    SUM(CASE WHEN p9=2 THEN 1 ELSE 0 END) AS p9_f2,
    SUM(CASE WHEN p9=3 THEN 1 ELSE 0 END) AS p9_f3,
    SUM(CASE WHEN p9=4 THEN 1 ELSE 0 END) AS p9_f4,
    SUM(CASE WHEN p9=5 THEN 1 ELSE 0 END) AS p9_f5
FROM respuestas;

CREATE OR REPLACE VIEW v_medias_likert AS
SELECT
    ROUND(AVG(p1),2) AS p1_media, ROUND(AVG(p2),2) AS p2_media,
    ROUND(AVG(p3),2) AS p3_media, ROUND(AVG(p4),2) AS p4_media,
    ROUND(AVG(p5),2) AS p5_media, ROUND(AVG(p6),2) AS p6_media,
    ROUND(AVG(p7),2) AS p7_media, ROUND(AVG(p8),2) AS p8_media,
    ROUND(AVG(p9),2) AS p9_media,
    COUNT(*) AS total
FROM respuestas;

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

CREATE OR REPLACE VIEW v_tiempos AS
SELECT
    ROUND(AVG(duracion_segundos),1) AS tiempo_promedio_seg,
    MIN(duracion_segundos) AS tiempo_min_seg,
    MAX(duracion_segundos) AS tiempo_max_seg,
    COUNT(*) AS total
FROM respuestas;

-- =============================================
-- TRIGGER: validar email duplicado
-- =============================================
DELIMITER //

CREATE TRIGGER trg_check_email
BEFORE INSERT ON respuestas
FOR EACH ROW
BEGIN
    IF EXISTS (SELECT 1 FROM respuestas WHERE email = NEW.email) THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Email duplicado: ya existe una respuesta con este correo';
    END IF;
END //

-- =============================================
-- TRIGGER: log de actividad
-- =============================================
CREATE TABLE IF NOT EXISTS log_actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tabla VARCHAR(50),
    operacion VARCHAR(10),
    registro_id VARCHAR(100),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    detalle TEXT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 //

CREATE TRIGGER trg_log_respuestas_insert
AFTER INSERT ON respuestas
FOR EACH ROW
BEGIN
    INSERT INTO log_actividad (tabla, operacion, registro_id, detalle)
    VALUES ('respuestas', 'INSERT', NEW.id, CONCAT('Nueva respuesta de ', NEW.email));
END //

CREATE TRIGGER trg_log_respuestas_delete
AFTER DELETE ON respuestas
FOR EACH ROW
BEGIN
    INSERT INTO log_actividad (tabla, operacion, registro_id, detalle)
    VALUES ('respuestas', 'DELETE', OLD.id, CONCAT('Respuesta eliminada: ', OLD.email));
END //

DELIMITER ;
