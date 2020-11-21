<!DOCTYPE HTML>
<html lang="es">

<head>
	<meta charset="utf-8">
	<title>Manejador</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" href="/css/bootstrap.min.css">

	<link rel="stylesheet" type="text/css" href="/DataTables/datatables.min.css" />
	<script src="/js/jquery-3.4.1.min.js"></script>
	<script src="/js/popper.min.js"></script>
	<script src="/js/bootstrap.min.js"></script>

	<script src="/js/bootstrap-select.min.js"></script>
	<script type="text/javascript" src="/DataTables/datatables.min.js"></script>


	<link rel="stylesheet" href="/css/bootstrap-select.min.css">

	<!-- Latest compiled and minified JavaScript -->
</head>

<body>
	<header>
		<h1 align="center">Manejador de Base de datos <?php if(session()->get('loggedIn')): ?> <a href="/logout" class=" btn btn-danger" >Salir</a> <?php endif;?>
</h1>
	</header>

	<div class="container">

		<?php $this->renderSection('content') ?>
	</div>


</body>

</html>