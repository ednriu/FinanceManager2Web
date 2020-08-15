<?php

	session_start();
	
	//Czesc Logowania
	
	if ((isset($_SESSION['zalogowany'])) && ($_SESSION['zalogowany']==true))
	{
		header('Location: report.php');
		exit();
	}
	
	//Czesc Rejestracji
	if (isset($_POST['email']) && !isset($_SESSION['udanarejestracja']))
	{
		//Udana walidacja? Załóżmy, że tak!
		$wszystko_OK=true;
		
		//Sprawdź poprawność nickname'a
		$nick = $_POST['nick'];
		
		//Sprawdzenie długości nicka
		if ((strlen($nick)<3) || (strlen($nick)>20))
		{
			$wszystko_OK=false;
			$_SESSION['e_nick']="Nick musi posiadać od 3 do 20 znaków!";
		}
		
		if (ctype_alnum($nick)==false)
		{
			$wszystko_OK=false;
			$_SESSION['e_nick']="Nick może składać się tylko z liter i cyfr (bez polskich znaków)";
		}
		
		// Sprawdź poprawność adresu email
		$email = $_POST['email'];
		$emailB = filter_var($email, FILTER_SANITIZE_EMAIL);
		
		if ((filter_var($emailB, FILTER_VALIDATE_EMAIL)==false) || ($emailB!=$email))
		{
			$wszystko_OK=false;
			$_SESSION['e_email']="Podaj poprawny adres e-mail!";
		}
		
		//Sprawdź poprawność hasła
		$haslo1 = $_POST['haslo1'];
		$haslo2 = $_POST['haslo2'];
		
		if ((strlen($haslo1)<8) || (strlen($haslo1)>20))
		{
			$wszystko_OK=false;
			$_SESSION['e_haslo']="Hasło musi posiadać od 8 do 20 znaków!";
		}
		
		if ($haslo1!=$haslo2)
		{
			$wszystko_OK=false;
			$_SESSION['e_haslo']="Podane hasła nie są identyczne!";
		}	

		$haslo_hash = password_hash($haslo1, PASSWORD_DEFAULT);
		
		//sprawdza poprawność imienia
		$imie = $_POST['imie'];
		
		if (strlen($imie)<3)
		{
			$wszystko_OK=false;
			$_SESSION['e_imie']="Imie musi być dłuższe od 2 znaków";
		}
		
		//Sprawdzanie Recaptchy
		$sekret = "6LdEmLAZAAAAABGVjHf3l3LGJBaVxHZeN2olnPw4";		
		$sprawdz = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$sekret.'&response='.$_POST['g-recaptcha-response']);		
		$odpowiedz = json_decode($sprawdz);		
		if ($odpowiedz->success==false)
		{
			$wszystko_OK=false;
			$_SESSION['e_bot']="Potwierdź, że nie jesteś botem!";
		}
		
		
		
		//Zapamiętaj wprowadzone dane
		$_SESSION['fr_nick'] = $nick;
		$_SESSION['fr_email'] = $email;
		$_SESSION['fr_haslo1'] = $haslo1;
		$_SESSION['fr_haslo2'] = $haslo2;
		$_SESSION['fr_imie'] = $imie;
		if (isset($_POST['regulamin'])) $_SESSION['fr_regulamin'] = true;
		
		require_once "connect.php";
		mysqli_report(MYSQLI_REPORT_STRICT);
		
		try 
		{
			$polaczenie = new mysqli($host, $db_user, $db_password, $db_name);
			if ($polaczenie->connect_errno!=0)
			{
				throw new Exception(mysqli_connect_errno());
			}
			else
			{
				//Czy email już istnieje?
				$rezultat = $polaczenie->query("SELECT user_id FROM users WHERE mail='$email'");
				
				if (!$rezultat) throw new Exception($polaczenie->error);
				
				$ile_takich_maili = $rezultat->num_rows;
				if($ile_takich_maili>0)
				{
					$wszystko_OK=false;
					$_SESSION['e_email']="Istnieje już konto przypisane do tego adresu e-mail!";
				}		

				//Czy nick jest już zarezerwowany?
				$rezultat = $polaczenie->query("SELECT user_id FROM users WHERE login='$nick'");
				
				if (!$rezultat) throw new Exception($polaczenie->error);
				
				$ile_takich_nickow = $rezultat->num_rows;
				if($ile_takich_nickow>0)
				{
					$wszystko_OK=false;
					$_SESSION['e_nick']="Istnieje już gracz o takim nicku! Wybierz inny.";
				}
				
				if ($wszystko_OK==true)
				{
					//Hurra, wszystkie testy zaliczone, dodajemy gracza do bazy
					if ($polaczenie->query("INSERT INTO users VALUES (NULL, '$nick', '$haslo_hash', '$imie', '$email')"))
					{
						$_SESSION['udanarejestracja'] = $wszystko_OK;
						$_SESSION['wszystko_OK'] = $wszystko_OK;
						unset($_POST['email']);
						header('Location: witamy.php');
					}
					else
					{
						throw new Exception($polaczenie->error);
					}

					
				}
				
				$polaczenie->close();
			}
			
		}
		catch(Exception $e)
		{
			echo '<span style="color:red;">Błąd serwera! Przepraszamy za niedogodności i prosimy o rejestrację w innym terminie!</span>';
			echo '<br />Informacja developerska: '.$e;
		}
		
	}
	
	
?>

<!doctype html>
<html lang="pl">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">	
	<meta name="description" content="Aplikacja do zarządzania finansami">
	<meta name="keywords" content="pieniądze, gospodarność, oszczędności">
	<meta name="author" content="Andrzej Konicki">		
	<meta http-equiv="X-Ua-Compatible" content="IE=edge">
	<title>Panel Logowania</title>
	

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" type="text/css" href="bootstrap-4.0.0-dist/css/bootstrap.min.css">
	<!-- FontAwesome CSS -->
	<script src="https://kit.fontawesome.com/9427ffaa84.js" crossorigin="anonymous"></script>
	<!-- Global CSS -->
	<link rel="stylesheet" type="text/css" href="index_global.css">
	<!-- Recaptcha -->
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<script>
		var hideInProgress = false;
		var showModalId = '';

		function showModal(elementId) {
			if (hideInProgress) {
				showModalId = elementId;
			} else {
				$("#" + elementId).modal("show");
			}
		};

		function hideModal(elementId) {
			hideInProgress = true;
			$("#" + elementId).on('hidden.bs.modal', hideCompleted);
			$("#" + elementId).modal("hide");

			function hideCompleted() {
				hideInProgress = false;
				if (showModalId) {
					showModal(showModalId);
				}
				showModalId = '';
				$("#" + elementId).off('hidden.bs.modal');
			}
		};
	</script>
	
	<?php
					if (($_SESSION['blad'] == true) && isset($_SESSION['blad'])){
						echo "<script>alertModal();</script>";
					}
	?>
	
  </head>
<body>
<div class="container-fluid bg">
	<div class="row d-flex justify-content-center">

			<div class="col-md-3 col-sm-6 col-xs-12">
				<div class="start-buttons">
					<button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#loginModal"><i class="fas fa-user"></i> Zaloguj</button>
					<button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#registerModal"><i class="fas fa-caret-square-right"></i> Rejestracja</button>
				</div>	

			
				<!--Modal Logowanie-->
				<div class="modal" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				  <div class="modal-dialog  form-logowanie">
					<div class="modal-content">					
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">Logowanie użytkownika</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							  <span aria-hidden="true">&times;</span>
							</button>
						 </div>
						 
						<div class="modal-body logowanie">
							<form action="zaloguj.php" method="post">
								<div class="text-center">
									<img src="img/user_img/user_img.png" class="rounded user_img" alt="">
								</div>
								<div>
								  <div class="form-group login-form">
									<label for="login">Twój Login:</label>
									<input type="text" class="form-control" name="login" id="login" placeholder="Twój Login">			
								  </div>
								  <div class="form-group login-form">
									<label for="haslo">Hasło</label>
									<input type="password" class="form-control" name="haslo" id="haslo" placeholder="Twoje Hasło">
									<small id="emailHelp" class="form-text text-muted">Rekomendujemy wpisywać hasło zasłonięte gwiazdkami.</small>
								  </div>
								  <div class="form-group login-form form-check">
									<input type="checkbox" class="form-check-input" id="showPassword">
									<label class="form-check-label" for="showPassword">Pokaż Hasło</label>
								  </div>
								 </div>		 

								  <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i> Zaloguj</button>
								  <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Anuluj</button>
							</form>
						</div>
						
						<?php
							if(isset($_SESSION['blad']))	echo $_SESSION['blad'];
						?>
						
						<div class="modal-footer">
							<small id="logingHelp" class="form-text text-muted">Problemy z logowaniem?</small>
						  </div>
						</div>
						
					</div>
				  </div>

				<!--Modal Rejestracja-->
				<div class="modal fade <?php
					if (($wszystko_OK == false) && isset($_POST['email']))
						echo 'show d-block';
				?>" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				  <div class="modal-dialog form-logowanie">
					<div class="modal-content">					
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">Rejestracja użytkownika</h5>
							<button type="button" class="close" onclick = "$('.modal').removeClass('show d-block').addClass('fade');" data-dismiss="modal" aria-label="Close">
							  <span aria-hidden="true">&times;</span>
							</button>
						 </div>
						<div class="modal-body rejestracja">					
							<form method="post" id='formularz_rej'>
								<div>
								
								<!--Login-->
								  <div class="form-group">
									<label for="register_login">Login:</label>
									<input type="text" class="form-control" name="nick" id="register_login" value="<?php
										if (isset($_SESSION['fr_nick']))
										{
											echo $_SESSION['fr_nick'];
											unset($_SESSION['fr_nick']);
										}
										?>" placeholder="Twój Login">
									<?php
										if (isset($_SESSION['e_nick']))
										{											
											echo '<div class="alert alert-warning mt-1" role="alert"><small>'.$_SESSION['e_nick'].'</small></div>';
											unset($_SESSION['e_nick']);
										}
									?>								
								  </div>
								  
								  <!--Hasło1-->
								  <div class="form-group">
									<label for="reg_exampleInputPassword1">Hasło:</label>
									<input type="password" class="form-control" name="haslo1" id="reg_exampleInputPassword1" value="<?php
										if (isset($_SESSION['fr_haslo1']))
										{
											echo $_SESSION['fr_haslo1'];
											unset($_SESSION['fr_haslo1']);
										}
									?>"placeholder="Twoje Hasło">
									<?php
										if (isset($_SESSION['e_haslo1']))
										{
											echo '<div class="alert alert-warning mt-1" role="alert"><small>'.$_SESSION['e_haslo1'].'</small></div>';
											unset($_SESSION['e_haslo1']);
										}
									?>
									<small id="register_passwordHelp" class="form-text text-muted text-justify">Hasło powinno składać się conajmniej z 6 znaków, powinno zawierać conajmniej jedną wielka literę oraz cyfrę.</small>
								  </div>
								  
								  <!--Hasło2-->
								  <div class="form-group">
									<label for="reg_exampleInputPassword2">Powtórz Hasło:</label>
									<input type="password" class="form-control" name="haslo2" id="reg_exampleInputPassword2" value="<?php
										if (isset($_SESSION['fr_haslo2']))
										{
											echo $_SESSION['fr_haslo2'];
											unset($_SESSION['fr_haslo2']);
										}
									?>"placeholder="Twoje Hasło">
									
									<?php
										if (isset($_SESSION['e_haslo2']))
										{
											echo '<div class="alert alert-warning mt-1" role="alert"><small>'.$_SESSION['e_haslo2'].'</small></div>';
											unset($_SESSION['e_haslo2']);
										}
									?>
								  </div>
								  
								 <!--Imię-->
								 <div class="form-group">
									<label for="register_name">Imię:</label>
									<input type="text" class="form-control" name="imie" id="register_name" value="<?php
										if (isset($_SESSION['fr_imie']))
										{
											echo $_SESSION['fr_imie'];
											unset($_SESSION['fr_imie']);
										}
									?>" placeholder="Twoje Imię">
									<?php
										if (isset($_SESSION['e_imie']))
										{
											echo '<div class="alert alert-warning mt-1" role="alert"><small>'.$_SESSION['e_imie'].'</small></div>';
											unset($_SESSION['e_imie']);
										}
									?>									
								  </div>
								  
								  <!--Email-->
								  <div class="form-group">
									<label for="register_email">Email:</label>
									<input type="text" class="form-control" name="email" id="register_email" value="<?php
										if (isset($_SESSION['fr_email']))
										{
											echo $_SESSION['fr_email'];
											unset($_SESSION['fr_email']);
										}
									?>" placeholder="Adres poczty elektronicznej">				
									<?php
										if (isset($_SESSION['e_email']))
										{
											echo '<div class="alert alert-warning mt-1" role="alert"><small>'.$_SESSION['e_email'].'</small></div>';
											unset($_SESSION['e_email']);
										}
									?>					
								  </div>
								  <!--Recaptcha-->
								  <div class="form=group"><div class="g-recaptcha d-flex" data-sitekey="6LdEmLAZAAAAAE9dRZIUDMJkNs3Avgm7C6LCBm3z"></div></div>
								  	<?php
										if (isset($_SESSION['e_bot']))
										{
											echo '<div class="error">'.$_SESSION['e_bot'].'</div>';
											unset($_SESSION['e_bot']);
										}
									?>
								</div>
	
								
								  <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i> Zarejestruj</button>
								  <button type="button" class="btn btn-secondary btn-block" onclick = "$('.modal').removeClass('show d-block').addClass('fade');" data-dismiss="modal">Anuluj</button>
							</form>
						</div>
						</div>			
					</div>		
				</div>
		</div>
	</div>
</div>




<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
</body>
</html>