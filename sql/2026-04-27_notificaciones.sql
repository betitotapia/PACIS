CREATE TABLE IF NOT EXISTS notificaciones (
  id_notificacion INT AUTO_INCREMENT PRIMARY KEY,
  tipo            ENUM('remision','factura','recepcion') NOT NULL,
  mensaje         VARCHAR(255) NOT NULL,
  id_referencia   INT NOT NULL DEFAULT 0,
  folio           VARCHAR(50) DEFAULT NULL,
  leida           TINYINT(1) NOT NULL DEFAULT 0,
  fecha_creacion  DATETIME NOT NULL DEFAULT NOW()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
