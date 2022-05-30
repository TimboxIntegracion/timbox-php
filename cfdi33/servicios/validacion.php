<?php

// Parametros para la conexión al Webservice
$wsdl_url        = 'https://staging.ws.timbox.com.mx/valida_cfdi/wsdl';
$wsdl_usuario    = '';
$wsdl_contrasena = '';

// Parametros para la validacion del CFDI
$file_xml     = file_get_contents('../xml_ejemplos/ejemplo_cfdi_33.xml');
$xml          = base64_encode($file_xml);
$comprobantes = [
    [
        'sxml'        => $xml,
        'external_id' => 1,
    ],
    [
        'sxml'        => $xml,
        'external_id' => 2,
    ],
    // ... n comprobantes a validar
];

//  Crear un cliente para hacer la petición al WS
$cliente = new SoapClient($wsdl_url, ['trace' => 1, 'use' => SOAP_LITERAL]);

// Parametros para llamar la funcion validar_cfdi
// Nota: Tener en cuenta el orden de los parametros enviados.
$parametros = [
    'username' => $wsdl_usuario,
    'password' => $wsdl_contrasena,
    'validar'  => $comprobantes,
];

try {
    // Llamar la funcion validar_cfdi
    $respuesta = $cliente->__soapCall('validar_cfdi', $parametros);
    print_r([$respuesta->resultados]);
} catch (Exception $exception) {
    // Imprimir los mensajes de la excepcion
    echo '# del error: ' . $exception->getCode() . "\n";
    echo 'Descripción del error: ' . $exception->getMessage() . "\n";
}
