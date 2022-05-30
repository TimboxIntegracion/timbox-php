<?php

// Parametros para la conexión al Webservice
$wsdl_url        = 'https://staging.ws.timbox.com.mx/cancelacion/wsdl';
$wsdl_usuario    = '';
$wsdl_contrasena = '';

// Parametros para la cancelación del CFDI
$rfc_emisor      = 'EKU9003173C9';
$rfc_receptor    = 'IAD121214B34';
$uuid            = '2E5E92CC-11C2-4621-8566-95A2079283A0';
$total           = '5420.34';
$motivo          = '01';
$folio_sustituto = '1EEEC338-E260-4418-85AB-B52B9E36F89C';

$file_cer_pem = file_get_contents('../certiificados_key_pruebas/EKU9003173C9.cer.pem');
$file_key_pem = file_get_contents('../certiificados_key_pruebas/EKU9003173C9.key.pem');

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
