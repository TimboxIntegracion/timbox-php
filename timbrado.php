<?php
// Este metodo se utiliza para actualizar la fecha del XML, por lo que se requiere que
// actualice el sello del mismo
function actualizarSello($archivoXml){
    //Leer XML
    $xmlDoc = new DOMDocument();
    $xmlDoc->load($archivoXml);
    
    //Cambiar Fecha a actual y guardar en archivo
    date_default_timezone_set('America/Mexico_City');
    $date = date('Y-m-d_H:i:s');
    $date = str_replace("_", "T", $date);
    $xmlDoc->firstChild->setAttribute('Fecha', $date);
    $xmlString = $xmlDoc->saveXML();
    file_put_contents($archivoXml, $xmlString);

    //Crear cadena original
    $xslt = new DOMDocument();
    $xslt->load('cadenaoriginal_3_3.xslt');
    $xml = new DOMDocument;
    $xml->load($archivoXml);

    $proc = new XSLTProcessor;
    @$proc->importStyleSheet($xslt); // attach the xsl rules
    $cadena = $proc->transformToXML($xml);
    file_put_contents('cadena_original.txt', $cadena);
    
    //Firmar cadena y obtener el digest
    $key = file_get_contents('CSD01_AAA010101AAA.key.pem');
    openssl_sign($cadena, $digest, $key, OPENSSL_ALGO_SHA256);
    file_put_contents('digest.txt', $digest);
    
    //Generar Sello
    $command = "openssl enc -in digest.txt -out sello.txt -base64 -A -K CSD01_AAA010101AAA.key.pem";
    $res = shell_exec($command);

    //Actualizar el sello del XML
    $sello = file_get_contents('sello.txt');
    $xmlDoc->firstChild->setAttribute('Sello', $sello);
    $xmlString = $xmlDoc->saveXML();
    file_put_contents($archivoXml, $xmlString);    
}

//parametros para conexion al Webservice (URL de Pruebas)
$wsdl_url = "https://staging.ws.timbox.com.mx/timbrado_cfdi33/wsdl";
$wsdl_usuario = "AAA010101000";
$wsdl_contrasena = "h6584D56fVdBbSmmnB";
$ruta_xml = "archivoXml.xml";


actualizarSello($ruta_xml);
// convertir la cadena del xml en base64
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
    echo $respuesta->xml;

} catch (Exception $exception) {
    //imprimir los mensajes de la excepcion
    echo "# del error: " . $exception->getCode() . "\n";
    echo "Descripción del error: " . $exception->getMessage() . "\n";
}

?>
