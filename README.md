# PHP
Ejemplo con la integración al Webservice de Timbox

Se deberá hacer uso de las URL que hacen referencia al WSDL, en cada petición realizada:

Webservice de Timbrado:
- [Timbox Pruebas](https://staging.ws.timbox.com.mx/timbrado_cfdi33/wsdl)

- [Timbox Producción](https://sistema.timbox.com.mx/timbrado_cfdi33/wsdl)

Webservice de Cancelación:

- [Timbox Pruebas](https://staging.ws.timbox.com.mx/cancelacion/wsdl)

- [Timbox Producción](https://sistema.timbox.com.mx/cancelacion/wsdl)

## Activar libreria SOAP Client
En caso de tener problemas con la libreria SOAP Client de php, normalmente se debe a que por defecto esta desactivada.

La solución es editar el archivo php.ini generalmente ubicado en **/etc/php.ini**, la solución es la siguiente:

1. En tu consola edita tu archivo php.ini con tu editor preferido, busca la siguiente linea  **;extension=php_soap.dll** y quita el **;** que esta al inicio de la linea

2. Reiniciar el servidor con la siguiente linea:

    **sudo apachectl restart**
    
## Timbrar CFDI
### Generacion de Sello
Para generar el sello se necesita: la llave privada (.key) en formato PEM y el XSLT del SAT (cadenaoriginal_3_3.xslt).El XSLT del SAT se utiliza para poder transformar el XML y obtener la cadena original.

De la cadena original se obtiene el digest y luego se utiliza el digest y la llave privada para obtener el sello. Todo esto se realiza con comandos de OpenSSL.

Finalmente el sello es actualizado en el archivo XML para que pueda ser timbrado. Esto se logra mandando a llamar el método de actualizarSello:
```
actualizarSello($ruta_xml);
```
### Timbrado
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
Para la cancelación son necesarios el certificado y llave, en formato pem que corresponde al emisor del comprobante:
```
$file_cer_pem = file_get_contents("CSD01_AAA010101AAA.cer.pem");
$file_key_pem = file_get_contents("CSD01_AAA010101AAA.key.pem");
```

Crear un cliente para hacer la petición de cancelación al webservice:
```
// Parametros para la conexión al Webservice
$wsdl_url = "https://staging.ws.timbox.com.mx/cancelacion/wsdl";
$wsdl_usuario = "AAA010101000";
$wsdl_contrasena = "h6584D56fVdBbSmmnB";

// Parametros para la cancelación del CFDI
$rfc_emisor = "AAA010101AAA";
$rfc_receptor = "IAD121214B34";
$uuid = "8998F324-4421-47F6-B68E-EC283B1545F4";
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

// Crear un cliente para hacer la petición al WS
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

```

## Consultar Estatus CFDI
Para la consulta de estatus de CFDI solo es necesario crear un cliente para hacer la petición de consulta al webservice:

```
// Parametros para la conexión al Webservice
$wsdl_url = "https://staging.ws.timbox.com.mx/cancelacion/wsdl";
$wsdl_usuario = "AAA010101000";
$wsdl_contrasena = "h6584D56fVdBbSmmnB";

// Parametros para la consulta del CFDI
$rfc_emisor = "AAA010101AAA";
$rfc_receptor = "IAD121214B34";
$uuid  = "56756B66-A0E5-4D90-ACF5-0912DFA354B0";
$total = "7261.60";
//crear un cliente para hacer la petición al WS
$cliente = new SoapClient($wsdl_url);

// Parametros para llamar la funcion consultar_estatus
// Nota: Tener en cuenta el orden de los parametros enviados.
$parametros = array(
    "username" => $wsdl_usuario,
    "password" => $wsdl_contrasena,
    "uuid" => $uuid,
    "rfc_emisor" => $rfc_emisor,
    "rfc_receptor" => $rfc_receptor,
    "total" => $total
);

try {
    // Llamar la funcion consultar_estatus
    $respuesta = $cliente->__soapCall("consultar_estatus", $parametros);
    echo $respuesta->estado ." ". $respuesta->estatus_cancelacion . "\n";
} catch (Exception $exception) {
    // Imprimir los mensajes de la excepcion
    echo "# del error: " . $exception->getCode() . "\n";
    echo "Descripción del error: " . $exception->getMessage() . "\n";
}

```

## Consultar Peticiones Pendientes
Para la consulta de peticiones pendientes son necesarios el certificado y llave, en formato pem que corresponde al emisor del comprobante:
```
$file_cer_pem = file_get_contents("CSD01_AAA010101AAA.cer.pem");
$file_key_pem = file_get_contents("CSD01_AAA010101AAA.key.pem");
```
Crear un cliente para hacer la petición de consultas pendientes al webservice:
```
// Parametros para la conexión al Webservice
$wsdl_url = "https://staging.ws.timbox.com.mx/cancelacion/wsdl";
$wsdl_usuario = "AAA010101000";
$wsdl_contrasena = "h6584D56fVdBbSmmnB";

// Parametros para la cancelación del CFDI
$rfc_emisor = "AAA010101AAA";

$file_cer_pem = file_get_contents("CSD01_AAA010101AAA.cer.pem");
$file_key_pem = file_get_contents("CSD01_AAA010101AAA.key.pem");

// Crear un cliente para hacer la petición al WS
$cliente = new SoapClient($wsdl_url);

// Parametros para llamar la funcion consultar_peticiones_pendientes
// Nota: Tener en cuenta el orden de los parametros enviados.
$parametros = array(
    "username" => $wsdl_usuario,
    "password" => $wsdl_contrasena,
    "rfc_receptor" => $rfc_emisor,
    "cert_pem" => $file_cer_pem,
    "llave_pem" => $file_key_pem,
);


try {
    // Llamar la funcion consultar_peticiones_pendientes
    $respuesta = $cliente->__soapCall("consultar_peticiones_pendientes", $parametros);
    // Imprimir lista de uuids pendientes
    print_r([$respuesta->uuids]);
} catch (Exception $exception) {
    // Imprimir los mensajes de la excepcion
    echo "# del error: " . $exception->getCode() . "\n";
    echo "Descripción del error: " . $exception->getMessage() . "\n";
}

```

## Procesar Respuesta
Para realizar la petición de aceptación/rechazo de la solicitud de cancelación son necesarios el certificado y llave, en formato pem que corresponde al emisor del comprobante:
```
$file_cer_pem = file_get_contents("CSD01_AAA010101AAA.cer.pem");
$file_key_pem = file_get_contents("CSD01_AAA010101AAA.key.pem");
```
Crear un cliente para hacer la petición de aceptación/rechazo al webservice:
```
// Parametros para la conexión al Webservice
$wsdl_url = "https://staging.ws.timbox.com.mx/cancelacion/wsdl";
$wsdl_usuario = "AAA010101000";
$wsdl_contrasena = "h6584D56fVdBbSmmnB";

// Parametros para procesar respuesta del CFDI
$rfc_emisor = "AAA010101AAA";
$rfc_receptor = "AAA010101AAA";
$uuid = "6B95A1A8-7155-470C-A651-620DC081B540";
$total = "7261.60";
$file_cer_pem = file_get_contents("CSD01_AAA010101AAA.cer.pem");
$file_key_pem = file_get_contents("CSD01_AAA010101AAA.key.pem");

// A(Aceptar la solicitud), R(Rechazar la solicitud)
$respuesta_de_solicitud = 'A';

$respuestas = array(
	"folios_respuestas" => array(
		"uuid" => $uuid,
		"rfc_emisor" => $rfc_emisor,
		"total" => $total,
		"respuesta" => $respuesta_de_solicitud
		)
  	   // ... n folios_respuestas a procesar
	);

// Crear un cliente para hacer la petición al WS
$cliente = new SoapClient($wsdl_url);

// Parametros para llamar la funcion procesar_respuesta
// Nota: Tener en cuenta el orden de los parámetros enviados.
$parametros = array(
    "username" => $wsdl_usuario,
    "password" => $wsdl_contrasena,
    "rfc_receptor" => $rfc_receptor,
    "respuestas" => $respuestas,
    "cert_pem" => $file_cer_pem,
    "llave_pem" => $file_key_pem
);

try {
    // Llamar la funcion procesar_respuesta
    $respuesta = $cliente->__soapCall("procesar_respuesta", $parametros);
        // Imprimir lista de folios respuesta
        print_r([$respuesta->folios]);
} catch (Exception $exception) {
    // Imprimir los mensajes de la excepcion
    echo "# del error: " . $exception->getCode() . "\n";
    echo "Descripción del error: " . $exception->getMessage() . "\n";
}
```