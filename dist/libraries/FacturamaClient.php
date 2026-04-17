<?php
// dist/facturacion/lib/FacturamaClient.php

class FacturamaClient {
  private string $baseUrl;
  private string $user;
  private string $password;

  public function __construct(array $cfg){
    $this->baseUrl = $cfg['sandbox']
      ? 'https://apisandbox.facturama.mx'
      : 'https://api.facturama.mx';
    $this->user = $cfg['user'];
    $this->password = $cfg['password'];
  }

  private function request(string $method, string $path, ?array $jsonBody=null): array {
    $url = $this->baseUrl . $path;
    $ch = curl_init($url);
    $headers = [
      'Accept: application/json',
      'Content-Type: application/json',
      'Authorization: Basic ' . base64_encode($this->user . ':' . $this->password),
    ];

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    if($jsonBody !== null){
      curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($jsonBody, JSON_UNESCAPED_UNICODE));
    }

    $resp = curl_exec($ch);
    $http = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err  = curl_error($ch);
    curl_close($ch);

    if($resp === false){
      return ['ok'=>false, 'http'=>$http, 'error'=>$err ?: 'curl_error', 'raw'=>null];
    }

    $decoded = json_decode($resp, true);
    if($http >= 200 && $http < 300){
      return ['ok'=>true, 'http'=>$http, 'data'=>$decoded, 'raw'=>$resp];
    }
    return ['ok'=>false, 'http'=>$http, 'error'=>$decoded ?: $resp, 'raw'=>$resp];
  }

  // Crear CFDI (Factura/Complemento) API Web
  // Path para API Web: /api-lite/3/cfdis  (la guía “Factura” y “Complemento de pago” usa este esquema)
  public function createCfdi(array $cfdi): array {
    return $this->request('POST', '/api-lite/3/cfdis', $cfdi);
  }

  // Descargar CFDI: PDF o XML
  public function downloadPdf(string $id): array {
    return $this->request('GET', '/api-lite/cfdis/'.$id.'/pdf', null);
  }
  public function downloadXml(string $id): array {
    return $this->request('GET', '/api-lite/cfdis/'.$id.'/xml', null);
  }

  // Cancelar CFDI (API Web) — usa guías de cancelación con motivo
  public function cancelCfdi(string $uuid, string $motivo, ?string $uuidSustitucion=null): array {
    $body = ['Motivo' => $motivo];
    if($uuidSustitucion){ $body['FolioSustitucion'] = $uuidSustitucion; }
    return $this->request('DELETE', '/api-lite/3/cfdis/'.$uuid, $body);
  }
}
