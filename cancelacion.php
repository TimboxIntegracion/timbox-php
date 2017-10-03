<?php
//parametros para la conexión al Webservice
$wsdl_url = "https://staging.ws.timbox.com.mx/timbrado_cfdi33/wsdl";
$wsdl_usuario = "AAA010101000";
$wsdl_contrasena = "h6584D56fVdBbSmmnB";

//parametros para la cancelación del CFDI
//, ,
$rfc = "AAA010101AAA";
$uuids_cancelar = array("E28DBCF2-F852-4B2F-8198-CD8383891EB0",
    "3CFF7200-0DE5-4BEE-AC22-AA2A49052FBC",
    "51408B33-FE29-47DA-9517-FBF420240FD3");
$pfx_path = 'archivoPfx.pfx';
$bin_file = file_get_contents($pfx_path);
$pfx_base64 = base64_encode($bin_file);
$pfx_password = "12345678a";

//crear un cliente para hacer la petición al WS
$cliente = new SoapClient($wsdl_url);

//parametros para llamar la funcion cancelar_cfdi
$parametros = array(
    "username" => $wsdl_usuario,
    "password" => $wsdl_contrasena,
    "rfcemisor" => $rfc,
    "uuids" => $uuids_cancelar,
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
