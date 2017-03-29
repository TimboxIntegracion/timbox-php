<?php
//parametros para la conexión al Webservice
$wsdl_url = "https://staging.ws.timbox.com.mx/timbrado/wsdl";
$wsdl_usuario = "AAA010101000";
$wsdl_contrasena = "h6584D56fVdBbSmmnB";

//parametros para la cancelación del CFDI
$rfc = "AAA010101AAA";
$uuid_cancelar = "3553A1EE-FFD9-47A3-95D9-1528E65AF5CF";
$pfx_path = 'archivoPfx.pfx';
$bin_file = file_get_contents($pfx_path);
$pfx_base64 = base64_encode($bin_file);
echo $pfx_base64;
$pfx_password = "12345678a";

//crear un cliente para hacer la petición al WS
$cliente = new SoapClient($wsdl_url);

//crear el array de uuid o uuids
//este nodo se repite cuantas veces se quiera cancelar un uuid
$uuid = array("uuid" => $uuid_cancelar);

//parametros para llamar la funcion cancelar_cfdi
$parametros = array(
    "username" => $wsdl_usuario,
    "password" => $wsdl_contrasena,
    "rfcemisor" => $rfc,
    "uuids" => $uuid,
    "pfxbase64" => $pfx_base64,
    "pfxpassword" => $pfx_password,
);

try {
    //llamar la funcion cancelar_cfdi
    $respuesta = $cliente->__soapCall("cancelar_cfdi", $parametros);
    echo $respuesta->comprobantes_cancelados;
} catch (Exception $exception) {
    //imprimir los mensajes de la excepcion
    echo "# del error: " . $exception->getCode() . "\n";
    echo "Descripción del error: " . $exception->getMessage() . "\n";
}

?>
