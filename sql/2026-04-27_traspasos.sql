-- ============================================================
-- TRASPASOS ENTRE ALMACENES
-- ============================================================

-- Tabla temporal por sesión (borrador)
CREATE TABLE IF NOT EXISTS tmp_traspaso (
  id_tmp                 INT            NOT NULL AUTO_INCREMENT,
  session_id             VARCHAR(128)   NOT NULL,
  id_producto_origen     INT            NOT NULL DEFAULT 0,
  referencia_tmp         VARCHAR(100)   NOT NULL DEFAULT '',
  descripcion_tmp        VARCHAR(255)   NOT NULL DEFAULT '',
  lote_tmp               VARCHAR(100)   NOT NULL DEFAULT '',
  caducidad_tmp          DATE           DEFAULT NULL,
  cantidad_tmp           DECIMAL(12,4)  NOT NULL DEFAULT 0,
  id_almacen_origen_tmp  INT            NOT NULL DEFAULT 0,
  id_almacen_destino_tmp INT            NOT NULL DEFAULT 0,
  PRIMARY KEY (id_tmp),
  KEY idx_session (session_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Encabezado de traspasos
CREATE TABLE IF NOT EXISTS traspasos (
  id_traspaso        INT            NOT NULL AUTO_INCREMENT,
  folio              INT            NOT NULL,
  fecha_traspaso     DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_usuario         INT            NOT NULL,
  id_almacen_origen  INT            NOT NULL,
  id_almacen_destino INT            NOT NULL,
  observaciones      TEXT           DEFAULT NULL,
  estatus            ENUM('ACTIVO','CANCELADO') NOT NULL DEFAULT 'ACTIVO',
  PRIMARY KEY (id_traspaso),
  KEY idx_folio (folio)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Detalle de traspasos
CREATE TABLE IF NOT EXISTS traspasos_detalle (
  id_det_traspaso    INT            NOT NULL AUTO_INCREMENT,
  id_traspaso        INT            NOT NULL,
  referencia         VARCHAR(100)   NOT NULL DEFAULT '',
  descripcion        VARCHAR(255)   NOT NULL DEFAULT '',
  lote               VARCHAR(100)   NOT NULL DEFAULT '',
  caducidad          DATE           DEFAULT NULL,
  cantidad           DECIMAL(12,4)  NOT NULL,
  id_almacen_origen  INT            NOT NULL,
  id_almacen_destino INT            NOT NULL,
  PRIMARY KEY (id_det_traspaso),
  KEY idx_traspaso (id_traspaso)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Consecutivo para folio de traspaso
INSERT IGNORE INTO consecutivos (nombre, valor) VALUES ('folio_traspaso', 0);

-- Agrega 'traspaso' al ENUM de notificaciones (si la tabla ya existe)
ALTER TABLE notificaciones MODIFY COLUMN tipo ENUM('remision','factura','recepcion','traspaso') NOT NULL;
