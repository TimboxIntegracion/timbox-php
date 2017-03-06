<?php
//parametros para conexion al Webservice (URL de Pruebas)
$wsdl_url = "https://staging.ws.timbox.com.mx/timbrado/wsdl";
$wsdl_usuario = "user_name";
$wsdl_contrasena = "password";
$ruta_xml = "ruta/del/archivo.xml";

#convertir la cadena del xml en base64
$documento_xml = file_get_contents($ruta_xml);
$xml_base64 = base64_encode($documento_xml);

//crear un cliente para hacer la petición al WS
$cliente = new SoapClient($wsdl_url, array(
        'trace' => 1,
        'use' => SOAP_LITERAL,
    ));

//parametros para llamar la funcion timbrar_cfdi
$parametros = array(
    "username" => $wsdl_usuario,
    "password" => $wsdl_contrasena,
    "sxml" => $xml_base64,
);

try {
    //llamar la funcion timbrar_cfdi
    $respuesta = $cliente->__soapCall("timbrar_cfdi", $parametros);
    //imprimir el contenido del XML timbrado
    var_dump($cliente->__getLastResponse());
} catch (Exception $exception) {
    //imprimir los mensajes de la excepcion
    echo $exception->getCode();
    echo $exception->getMessage();
}

?>
