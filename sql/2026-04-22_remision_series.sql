-- PACIS: series y folios para remisiones
-- Fecha: 2026-04-22

-- =====================================================
-- SERIES DE REMISION
-- =====================================================
-- Tabla de series/folios para remisiones, analoga a facturacion_series.

CREATE TABLE IF NOT EXISTS remision_series (
  id_serie int NOT NULL AUTO_INCREMENT,
  serie varchar(10) NOT NULL,
  descripcion varchar(120) DEFAULT NULL,
  folio_actual int NOT NULL DEFAULT 0,
  activo tinyint(1) NOT NULL DEFAULT 1,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime DEFAULT NULL,
  PRIMARY KEY (id_serie),
  UNIQUE KEY uq_remision_series_serie (serie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Agrega columnas de serie/folio a la tabla facturas (remisiones).
-- La tabla facturas es MyISAM, por eso no se crea FK; integridad en aplicacion.
ALTER TABLE facturas
  ADD COLUMN id_serie_remision int DEFAULT NULL AFTER numero_factura,
  ADD COLUMN serie_remision varchar(10) DEFAULT NULL AFTER id_serie_remision,
  ADD COLUMN folio_remision int DEFAULT NULL AFTER serie_remision;

-- ADD KEY no soporta IF NOT EXISTS; ejecutar solo si el indice no existe aun.
ALTER TABLE facturas
  ADD KEY idx_facturas_id_serie_remision (id_serie_remision);
