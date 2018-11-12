<?php

// Parametros para la conexión al Webservice
$wsdl_url = "https://staging.ws.timbox.com.mx/cancelacion/wsdl";
$wsdl_usuario = "AAA010101000";
$wsdl_contrasena = "h6584D56fVdBbSmmnB";

// Parametros para procesar respuesta del CFDI
$rfc_emisor = "AAA010101AAA";
$rfc_receptor = "AAA010101AAA";
$uuid = "D6B0CDE5-0E45-4049-8B67-E5D5B08ACFC9";
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