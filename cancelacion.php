<?php

// Parametros para la conexión al Webservice
$wsdl_url = "https://staging.ws.timbox.com.mx/cancelacion/wsdl";
$wsdl_usuario = "AAA010101000";
$wsdl_contrasena = "h6584D56fVdBbSmmnB";

// Parametros para la cancelación del CFDI
$rfc_emisor = "AAA010101AAA";
$rfc_receptor = "IAD121214B34";
$uuid = "D6B0CDE5-0E45-4049-8B67-E5D5B08ACFC9";
$total = "7261.60";

$file_cer_pem = file_get_contents("CSD01_AAA010101AAA.cer.pem");
$file_key_pem = file_get_contents("CSD01_AAA010101AAA.key.pem");

$uuids_cancelar = array(
         "folio" => array(
            "uuid" => $uuid,
            "rfc_receptor" => $rfc_receptor,
            "total" => $total
        )
        // ... n folio a cancelar
);

//  Crear un cliente para hacer la petición al WS
$cliente = new SoapClient($wsdl_url);

// Parametros para llamar la funcion cancelar_cfdi
// Nota: Tener en cuenta el orden de los parametros enviados.
$parametros = array(
    "username" => $wsdl_usuario,
    "password" => $wsdl_contrasena,
    "rfc_emisor" => $rfc_emisor,
    "folios" => $uuids_cancelar,
    "cert_pem" => $file_cer_pem,
    "llave_pem" => $file_key_pem,
);

try {
    // Llamar la funcion cancelar_cfdi
    $respuesta = $cliente->__soapCall("cancelar_cfdi", $parametros);
    echo $respuesta->acuse_cancelacion;
} catch (Exception $exception) {
    // Imprimir los mensajes de la excepcion
    echo "# del error: " . $exception->getCode() . "\n";
    echo "Descripción del error: " . $exception->getMessage() . "\n";
}
