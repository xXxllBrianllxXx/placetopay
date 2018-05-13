<?php

function conectar_mysql() {

  global $mysql_conexion;

  $link = mysqli_connect($mysql_conexion['host'],$mysql_conexion['user'], $mysql_conexion['pass'], $mysql_conexion['dbas']);

  if (!$link) {
    echo "No se pudo Conectar :( <br>" . mysqli_connect_errno() . PHP_EOL;
    exit;
  }

  return $link;
}

function cerrar_mysql($link) {
  mysqli_close($link);
}

?>
