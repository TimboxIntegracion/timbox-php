<?php

// Parametros para la conexión al Webservice
$wsdl_url        = 'https://staging.ws.timbox.com.mx/cancelacion/wsdl';
$wsdl_usuario    = '';
$wsdl_contrasena = '';

// Parametros para la cancelación del CFDI
$rfc_emisor      = 'EKU9003173C9';
$rfc_receptor    = 'XAXX010101000';
$uuid            = '926DB77E-8128-4D25-AAF8-045B4258F4A0';
$total           = '14500.00';
$motivo          = '02';
$folio_sustituto = '';

$file_cer_pem = file_get_contents('../certificados_keys_pruebas/EKU9003173C9.cer.pem');
$file_key_pem = file_get_contents('../certificados_keys_pruebas/EKU9003173C9.key.pem');

$uuids_cancelar = [
    [
        'uuid'            => $uuid,
        'rfc_receptor'    => $rfc_receptor,
        'total'           => $total,
        'motivo'          => $motivo,
        'folio_sustituto' => $folio_sustituto
    ]];

//  Crear un cliente para hacer la petición al WS
$cliente = new SoapClient($wsdl_url, ['trace' => 1, 'use' => SOAP_LITERAL]);

// Parametros para llamar la funcion cancelar_cfdi
// Nota: Tener en cuenta el orden de los parametros enviados.
$parametros = [
    'username'   => $wsdl_usuario,
    'password'   => $wsdl_contrasena,
    'rfc_emisor' => $rfc_emisor,
    'folios'     => $uuids_cancelar,
    'cert_pem'   => $file_cer_pem,
    'llave_pem'  => $file_key_pem,
];

try {
    // Llamar la funcion cancelar_cfdi
    // $respuesta = $cliente->__soapCall('cancelar_cfdi', $parametros);
    print_r($cliente->__soapCall('cancelar_cfdi', $parametros));
    die();
    echo "\nResponse:\n";
    var_dump($respuesta);
    echo "\nNodo Folios:\n";
    echo $respuesta->folios_cancelacion;
    echo "\n\nNodo Acuse:\n";
    echo $respuesta->acuse_cancelacion;
} catch (Exception $exception) {
    // Imprimir los mensajes de la excepcion
    echo '# del error: ' . $exception->getCode() . "\n";
    echo 'Descripción del error: ' . $exception->getMessage() . "\n";
}
