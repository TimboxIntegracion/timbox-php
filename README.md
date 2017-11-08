# PHP
Ejemplo con la integración al Webservice de Timbox

Se deberá hacer uso de las URL que hacen referencia al WSDL, en cada petición realizada:

- [Timbox Pruebas](https://staging.ws.timbox.com.mx/timbrado_cfdi33/wsdl)

- [Timbox Producción](https://sistema.timbox.com.mx/timbrado_cfdi33/wsdl)

## Activar libreria SOAP Client
En caso de tener problemas con la libreria SOAP Client de php, normalmente se debe a que por defecto esta desactivada.

La solución es editar el archivo php.ini generalmente ubicado en **/etc/php.ini**, la solución es la siguiente:

1. En tu consola edita tu archivo php.ini con tu editor preferido, busca la siguiente linea  **;extension=php_soap.dll** y quita el **;** que esta al inicio de la linea

2. Reiniciar el servidor con la siguiente linea:

    **sudo apachectl restart**
    
### Generacion de Sello
Para generar el sello se necesita: la llave privada (.key) y el certificado (.cer) en formato PEM. También es necesario incluir el XSLT del SAT para obtener transformar el XML a la cadena original.

De la cadena original se obtiene el digest y luego se utiliza el digest y la llave privada para obtener el sello. Todo esto se realiza con comandos de OpenSSL.

Finalmente el sello es actualizado en el archivo XML para que pueda ser timbrado.
## Timbrar CFDI
Para hacer una petición de timbrado de un CFDI, deberá enviar las credenciales asignadas, asi como el xml que desea timbrar convertido a una cadena en base64:
```
$documento_xml = file_get_contents($ruta_xml);
$xml_base64 = base64_encode($documento_xml);
```
Crear un cliente y hacer el llamado al método timbrar_cfdi enviándole los parametros con la información necesaria:

```
//parametros para conexion al Webservice (URL de Pruebas)
$wsdl_url = "https://staging.ws.timbox.com.mx/timbrado_cfdi33/wsdl";
$wsdl_usuario = "AAA010101000";
$wsdl_contrasena = "h6584D56fVdBbSmmnB";
$ruta_xml = "archivoXml.xml";

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
    echo $respuesta->xml;

} catch (Exception $exception) {
    //imprimir los mensajes de la excepcion
    echo "# del error: " . $exception->getCode() . "\n";
    echo "Descripción del error: " . $exception->getMessage() . "\n";
}
```

## Cancelar CFDI
Para la cancelación son necesarias las credenciales asignadas, RFC del emisor, un arreglo de UUIDs, el archivo PFX convertido a cadena en base64 y el password del archivo PFX:
```
$pfx_path = 'archivoPfx.pfx';
$bin_file = file_get_contents($pfx_path);
$pfx_base64 = base64_encode($bin_file);
```
Crear un cliente para hacer la petición de cancelación al webservice:
```
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
```
