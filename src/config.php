<?php

// Base de datos
$mysql_conexion= array(
  'host' => "localhost",
  'user' => "root",
  'pass' => "",
  'dbas' => "placetopay"
);

// Credenciales PlaceToPay
$ident   = '6dd490faf9cb87a9862245da41170ff2';
$tranKey = '024h1IlD';
$WSDL    = 'https://test.placetopay.com/soap/pse/?wsdl';

// Url de retorno.
$returnURL = 'http://localhost/placetopay/index.php';

$seed    = date('c');
$tranKey = sha1($seed.$tranKey);
$additio = array();

// Auth
$auth  = array(
  'login'=> $ident,
  'tranKey'=> $tranKey,
  'seed'=> $seed,
  'additional'=> $additio,
);

// Datos del Usuario
$person = array(
  'document'=> '1047971220',
  'documentType'=> 'CC',
  'firstName'=> 'Brian',
  'lastName'=> 'Rodriguez',
  'company'=> 'PlaceToPay',
  'emailAddress'=> 'brian.alber@hotmail.com',
  'address'=> 'Carrera 87 A # 78 A - 21',
  'city'=> 'Medellin',
  'province'=> 'Antioquia',
  'country'=> 'CO',
  'phone'=> '5895074',
  'mobile'=> '3137398299',
);

?>
