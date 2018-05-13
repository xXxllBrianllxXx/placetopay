<html>

<head>

	<?php print redireccionar(); ?>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>PlaceToPay</title>
  <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css'>
  <link rel='stylesheet prefetch' href='https://maxcdn.bootstrapcdn.com/font-awesome/4.6.2/css/font-awesome.min.css'>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/normalize/5.0.0/normalize.min.css">
	<link rel="stylesheet" type="text/css" href="css/index.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

</head>

<body>

	<header>
	  <img src="img/logo.png" alt="">
    <br><br><br>
		<nav>
			<ul class="menu">
			  <li><a href="index.php">Inicio</a></li>
				<li><a href="index.php?registros">Registros</a></li>
			</ul>
		</nav>
    <br><br><br>
	</header>

	<section>

		<?php print $html; ?>

	</section>

  <script src='https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js'></script>
  <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js'></script>
  <script src='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js'></script>
  <script src="js/index.js"></script>

</body>

</html>
