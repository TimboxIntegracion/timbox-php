<?php

// Parametros para la conexión al Webservice
$wsdl_url        = 'https://staging.ws.timbox.com.mx/cancelacion/wsdl';
$wsdl_usuario    = '';
$wsdl_contrasena = '';

// Parametros para procesar respuesta del CFDI
$rfc_emisor   = 'EKU9003173C9';
$rfc_receptor = 'PZA000413788';
$uuid         = 'F40EB7C6-F138-425D-BD31-0A416FC2E2C5';
$total        = '5420.34';
$file_cer_pem = file_get_contents('../certiificados_key_pruebas/EKU9003173C9.cer.pem');
$file_key_pem = file_get_contents('../certiificados_key_pruebas/EKU9003173C9.key.pem');

// A(Aceptar la solicitud), R(Rechazar la solicitud)
$respuesta_de_solicitud = 'A';

$respuestas = [
    [
        'uuid'       => $uuid,
        'rfc_emisor' => $rfc_emisor,
        'total'      => $total,
        'respuesta'  => $respuesta_de_solicitud
    ]
    // ... n folios_respuestas a procesar
];

// Crear un cliente para hacer la petición al WS
$cliente = new SoapClient($wsdl_url, ['trace' => 1, 'use' => SOAP_LITERAL]);

// Parametros para llamar la funcion procesar_respuesta
// Nota: Tener en cuenta el orden de los parámetros enviados.
$parametros = [
    'username'     => $wsdl_usuario,
    'password'     => $wsdl_contrasena,
    'rfc_receptor' => $rfc_receptor,
    'respuestas'   => $respuestas,
    'cert_pem'     => $file_cer_pem,
    'llave_pem'    => $file_key_pem
];

try {
    // Llamar la funcion procesar_respuesta
    $respuesta = $cliente->__soapCall('procesar_respuesta', $parametros);
    // Imprimir lista de folios respuesta
    print_r([$respuesta->folios]);
} catch (Exception $exception) {
    // Imprimir los mensajes de la excepcion
    echo '# del error: ' . $exception->getCode() . "\n";
    echo 'Descripción del error: ' . $exception->getMessage() . "\n";
}