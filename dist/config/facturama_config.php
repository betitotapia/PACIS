<?php
// dist/facturacion/config/facturama_config.php
// Configura tus credenciales (Sandbox/Producción) y datos del emisor.
// IMPORTANTE: NO subas tus credenciales a GitHub.

return [
  'sandbox' => true, // true = sandbox, false = producción

  // Credenciales Facturama (API Web)
  'user' => 'betitotapia',
  'password' => 'Marifer2707',

  // Emisor (tu RFC único)
  'issuer' => [
    'Rfc' => 'TAAA830909SZ2',
    'Name' => 'ALBERTO CARLOS TAPIA ANDRADE', // en mayúsculas y sin “S.A. DE C.V.”
    'FiscalRegime' => '612', // c_RegimenFiscal
  ],

  // Sucursal / lugar de expedición (CP de la sucursal dada de alta en Facturama)
  'expedition_place' => '72499',

  // Serie/Folio internos (opcional)
  'default_serie' => 'A',

  // NameId: catálogo “Nombres del CFDI” en Facturama (1 suele ser “Factura”)
  'name_id_factura' => 1,
  'name_id_pago' => 1,
];
