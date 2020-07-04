<?php

	session_start();
	echo 'wykonuje';
	
	if (isset($_POST['email']))
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
			echo 'wykonuje';
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
					echo 'wszystko ok';
					if ($polaczenie->query("INSERT INTO users VALUES (NULL, '$nick', '$haslo_hash', '$imie', '$email')"))
					{
						$_SESSION['udanarejestracja']=true;
						header('Location: index.php');
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


  </head>
<body>
<div class="container-fluid bg">
	<div class="row d-flex justify-content-center">

			<div class="col-md-3 col-sm-6 col-xs-12">
				<div class="start-buttons">
					<button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#loginModal"><i class="fas fa-user"></i> Zaloguj</button>
					<button type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#registerModal"><i class="fas fa-caret-square-right"></i> Rejestracja</button>
				</div>

				<?php					
					if (($_POST['wszystko_OK']==true) && isset($_POST['email']))
						echo 'Zostałeś zarejestrowany';
				?>

				<div class="alert alert-primary" role="alert">
					Zostałeś zarejestrowany jako nowy użytkownik. Możesz się teraz zalogować.
				</div>
				
				<!--Logowanie-->
				<div class="modal fade" id="loginModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				  <div class="modal-dialog  form-logowanie">
					<div class="modal-content">					
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">Logowanie użytkownika</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							  <span aria-hidden="true">&times;</span>
							</button>
						 </div>
						<div class="modal-body logowanie">
							<form>
								<div class="text-center">
									<img src="img/user_img/user_img.png" class="rounded user_img" alt="">
								</div>
								<div>
								  <div class="form-group login-form">
									<label for="login">Twój Login:</label>
									<input type="text" class="form-control" id="login" placeholder="Twój Login">			
								  </div>
								  <div class="form-group login-form">
									<label for="exampleInputPassword1">Hasło</label>
									<input type="password" class="form-control" id="exampleInputPassword1" placeholder="Twoje Hasło">
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
						
						<div class="modal-footer">
							<small id="logingHelp" class="form-text text-muted">Problemy z logowaniem?</small>
						  </div>
						</div>
						
					</div>
				  </div>
					  

					
				<!--Rejestracja-->
				<div class="modal fade <?php					
					if (($wszystko_OK==false) && isset($_POST['email']))
						echo 'show d-block';
				?>" id="registerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
				  <div class="modal-dialog form-logowanie">
					<div class="modal-content">
					
						<div class="modal-header">
							<h5 class="modal-title" id="exampleModalLabel">Logowanie użytkownika</h5>
							<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							  <span aria-hidden="true">&times;</span>
							</button>
						 </div>
						<div class="modal-body rejestracja">					
							<form method="post">
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
											echo '<div class="error">'.$_SESSION['e_nick'].'</div>';
											unset($_SESSION['e_nick']);
										}
									?>
									<small id="register_loginHelp" class="form-text text-muted">Login powinien składać się conajmniej z 6 liter.</small>								
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
											echo '<div class="error">'.$_SESSION['e_haslo1'].'</div>';
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
											echo '<div class="error">'.$_SESSION['e_haslo2'].'</div>';
											unset($_SESSION['e_haslo2']);
										}
									?>
								  </div>
								  
								 <!--Imię-->
								 <div class="form-group">
									<label for="register_name">Imię:</label>
									<input type="text" class="form-control" name="imie" id="register_name" value="<?php
										if (isset($_SESSION['fr_email']))
										{
											echo $_SESSION['fr_email'];
											unset($_SESSION['fr_email']);
										}
									?>" placeholder="Twoje Imię">
									<?php
										if (isset($_SESSION['e_imie']))
										{
											echo '<div class="error">'.$_SESSION['e_imie'].'</div>';
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
											echo '<div class="error">'.$_SESSION['e_email'].'</div>';
											unset($_SESSION['e_email']);
										}
									?>					
								  </div>
								 </div>
								  <button type="submit" class="btn btn-primary btn-block"><i class="fas fa-sign-in-alt"></i> Zarejestruj</button>
								  <button type="button" class="btn btn-secondary btn-block" data-dismiss="modal">Anuluj</button>
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
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
</body>
</html>