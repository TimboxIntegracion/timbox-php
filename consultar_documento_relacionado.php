<?php

// Parametros para la conexión al Webservice
$wsdl_url = "https://staging.ws.timbox.com.mx/cancelacion/wsdl";
$wsdl_usuario = "AAA010101000";
$wsdl_contrasena = "h6584D56fVdBbSmmnB";

// Parametros para la consulta de documentos relacionados
$rfc_receptor = "AAA010101AAA";
$uuid  = "3E30C124-58FB-408B-84D6-C253E8E573F1";

$file_cer_pem = file_get_contents("CSD01_AAA010101AAA.cer.pem");
$file_key_pem = file_get_contents("CSD01_AAA010101AAA.key.pem");

// crear un cliente para hacer la petición al WS
$cliente = new SoapClient($wsdl_url);

// Parametros para llamar la funcion consultar_documento_relacionado
// Nota: Tener en cuenta el orden de los parametros enviados.
$parametros = array(
    "username" => $wsdl_usuario,
    "password" => $wsdl_contrasena,
    "uuid" => $uuid,
    "rfc_receptor" => $rfc_receptor,
    "cert_pem" => $file_cer_pem,
    "llave_pem" => $file_key_pem,
);

try {
    // Llamar la funcion consultar_documento_relacionado
    $respuesta = $cliente->__soapCall("consultar_documento_relacionado", $parametros);
    echo $respuesta->resultado ."\n". $respuesta->relacionados_padres . "\n" . $respuesta->relacionados_hijos."\n";
} catch (Exception $exception) {
    // Imprimir los mensajes de la excepcion
    echo "# del error: " . $exception->getCode() . "\n";
    echo "Descripción del error: " . $exception->getMessage() . "\n";
}

