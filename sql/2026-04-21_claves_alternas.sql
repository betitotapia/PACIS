-- PACIS: claves alternas por producto
-- Fecha: 2026-04-21

-- =====================================================
-- 1) CAMBIOS MINIMOS OBLIGATORIOS
-- =====================================================
-- Agrega dos campos alternos en catalogo de productos.

ALTER TABLE products
  ADD COLUMN cve_alterna_1 varchar(240) DEFAULT NULL AFTER referencia,
  ADD COLUMN cve_alterna_2 varchar(240) DEFAULT NULL AFTER cve_alterna_1;

-- Indices para busqueda por claves alternas (no unicos).
ALTER TABLE products
  ADD INDEX idx_products_cve_alterna_1 (cve_alterna_1),
  ADD INDEX idx_products_cve_alterna_2 (cve_alterna_2);


-- =====================================================
-- 2) RECOMENDADO (MODELO POR CLIENTE)
-- =====================================================
-- Define que tipo de clave prefiere ver cada cliente en remision/factura.
-- ORIGINAL: products.referencia
-- ALTERNA_1: products.cve_alterna_1
-- ALTERNA_2: products.cve_alterna_2

ALTER TABLE clientes
  ADD COLUMN tipo_referencia enum('ORIGINAL','ALTERNA_1','ALTERNA_2')
  NOT NULL DEFAULT 'ORIGINAL' AFTER credito;


-- =====================================================
-- 3) OPCIONAL (AUDITORIA DE LO MOSTRADO)
-- =====================================================
-- Si quieres guardar de forma explicita de donde salio la referencia mostrada
-- al momento de crear el detalle.

ALTER TABLE detalle_factura
  ADD COLUMN referencia_fuente enum('ORIGINAL','ALTERNA_1','ALTERNA_2','MANUAL')
  NOT NULL DEFAULT 'ORIGINAL' AFTER referencia;


-- =====================================================
-- 4) SOPORTE RECEPCION (RECOMENDADO)
-- =====================================================
-- Permite capturar claves alternas desde recepcion y conservarlas
-- hasta el guardado final en inventario.

ALTER TABLE tmp_recepcion
  ADD COLUMN cve_alterna_1_tmp varchar(240) DEFAULT NULL AFTER referencia_tmp,
  ADD COLUMN cve_alterna_2_tmp varchar(240) DEFAULT NULL AFTER cve_alterna_1_tmp;


-- =====================================================
-- 5) SERIES DE FACTURACION
-- =====================================================

CREATE TABLE IF NOT EXISTS facturacion_series (
  id_serie int NOT NULL AUTO_INCREMENT,
  serie varchar(10) NOT NULL,
  descripcion varchar(120) DEFAULT NULL,
  folio_actual int NOT NULL DEFAULT 0,
  activo tinyint(1) NOT NULL DEFAULT 1,
  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at datetime DEFAULT NULL,
  PRIMARY KEY (id_serie),
  UNIQUE KEY uq_facturacion_series_serie (serie)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

ALTER TABLE fact_facturas
  ADD COLUMN id_serie_facturacion int DEFAULT NULL AFTER id_remision,
  ADD KEY idx_fact_facturas_id_serie (id_serie_facturacion);

-- Nota: fact_facturas esta en MyISAM en la base actual, por eso no se crea FK.
-- La integridad se maneja en aplicacion.
