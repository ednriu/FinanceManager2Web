<?php

	session_start();
	
	if (!isset($_SESSION['udanarejestracja']))
	{
		header('Location: index.php');
		exit();
	}
	else
	{
		unset($_SESSION['udanarejestracja']);
	}
	
	//Usuwanie zmiennych pamiętających wartości wpisane do formularza
	if (isset($_SESSION['imie'])) unset($_SESSION['imie']);
	if (isset($_SESSION['fr_nick'])) unset($_SESSION['fr_nick']);
	if (isset($_SESSION['fr_email'])) unset($_SESSION['fr_email']);
	if (isset($_SESSION['fr_haslo1'])) unset($_SESSION['fr_haslo1']);
	if (isset($_SESSION['fr_haslo2'])) unset($_SESSION['fr_haslo2']);
	if (isset($_SESSION['fr_regulamin'])) unset($_SESSION['fr_regulamin']);
	
	//Usuwanie błędów rejestracji
	if (isset($_SESSION['e_imie'])) unset($_SESSION['e_imie']);
	if (isset($_SESSION['e_nick'])) unset($_SESSION['e_nick']);
	if (isset($_SESSION['e_email'])) unset($_SESSION['e_email']);
	if (isset($_SESSION['e_haslo'])) unset($_SESSION['e_haslo']);
	if (isset($_SESSION['e_regulamin'])) unset($_SESSION['e_regulamin']);
	if (isset($_SESSION['e_bot'])) unset($_SESSION['e_bot']);
	
?>

<!DOCTYPE HTML>
<html lang="pl">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
	<title>Osadnicy - gra przeglądarkowa</title>
	
	<!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="bootstrap-4.0.0-dist/css/bootstrap.min.css">
	<!-- FontAwesome CSS -->
	<script src="https://kit.fontawesome.com/9427ffaa84.js" crossorigin="anonymous"></script>
	<!-- Global CSS -->
	<link rel="stylesheet" type="text/css" href="index_global.css">
</head>

<body>
	
<div class="container-fluid bg">
	<div class="row d-flex justify-content-center">

			<div class="col-md-3 col-sm-6 col-xs-12">
				<div class="start-buttons">
					<div class="alert alert-success justify-content-center d-flex" role="alert">
					  Dziękujemy za rejestrację w serwisie! Możesz już zalogować się na swoje konto!<br /><br />
	
					<a href="index.php">Powrót do Strony głównej</a>
					</div>		
				</div>
			</div>
	</div>
</div>				
	

</body>
</html>