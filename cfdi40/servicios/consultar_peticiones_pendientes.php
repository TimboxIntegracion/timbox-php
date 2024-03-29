<?php

//parametros para la conexión al Webservice
$wsdl_url        = 'https://staging.ws.timbox.com.mx/cancelacion/wsdl';
$wsdl_usuario    = '';
$wsdl_contrasena = '';

//parametros para la cancelación del CFDI
$rfc_receptor = 'EKU9003173C9';

$file_cer_pem = file_get_contents('../certiificados_key_pruebas/EKU9003173C9.cer.pem');
$file_key_pem = file_get_contents('../certiificados_key_pruebas/EKU9003173C9.key.pem');

//crear un cliente para hacer la petición al WS
$cliente = new SoapClient($wsdl_url, ['trace' => 1, 'use' => SOAP_LITERAL]);

//parametros para llamar la funcion consultar_peticiones_pendientes
// Nota: Tener en cuenta el orden de los parametros enviados.
$parametros = [
    'username'     => $wsdl_usuario,
    'password'     => $wsdl_contrasena,
    'rfc_receptor' => $rfc_receptor,
    'cert_pem'     => $file_cer_pem,
    'llave_pem'    => $file_key_pem,
];

try {
    //llamar la funcion consultar_peticiones_pendientes
    $respuesta = $cliente->__soapCall('consultar_peticiones_pendientes', $parametros);
    // Imprimir lista de uuids pendientes
    print_r([$respuesta->uuids]);
} catch (Exception $exception) {
    //imprimir los mensajes de la excepcion
    echo '# del error: ' . $exception->getCode() . "\n";
    echo 'Descripción del error: ' . $exception->getMessage() . "\n";
}
