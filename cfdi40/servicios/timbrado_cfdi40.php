<?php
// Este metodo se utiliza para actualizar la fecha del XML, por lo que se requiere que
// actualice el sello del mismo
function actualizarSello($archivoXml)
{
    //Leer XML
    $xmlDoc = new DOMDocument();
    $xmlDoc->load($archivoXml);
    //Cambiar Fecha a actual y guardar en archivo

    //Obtener el lugar de expedicion del xml
    $lugarDeExpedicion = $xmlDoc->firstChild->getAttribute('LugarExpedicion');
    //Consultar la zona horaria segun el lugar de expedición
    $zonaHoraria = zonaHorariaPorCP($lugarDeExpedicion);
    //Establecer la zona horaria
    date_default_timezone_set($zonaHoraria);
    $date = date('Y-m-d_H:i:s');
    $date = str_replace('_', 'T', $date);
    $xmlDoc->firstChild->setAttribute('Fecha', $date);
    $xmlString = $xmlDoc->saveXML();
    file_put_contents($archivoXml, $xmlString);

    //Crear cadena original
    $xslt = new DOMDocument();
    $xslt->load('../cadena_original/cadenaoriginal_4_0.xslt');
    $xml = new DOMDocument;
    $xml->load($archivoXml);

    $proc = new XSLTProcessor;
    @$proc->importStyleSheet($xslt); // attach the xsl rules
    $cadena = $proc->transformToXML($xml);
    file_put_contents('../cadena_original.txt', $cadena);

    //Firmar cadena y obtener el digest
    $key = file_get_contents('../certificados_keys_pruebas/EKU9003173C9.key.pem');
    openssl_sign($cadena, $digest, $key, OPENSSL_ALGO_SHA256);
    file_put_contents('../digest.txt', $digest);

    //Generar Sello
    $sello = base64_encode($digest);
    file_put_contents('../cfdi40/sello.txt', $sello);

    //Actualizar el sello del XML
    $xmlDoc->firstChild->setAttribute('Sello', $sello);
    $xmlString = $xmlDoc->saveXML();
    file_put_contents($archivoXml, $xmlString);
}

// Esta funcion se utiliza para obtener la zona horaria
// en base al lugar de expedición
function zonaHorariaPorCP($lugarDeExpedicion)
{
    $f        = fopen('../cat_postal_codes.csv', 'r');
    $timeZone = '';
    while ($row = fgetcsv($f)) {
        if ($row[1] == $lugarDeExpedicion) {
            $timeZone = $row[6];
            break;
        }
    }
    fclose($f);
    return $timeZone;
}

//parametros para conexion al Webservice (URL de Pruebas)
$wsdl_url        = 'https://staging.ws.timbox.com.mx/timbrado_cfdi40/wsdl';
$wsdl_usuario    = '';
$wsdl_contrasena = '';
$ruta_xml        = '../xml_ejemplos/ejemplo_cfdi_40.xml';

actualizarSello($ruta_xml);
// convertir la cadena del xml en base64
$documento_xml = file_get_contents($ruta_xml);
$xml_base64    = base64_encode($documento_xml);

//crear un cliente para hacer la petición al WS
$cliente = new SoapClient($wsdl_url, [
    'trace' => 1,
    'use'   => SOAP_LITERAL,
]);

//parametros para llamar la funcion timbrar_cfdi
$parametros = [
    'username' => $wsdl_usuario,
    'password' => $wsdl_contrasena,
    'sxml'     => $xml_base64,
];

try {
    //llamar la funcion timbrar_cfdi
    $respuesta = $cliente->__soapCall('timbrar_cfdi', $parametros);
    echo "\nResponse:\n";
    var_dump($respuesta);
    echo "\n\nCFDI Timbrado:\n";
    //imprimir el contenido del XML timbrado
    echo htmlspecialchars($respuesta->xml);
} catch (Exception $exception) {
    print_r($exception);
    //imprimir los mensajes de la excepcion
    echo '# del error: ' . $exception->getCode() . "\n";
    echo 'Descripción del error: ' . $exception->getMessage() . "\n";
}