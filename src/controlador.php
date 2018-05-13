<?php

/***********************************************************************************************************/
/* Cosultamos y actualizamos la tabla de los Bancos */
/***********************************************************************************************************/
function getBanks() {

  $banks = array();
  $update = false;
  $now = time();
  $day = 86400; // Segundos por dia.
  $lim = $now - $day;// Límite inferior

  $link = conectar_mysql();

  $sql  = "select value as last_banks_cache
           from variables
           where name = 'last_banks_cache'; ";

  $res = mysqli_query($link, $sql);

  if($res) {

  	if(mysqli_num_rows($res) === 0)  {
  	  $sql = "insert into variables values('last_banks_cache',UNIX_TIMESTAMP())";
	    mysqli_query($link,$sql);
      $update = true;
    }
    else  {
      $row = mysqli_fetch_assoc($res);
      if($row['last_banks_cache'] < $lim) {
  	    $sql = "update variables set value = UNIX_TIMESTAMP() where name = 'last_banks_cache'";
        $update = true;
      }
    }
  }

  if($update == true) {
  /* Borramos lo de la tabla Bancos */
	$sql = "truncate table bancos";
	mysqli_query($link,$sql);
	/* Insertamos los valores actuales */
    $banklist = getBankList();
    foreach($banklist->getBankListResult->item as $item) {
  	  $sql = "insert into bancos values(".$item->bankCode.",'".$item->bankName."')";
	  $x = mysqli_query($link,$sql);
    }
  }

  /* Consultamos los bancos para pintarlos */
  $sql = "select * from bancos";
  $res = mysqli_query($link, $sql);

  while ($row = mysqli_fetch_row($res)) {
    $banks[$row[0]] = $row[1];
  }

  cerrar_mysql($link);

  return $banks;
}
/***********************************************************************************************************/
/***********************************************************************************************************/
//
//
//
//
/***********************************************************************************************************/
/* Creamos la interfaz del Select de Bancos */
/***********************************************************************************************************/
function renderSeleccionarBanco() {

  $options = "<option value='0'>Seleccione Banco</option>";
  $banks   = getBanks();

  foreach($banks as $code=>$bank) {
  	$options .= "<option value='".$code."'>".$bank."</option>";
  }
  $select = "<select id='bank' name='bank' class='selectpicker' data-live-search='true' data-live-search-placeholder='Search' tabindex='-98'>".$options."</select><br><br>";

  return $select;
}
/***********************************************************************************************************/
/***********************************************************************************************************/
//
//
//
//
/***********************************************************************************************************/
/* Creamos la interfaz del Formulario completo que contentra los Select y el Submit */
/***********************************************************************************************************/
function form_render() {
  $html = "
    <form name='placetopay' action='index.php' method='POST'>
      <p>Tipo de persona:</p>
      <select name='tipo' class='selectpicker' data-live-search='true' data-live-search-placeholder='Search' tabindex='-98'>
	    <option value='0'>Natural</option>
	    <option value='1'>Jurídica</option>
	  </select><br><br>
    <p>Banco:</p>"
    .renderSeleccionarBanco()
    ." <button name='step1' type='submit' class='form-input--submit js-form-submit'>
      <i class='hidden fa fa-refresh' aria-hidden='true'></i>
      <i class='fa fa-arrow-circle-right' aria-hidden='true'></i>
    </button>
    </form>";
   return $html;
}
/***********************************************************************************************************/
/***********************************************************************************************************/
//
//
//
//
/***********************************************************************************************************/
/* Accion del Submit llamamos la funcion createTransaction() */
/***********************************************************************************************************/
function form_submit() {

  if(isset($_POST['step1'])) {

  	$TransactionResponse = createTransaction($_POST['bank'],$_POST['tipo']);

  	if($TransactionResponse->returnCode == "SUCCESS") {
  	  redireccionar_set($TransactionResponse->bankURL);
  	}
  	else {
  	  global $html;
  	  $html .= "<p>No se logro conectar!!!</p>";
  	}
  }
}
/***********************************************************************************************************/
/***********************************************************************************************************/
//
//
//
//
/***********************************************************************************************************/
/* Guardamos el estado de la transaccion en la base de datos */
/***********************************************************************************************************/
function transaction_save($req,$res) {

  $link = conectar_mysql();

  $sql  = "insert into transacciones (
             transactionID,
             sessionID,
             returnCode,
             trazabilityCode,
             transactionCycle,
             bankCurrency,
             bankFactor,
             bankURL,
             responseCode,
             responseReasonCode,
             responseReasonText,
             bankCode,
             bankInterface,
             reference,
             description,
             totalAmount
             ) values(?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

  $stmt = mysqli_prepare ($link, $sql);

  mysqli_stmt_bind_param(
  	$stmt,
  	"isssisdsissssssd",
  	$res->transactionID,
  	$res->sessionID,
  	$res->returnCode,
  	$res->trazabilityCode,
  	$res->transactionCycle,
  	$res->bankCurrency,
  	$res->bankFactor,
  	$res->bankURL,
  	$res->responseCode,
  	$res->responseReasonCode,
  	$res->responseReasonText,
  	$req['bankCode'],
  	$req['bankInterface'],
  	$req['reference'],
  	$req['description'],
  	$req['totalAmount']
  );

  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  cerrar_mysql($link);
}
/***********************************************************************************************************/
/***********************************************************************************************************/
//
//
//
//
/***********************************************************************************************************/
/* Guardamos la URL a la que debemos redireccionar */
/***********************************************************************************************************/
function redireccionar_set($url) {
  global $redireccionar;
  $redireccionar = $url;
}
/***********************************************************************************************************/
/***********************************************************************************************************/
//
//
//
//
/***********************************************************************************************************/
/* Confirma si se debe redirecionar y retorna el redireccionamiento */
/***********************************************************************************************************/
function redireccionar() {
  global $redireccionar;
  if($redireccionar) {
  	return "<meta http-equiv=\"refresh\" content=\"0;URL='$redireccionar'\" />";
  }
  return "";
}
/***********************************************************************************************************/
/***********************************************************************************************************/
//
//
//
//
/***********************************************************************************************************/
/* Consumimos el servicio getTransactionInformation para consultar una transaccion puntual */
/***********************************************************************************************************/
function response($ref) {

  // Obtener transactionID
  $link = conectar_mysql();
  $transactionID = false;

  $sql  = "select transactionID
           from transacciones
           where reference = ?;";

  $stmt = mysqli_prepare($link, $sql);

  mysqli_stmt_bind_param($stmt,"s",$ref);
  mysqli_stmt_execute($stmt);
  mysqli_stmt_bind_result($stmt, $transactionID);
  mysqli_stmt_fetch($stmt);
  mysqli_stmt_close($stmt);

  cerrar_mysql($link);

  $TransactionInformation = getTransactionInformation($transactionID);

  $html = "
      <table>
        <thead>
          <tr>
            <th># Transaccion</th>
            <th>Referencia</th>
            <th>Fecha Transaccion</th>
            <th>Retorno</th>
            <th>Estado Transaccion</th>
            <th>Respuesta</th>
          </tr>
        </thead>
        <tbody>";

  for ($i=0; $i < 1 ; $i++) {
    $html .= "<tr>";
    $html .= "<td>".$TransactionInformation->getTransactionInformationResult->transactionID."</td>";
    $html .= "<td>".$TransactionInformation->getTransactionInformationResult->reference."</td>";
    $html .= "<td>".$TransactionInformation->getTransactionInformationResult->requestDate."</td>";
    $html .= "<td>".$TransactionInformation->getTransactionInformationResult->returnCode."</td>";
    $html .= "<td>".$TransactionInformation->getTransactionInformationResult->transactionState."</td>";
    $html .= "<td>".$TransactionInformation->getTransactionInformationResult->responseReasonText."</td>";
    $html .= "</tr>";
  }

  $html .= "</tbody></table>";

  transaction_update($TransactionInformation->getTransactionInformationResult);

  return $html;
}
/***********************************************************************************************************/
/***********************************************************************************************************/
//
//
//
//
/***********************************************************************************************************/
/* Actualizamos los datos de la transaccion puntual antes consultada */
/***********************************************************************************************************/
function transaction_update($res) {

  $link = conectar_mysql();

  $sql  = "update transacciones set
             requestDate = ?,
             bankProcessDate = ?,
             trazabilityCode = ?,
             transactionCycle = ?,
             transactionState = ?,
             responseCode = ?,
             responseReasonCode = ?,
             responseReasonText =  ?
          where transactionID = ?";

  $stmt = mysqli_prepare ($link, $sql);

  mysqli_stmt_bind_param(
  	$stmt,
  	"sssisissi",
  	$res->requestDate,
  	$res->bankProcessDate,
  	$res->trazabilityCode,
  	$res->transactionCycle,
  	$res->transactionState,
  	$res->responseCode,
  	$res->responseReasonCode,
  	$res->responseReasonText,
  	$res->transactionID
  );

  mysqli_stmt_execute($stmt);
  mysqli_stmt_close($stmt);

  cerrar_mysql($link);
}
/***********************************************************************************************************/
/***********************************************************************************************************/
//
//
//
//
/***********************************************************************************************************/
/* Creamos la interfaz de la seccion de Registros para ver todas las transacciones realizadas */
/***********************************************************************************************************/
function logpage() {

  $link = conectar_mysql();

  $sql = "select * from transacciones order by idt DESC";

  $res = mysqli_query($link, $sql);

  $html = "
      <table>
        <thead>
          <tr>
            <th>Fecha Respuesta</th>
            <th>Estaro Retorno</th>
            <th>Etado Transaccion</th>
            <th>Cod Respuesta</th>
            <th>Cod Reason</th>
            <th>Respuesta</th>
            <th>Descripcion</th>
            <th>Monto</th>
          </tr>
        </thead>
        <tbody>";

  while ($row = mysqli_fetch_object($res)) {
    $html .= "<tr>";
    $html .= "<td>".$row->requestDate."</td>";
    $html .= "<td>".$row->returnCode."</td>";
    $html .= "<td>".$row->transactionState."</td>";
    $html .= "<td>".$row->responseCode."</td>";
    $html .= "<td>".$row->responseReasonCode."</td>";
    $html .= "<td>".$row->responseReasonText."</td>";
    $html .= "<td>".$row->description."</td>";
    $html .= "<td>".$row->totalAmount."</td>";
    $html .= "</tr>";
  }

  $html .= "</tbody></table>";

  cerrar_mysql($link);
  return $html;
}
/***********************************************************************************************************/
/***********************************************************************************************************/
//
//
//
//
/***********************************************************************************************************/
/* Actualizamos el estado de las transacciones que se encuentran PENDING */
/***********************************************************************************************************/
function   actualizar_estado_transacciones() {

  $link = conectar_mysql();

  $sql = "select transactionID from transacciones where transactionState = 'PENDING';";
  $res = mysqli_query($link, $sql);
  print mysqli_error($link);

  while ($row = mysqli_fetch_object($res)) {
  	$TransactionInformation = getTransactionInformation($row->transactionID);
    transaction_update($TransactionInformation->getTransactionInformationResult);
  }

  cerrar_mysql($link);
}
/***********************************************************************************************************/
/***********************************************************************************************************/

?>
