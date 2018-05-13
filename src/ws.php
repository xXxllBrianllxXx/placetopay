<?php

/***********************************************************************************************************/
/* Declaramos las funciones del WebService que vamos a consumir */
/***********************************************************************************************************/

$options = array(
    'trace' => true,
);

$s = new SoapClient($WSDL, $options);
$s -> __setLocation('https://test.placetopay.com/soap/pse/?wsdl');

/***********************************************************************************************************/
/* Funcion para consultar al WS los Bancos */
/***********************************************************************************************************/
function getBankList() {

  global $s;
  global $auth;

  $param = array(
    'auth' => $auth
  );

  $banks = $s->getBankList($param);

  return $banks;
}
/***********************************************************************************************************/
/***********************************************************************************************************/
//
//
//
//
/***********************************************************************************************************/
/* Funcion que llamamos al momento de realizar la transaccion le pasamos el tipo de persona y el banco
  Esta funcion es la que me retorna la interfaz de PSE o mas o menos asi la entendi xD */
/***********************************************************************************************************/
function createTransaction($bank,$interface) {

  global $s;
  global $auth;
  global $person;
  global $returnURL;

  $referencia  = time();

  $descripcion = 'Prueba'; // Toca mandarle Prueba.

  $PSETransactionRequest = array(
  	'bankCode' => $bank,
  	'bankInterface' => $interface,
  	'returnURL' => $returnURL.'?buscar='.$referencia,
  	'reference' => $referencia,
  	'description' => $descripcion,
  	'language' => 'ES',
  	'currency' => 'COP',
  	'totalAmount' => '1234',
  	'taxAmount' => '123',
  	'devolutionBase' => '1000',
  	'tipAmount' => '0',
  	'payer' => $person,
  	'buyer' => $person,
  	'shipping' => $person,
  	'ipAddress' => $_SERVER['REMOTE_ADDR'],
  	'userAgent' => $_SERVER['HTTP_USER_AGENT'],
  	'additionalData' => array(),
  );

  $param = array(
    'auth'        => $auth,
    'transaction' => $PSETransactionRequest,
  );

  $PSETransactionResponse = $s->createTransaction($param);

  $TransactionResponse    = $PSETransactionResponse->createTransactionResult;

  transaction_save($PSETransactionRequest,$PSETransactionResponse->createTransactionResult);

  return $TransactionResponse;
}
/***********************************************************************************************************/
/***********************************************************************************************************/
//
//
//
//
/***********************************************************************************************************/
/* Funcion que utilizamos para consultar una transaccion especifica
   La usamos para mostrar el detalle del estado de la trasaccion al finalizarla */
/***********************************************************************************************************/
function getTransactionInformation($transactionID) {

  global $s;
  global $auth;

  $param = array(
    'auth'          => $auth,
    'transactionID' => $transactionID
  );

  $TransactionInformation = $s->getTransactionInformation($param);

  return $TransactionInformation;
}
/***********************************************************************************************************/
/***********************************************************************************************************/

?>
