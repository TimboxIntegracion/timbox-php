# PHP
Ejemplo con la integración al Webservice de Timbox

Se deberá hacer uso de las URL que hacen referencia al WSDL, en cada petición realizada:

- [Timbox Pruebas](https://staging.ws.timbox.com.mx/timbrado/wsdl)

- [Timbox Producción](https://sistema.timbox.com.mx/timbrado/wsdl)


##Timbrar CFDI
Para hacer una petición de timbrado de un CFDI, deberá enviar las credenciales asignadas, asi como el xml que desea timbrar convertido a una cadena en base64:
```
$documento_xml = file_get_contents($ruta_xml);
$xml_base64 = base64_encode($documento_xml);
```
Crear un cliente y hacer el llamado al método timbrar_cfdi enviándole los parametros con la información necesaria:

```
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

//llamar el método timbrar
$respuesta = $cliente->__soapCall("timbrar_cfdi", $parametros);
```

##Cancelar CFDI
Para la cancelación son necesarias las credenciales asignadas, RFC del emisor, un arreglo de UUIDs, el archivo PFX convertido a cadena en base64 y el password del archivo PFX:
```
$pfx_path = 'path_del_archivo/iad121214b34.pfx';
$bin_file = file_get_contents($pfx_path);
$pfx_base64 = base64_encode($bin_file);
```
Crear un cliente para hacer la petición de cancelación al webservice:
```
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

//hacer el llamado al método cancelar_cfdi
$respuesta = $cliente->__soapCall("cancelar_cfdi", $parametros);
```

