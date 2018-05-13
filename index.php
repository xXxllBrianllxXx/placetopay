<?php

require_once('src/config.php');
require_once('db/conexion.php');
require_once('src/controlador.php');
require_once('src/ws.php');

/***********************************************************************************************************/
/* Si vamos a buscar una transaccion puntua, a este punto me retorna al finalizar la transaccion
   Para ver el detalle del resultado o respuesta */
/***********************************************************************************************************/
if(isset($_GET['buscar'])) {
  $html = response($_GET['buscar']);
}
/***********************************************************************************************************/
/* Si queremos ver los registros de todas las transacciones */
/***********************************************************************************************************/
elseif(isset($_GET['registros'])) {
  actualizar_estado_transacciones();
  $html = logpage();
}
/***********************************************************************************************************/
/* Es la pagina de inicio, donde posemos realizar una nueva transaccion */
/***********************************************************************************************************/
else {
  form_submit();
  $html = form_render();
}

require_once('vista.php');

?>
