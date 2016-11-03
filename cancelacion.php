<?php
//parametros para la conexión al Webservice
$wsdl_url = "https://staging.ws.timbox.com.mx/timbrado/wsdl";
$wsdl_usuario = "user_name";
$wsdl_contrasena = "password";

//parametros para la cancelación del CFDI
$rfc = "IAD121214B34";
$uuid = "A7A812CC-3B51-4623-A219-8F4173D061FE";
$pfx_path = 'path_del_archivo/iad121214b34.pfx';
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
    "uuids" => $uuid,
    "pfxbase64" => $pfx_base64,
    "pfxpassword" => $pfx_password,
);

try {
    //llamar la funcion cancelar_cfdi
    $respuesta = $cliente->__soapCall("cancelar_cfdi", $parametros);
    var_dump($respuesta);
} catch (Exception $exception) {
    //imprimir los mensajes de la excepcion
    echo $exception->getCode();
    echo $exception->getMessage();
}

?>
