<?php
// dist/facturacion/lib/cfdi_helpers.php

function sat_code_only(?string $value): ?string {
  // Muchos sistemas guardan “G03 - Gastos en general” o similar.
  if($value === null) return null;
  $value = trim($value);
  if($value === '') return null;
  // toma el primer token alfanumérico (ej "G03", "PPD", "03")
  if(preg_match('/^([A-Z0-9]{2,5})\b/i', $value, $m)){
    return strtoupper($m[1]);
  }
  return $value;
}

function upper_name_for_sat(string $name): string {
  $name = trim($name);
  $name = mb_strtoupper($name, 'UTF-8');
  // Remueve regimen societario común (opcional, ajusta a tu caso)
  $name = preg_replace('/\bS\.?A\.?\s*DE\s*C\.?V\.?\b/i', '', $name);
  $name = preg_replace('/\s{2,}/', ' ', $name);
  return trim($name);
}

function money2($n): float {
  return round((float)$n, 2);
}
