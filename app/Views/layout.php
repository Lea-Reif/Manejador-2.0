<!DOCTYPE HTML>
<html lang="es">

<head>
	<meta charset="utf-8">
	<title>Manejador</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<link rel="stylesheet" href="<?php echo base_url('/css/bootstrap.min.css');?>"/>

	<link rel="stylesheet" type="text/css" href="<?php echo base_url('/DataTables/datatables.min.css');?>" />
	<script src="<?php echo base_url('/js/jquery-3.4.1.min.js');?>"></script>
	<script src="<?php echo base_url('/js/popper.min.js');?>"></script></script>
	<script src="<?php echo base_url('/js/bootstrap.min.js');?>"></script>

	<script src="<?php echo base_url('/js/bootstrap-select.min.js');?>"></script>
	<script type="text/javascript" src="<?php echo base_url('/DataTables/datatables.min.js');?>"></script>

	<style>
	body {
		background-color: lightslategray;
	}
	</style>
	<link rel="stylesheet" href="<?php echo base_url('/css/bootstrap-select.min.css');?>"/>

	<!-- Latest compiled and minified JavaScript -->
</head>

<body>
	<header>
		<h1 align="center">Manejador de Base de datos <?php if(session()->get('loggedIn')): ?> <a href="<?php echo base_url('/logout');?>" class=" btn btn-danger" >Salir</a> <?php endif;?>
</h1>
	</header>

	<div class="container">

		<?php $this->renderSection('content') ?>
	</div>


</body>

</html>