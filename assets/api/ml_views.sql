-- =============================
-- Vistas para Machine Learning
-- BD: schoolcare
-- =============================

-- 1) Etiqueta: 1 si promedio < 6.00, 0 en otro caso (NO usaremos este promedio como feature)
CREATE OR REPLACE VIEW ml_labels AS
SELECT
  c.estudiante_id,
  c.clase_id,
  c.periodo_id,
  c.promedio,
  CASE WHEN c.promedio < 6.00 THEN 1 ELSE 0 END AS y_reprobado
FROM calificaciones c;

-- 2) Asistencia: % de presentes por alumno-clase-periodo
-- Mapeamos materia -> clase con clase_asignacion y recortamos por fechas del periodo
CREATE OR REPLACE VIEW ml_asistencia AS
SELECT
  a.estudiante_id,
  ca.clase_id,
  p.periodo_id,
  AVG(CASE WHEN a.estado IN ('presente','asistió','presente_total') THEN 1.0 ELSE 0.0 END) AS asistencia_pct
FROM asistencias a
JOIN clase_asignacion ca ON ca.materia_id = a.materia_id
JOIN periodos p ON a.fecha BETWEEN p.fecha_inicio AND p.fecha_fin
GROUP BY a.estudiante_id, ca.clase_id, p.periodo_id;

-- 3) Incidentes: conteo por alumno-clase-periodo
CREATE OR REPLACE VIEW ml_incidentes AS
SELECT
  i.estudiante_id,
  ca.clase_id,
  p.periodo_id,
  COUNT(*) AS incidentes_count
FROM incidentes i
JOIN clase_asignacion ca ON ca.materia_id = i.materia_id
JOIN periodos p ON i.fecha BETWEEN p.fecha_inicio AND p.fecha_fin
GROUP BY i.estudiante_id, ca.clase_id, p.periodo_id;

-- 4) Parciales (promedio de calificaciones_detalle) como feature de calificación temprana
CREATE OR REPLACE VIEW ml_parciales AS
SELECT
  cd.calificacion_id,
  AVG(cd.valor) AS parciales_avg
FROM calificaciones_detalle cd
GROUP BY cd.calificacion_id;

CREATE OR REPLACE VIEW ml_feat_parciales AS
SELECT
  c.estudiante_id,
  c.clase_id,
  c.periodo_id,
  p.parciales_avg
FROM calificaciones c
LEFT JOIN ml_parciales p ON p.calificacion_id = c.calificacion_id;

-- 5) Dataset final para entrenar y predecir
CREATE OR REPLACE VIEW ml_dataset AS
SELECT
  y.estudiante_id,
  y.clase_id,
  y.periodo_id,
  y.y_reprobado,
  COALESCE(a.asistencia_pct, 0)   AS asistencia_pct,
  COALESCE(i.incidentes_count, 0) AS incidentes_count,
  COALESCE(fp.parciales_avg, 0)   AS parciales_avg
FROM ml_labels y
LEFT JOIN ml_asistencia a
  ON a.estudiante_id = y.estudiante_id AND a.clase_id = y.clase_id AND a.periodo_id = y.periodo_id
LEFT JOIN ml_incidentes i
  ON i.estudiante_id = y.estudiante_id AND i.clase_id = y.clase_id AND i.periodo_id = y.periodo_id
LEFT JOIN ml_feat_parciales fp
  ON fp.estudiante_id = y.estudiante_id AND fp.clase_id = y.clase_id AND fp.periodo_id = y.periodo_id;
