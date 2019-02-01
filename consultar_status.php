<?php

// Parametros para la conexión al Webservice
$wsdl_url = "https://staging.ws.timbox.com.mx/cancelacion/wsdl";
$wsdl_usuario = "AAA010101000";
$wsdl_contrasena = "h6584D56fVdBbSmmnB";

// Parametros para la consulta del CFDI
$rfc_emisor = "AAA010101AAA";
$rfc_receptor = "IAD121214B34";
$uuid  = "D6B0CDE5-0E45-4049-8B67-E5D5B08ACFC9";
$total = "7261.60";
//crear un cliente para hacer la petición al WS
$cliente = new SoapClient($wsdl_url, array('trace' => 1, 'use' => SOAP_LITERAL));

// Parametros para llamar la funcion consultar_estatus
// Nota: Tener en cuenta el orden de los parametros enviados.
$parametros = array(
    "username" => $wsdl_usuario,
    "password" => $wsdl_contrasena,
    "uuid" => $uuid,
    "rfc_emisor" => $rfc_emisor,
    "rfc_receptor" => $rfc_receptor,
    "total" => $total
);

try {
    // Llamar la funcion consultar_estatus
    $respuesta = $cliente->__soapCall("consultar_estatus", $parametros);
    echo $respuesta->estado ." ". $respuesta->estatus_cancelacion . "\n";
} catch (Exception $exception) {
    // Imprimir los mensajes de la excepcion
    echo "# del error: " . $exception->getCode() . "\n";
    echo "Descripción del error: " . $exception->getMessage() . "\n";
}
