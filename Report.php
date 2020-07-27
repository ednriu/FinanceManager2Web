<?php
	session_start();
	error_reporting(E_ALL);
	
	require_once "connect.php";
	$polaczenie = @new mysqli($host, $db_user, $db_password, $db_name);
	
	if ($polaczenie->connect_errno!=0)
	{
		echo "Error: ".$polaczenie->connect_errno;
	}
	else
	{
		//pobieranie kategorii wydatków do wypełnienia pola Select w formularzu
		$exp_cat = $polaczenie->query('SELECT cat_id,cat_name FROM expence_categories');
		$exp_cat_names = array();   //array for category names
		While ($categories_list = $exp_cat->fetch_assoc()) {
			//Add newest 'cat_name' to the array
			$exp_cat_names[] = $categories_list['cat_name'];
		}
		
		//pobieranie kategorii wpływów do wypełnienia pola Select w formularzu
		$inc_cat = $polaczenie->query('SELECT cat_id,cat_name FROM income_categories');
		$inc_cat_names = array();   //array for category names
		While ($categories_list = $inc_cat->fetch_assoc()) {
			//Add newest 'cat_name' to the array
			$inc_cat_names[] = $categories_list['cat_name'];
		}
				
		//przetwarzanie formularza dodawania wydatków
		if (isset($_POST['expenceAmmount']))
		{
			$wszystko_OK = true;
			$expenceAmmount = $_POST['expenceAmmount'];
			if ($expenceAmmount==0){
				$wszystko_OK = false;
				$_SESSION['e_expenceAmmount'] = 'wpisz liczbę różną od 0';
			}
			$expenceDate = $_POST['expenceDatePicker'];
			if ($expenceDate==NULL){
				$wszystko_OK = false;
				$_SESSION['e_expenceDate'] = 'nie wybrano daty';
			}			
			$expenceCategory = $_POST['kategoriaInput'];
			if ($expenceCategory==NULL){
				$wszystko_OK = false;
				$_SESSION['e_expenceCategory'] = 'nie wybrano kategorii';
			}				
			$expenceComment = $_POST['komentarzInput'];			
			$userId = $_SESSION['id'];
			
			//pobieranie numeru kategorii wydatków
			$exp_cat = $polaczenie->query("SELECT cat_id FROM expence_categories WHERE cat_name='$expenceCategory'");
			if ($categories_list = $exp_cat->fetch_assoc()) {
				//Add newest 'cat_name' to the array
				$expenceCategoryId = $categories_list['cat_id'];
			}		

			$sql = "INSERT INTO `expences` (`date`, `ammount`, `category_id`, `user_id`, `comment`) VALUES (STR_TO_DATE('$expenceDate', '%Y-%m-%d'),".$expenceAmmount.",'$expenceCategoryId','$userId','$expenceComment')";
			
			if ($wszystko_OK)
			{
				if(mysqli_query($polaczenie, $sql)){
				 $_SESSION['dodano_wydatek'] = true;
				 unset($_POST['expenceAmmount']);
				} else{
					//porażka
					echo "ERROR: Could not able to execute $sql. " . mysqli_error($polaczenie);
				}
			}
		}		
	}
	
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="Mark Otto, Jacob Thornton, and Bootstrap contributors">
    <meta name="generator" content="Jekyll v4.0.1">
    <title>Moje Finanse</title>

	<!-- FontAwesome CSS -->
	<script src="https://kit.fontawesome.com/9427ffaa84.js" crossorigin="anonymous"></script>

    <!-- Bootstrap core CSS -->
	<link href="bootstrap-4.0.0-dist/css/bootstrap.css" rel="stylesheet">
	<link href="report_global.css" rel="stylesheet">
	<link href="bootstrap-4.0.0-dist/css/dashboard.css" rel="stylesheet">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
	
	<script type="text/javascript" src="scr_wydatki_przychody.js"></script>
	<script>
			// Edit row on edit button click
		$(document).on("click", ".edit", function(){		
			$(this).parents("tr").find("td:not(:last-child)").each(function(){
				$(this).html('<input type="text" class="form-control" value="' + $(this).text() + '">');
			});		
			$(this).parents("tr").find(".add, .edit").toggle();
			$(".add-new").attr("disabled", "disabled");
		});
		// Delete row on delete button click
		$(document).on("click", ".delete", function(){
			$(this).parents("tr").remove();
			$(".add-new").removeAttr("disabled");
		});
	</script>
  </head>
  
  
  
  
<body>
<!-- top nawigation bar-->
<nav class="navbar navbar-expand-md navbar-dark bg-primary fixed-top top-navbar">  
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#sidebarMenu" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
    <span data-feather="list"></span>
  </button>
  
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  
  <div class="collapse navbar-collapse" id="navbarsExampleDefault">
    <ul class="navbar-nav mr-auto">
      <li class="nav-item active">
        <a class="nav-link" href="Report.html"><span class="navbar-text">Moje Finanse</span><span class="sr-only">(current)</span></a>
      </li>
      <li class="nav-item">
        <a class="nav-link" href="UstawieniaAplikacji-kategorie-wydatkow.html"><span class="navbar-text">Aplikacja</span><span class="sr-only"></span></a>
      </li>
      <li class="nav-item">
       <a class="nav-link" href="UstawieniaUzytkownika-dane.html"><span class="navbar-text">Użytkownik</span><span class="sr-only"></span></a>
      </li>
	  <li class="nav-item">
        <a class="nav-link" href="logout.php"><span class="navbar-text">Wyloguj</span><span class="sr-only"></span></a>
      </li>
    </ul>
    <form class="form-inline my-2 my-lg-0">
      <input class="form-control mr-sm-2 search-input" type="text" placeholder="Search" aria-label="Search">
      <button class="btn btn-secondary my-2 my-sm-0 search-btn" type="submit">Search</button>
    </form>
  </div>
</nav>
<!-- END top nawigation bar-->
<!-- Finance Manager Content-->

<div class="container-fluid bg">

	<div class="row position-relative">

	<!--side Bar Menu-->
		<nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
		  <div class="sidebar-sticky pt-lg-3 pt-md-5">		  
			<ul class="nav flex-column pt-2">	
			<?php echo 'witaj:'.$_SESSION['name']; ?>
			  <li class="nav-item">
				<a class="nav-link" href="#" data-toggle="modal" data-target="#modalExpense">
				  <span data-feather="credit-card"></span>
				  Dodaj Wydatek <span class="sr-only">(current)</span>
				</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="#" data-toggle="modal" data-target="#modalIncome">
				  <span data-feather="dollar-sign"></span>
				  Dodaj Przychód
				</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="#">
				  <span data-feather="edit-2"></span>
				  Bieżący Miesiąc
				</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="#">
				  <span data-feather="edit-3"></span>
				  Poprzedni Miesiąc
				</a>
			  </li>
			  <li class="nav-item" href="#" data-toggle="modal" data-target="#modalAnotherRangeOfDate"">
				<a class="nav-link" href="#">
				  <span data-feather="filter"></span>
				  Dowolny okres
				</a>
			  </li>
			  <li class="nav-item">
				<a class="nav-link" href="#">
				  <span data-feather="activity"></span>
				  inne
				</a>
			  </li>
			</ul>       
		  </div>
		</nav>
		
		
	<!--Right Panel-->
		<main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4 pt-lg-3 pt-md-5">
		
			<?php
				if (isset($_SESSION['dodano_wydatek']) && $_SESSION['dodano_wydatek']){
					echo '<div class="row d-flex justify-content-center mt-3"><div class="alert alert-success" role="alert">Twój wydatek został dodany!</div></div>';
					unset($_SESSION['dodano_wydatek']);
				};				
			?>
			
			<div class="row d-flex justify-content-center mt-3">
				<div class="col-lg-3 col-sm-3 d-flex justify-content-center">
					<div class="info-box">
						<h1 class="text-center py-1">Suma Wpływów</h1>
							<div class="row justify-content-center mt-4"><span data-feather="plus-square"></span> <p class="balance font-weight-bold">120zł</p></div>
					</div>
				</div>
				<div class="col-lg-3 col-sm-3 d-flex justify-content-center">
					<div class="info-box balance">
						<h1 class="text-center py-1">Bilans</h1>
						<div class="row justify-content-center mt-4"><span data-feather="info"></span> <p class="balance font-weight-bold">145zł</p></div>
					</div>
				</div>
				<div class="col-lg-3 col-sm-3 d-flex justify-content-center">
					<div class="info-box">
						<h1 class="text-center py-1">Suma Wydatków</h1>
						<div class="row justify-content-center mt-4"><span data-feather="minus-square"></span> <p class="balance font-weight-bold">180zł</p></div>
					</div>
				</div>
			</div>

		<div class="row mt-3">
			<div class="col-lg-6">
				  <div class="table-responsive inc-exp-area mt-3">
				  	<h1 class="d-flex justify-content-center pb-2">Wydatki</h1>
					<div class="table-scrolling">
						<table id="table-expences" class="table table-striped table-sm table-bordered text-secondary table-light" >
						  <thead class="thead-dark">
							<tr>
							  <th>#</th>
							  <th>Data</th>
							  <th>Kwota [zł]</th>
							  <th>Kategoria</th>
							  <th>Komentarz</th>
							  <th>*</th>
							</tr>
						  </thead>
						  <tbody>
							<?php
								if ($polaczenie->connect_errno!=0)
								{
									echo "Error: ".$polaczenie->connect_errno;
								}
								else
								{
									$wydatki = $polaczenie->query("SELECT `expence_id`, `date`, `ammount`, `category_id`, `comment` FROM `expences` WHERE user_id='$userId'");
									$liczba_porzadkowa = 1;
									while ($wiersz_wydatkow = $wydatki->fetch_assoc()){
										echo '<tr>';
										  echo '<td>'.$liczba_porzadkowa.'</td>';
										  echo '<td>'.$wiersz_wydatkow['date'].'</td>';
										  echo '<td>'.$wiersz_wydatkow['ammount'].'</td>';
										  echo '<td>'.$wiersz_wydatkow['category_id'].'</td>';
										  echo '<td>'.$wiersz_wydatkow['comment'].'</td>';
										  echo '<td><a class="edit" title="Edit" data-toggle="tooltip"><i class="material-icons">&#xE254;</i></a>
												<a class="delete" title="Delete" data-toggle="tooltip"><i class="material-icons">&#xE872;</i></a></td>';
										echo '</tr>';
										$liczba_porzadkowa=$liczba_porzadkowa+1;
									};
								};
							?>							
						  </tbody>
						</table>
					</div>
				  </div>
				<div class="wykres">
					<canvas id="pieChart_wplywy"></canvas>
				</div>				  
			</div>
			
			<div class="col-lg-6">
				  <div class="table-responsive inc-exp-area mt-3">
				  	<h1 class="d-flex justify-content-center pb-2">Wpływy</h1>
					<div class="table-scrolling">
						<table id="table-incomes" class="table table-striped table-sm table-bordered text-secondary table-light">
						  <thead class="thead-dark">
							<tr>
							  <th>#</th>
							  <th>Data</th>
							  <th>Kwota [zł]</th>
							  <th>Kategoria</th>
							  <th>Komentarz</th>
							  <th>*</th>
							</tr>
						  </thead>
						  <tbody>
							<tr>
							  <td>1</td>
							  <td>22.05.2020</td>
							  <td>15</td>
							  <td>odsetki</td>
							  <td></td>
							  <td><a class="edit" title="Edit" data-toggle="tooltip"><i class="material-icons">&#xE254;</i></a>
									<a class="delete" title="Delete" data-toggle="tooltip"><i class="material-icons">&#xE872;</i></a></td>
							</tr>
							<tr>
							  <td>1</td>
							  <td>22.05.2020</td>
							  <td>16</td>
							  <td>praca</td>
							  <td></td>
							  <td><a class="edit" title="Edit" data-toggle="tooltip"><i class="material-icons">&#xE254;</i></a>
									<a class="delete" title="Delete" data-toggle="tooltip"><i class="material-icons">&#xE872;</i></a></td>
							</tr>
							<tr>
							  <td>1</td>
							  <td>22.05.2020</td>
							  <td>17</td>
							  <td>programowanie</td>
							  <td>wymiana oleju</td>
							  <td><a class="edit" title="Edit" data-toggle="tooltip"><i class="material-icons">&#xE254;</i></a>
									<a class="delete" title="Delete" data-toggle="tooltip"><i class="material-icons">&#xE872;</i></a></td>
							</tr>						
						  </tbody>
						</table>
					</div>
				  </div>
				  
					<div class="wykres">
						<canvas id="pieChart_wydatki"></canvas>	
					</div>

			</div>


			<!-- Modal Add INCOME -->
			<div class="modal fade  bd-example-modal-sm" id="modalIncome" tabindex="-1" role="dialog" aria-labelledby="modalIncome" aria-hidden="true">
			  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="modalIncomeTitle">Dodaj Przychód</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>
				  <div class="modal-body">
					<form>
					  <div class="form-group">
							<label for="kwotaInput">Kwota:</label>
							<input type="number" min="0" step="0.01" data-number-to-fixed="2" data-number-stepfactor="100" class="form-control currency" id="incomeAmmount" />
					  </div>
					  <div class="form-group">
						<label for="incomeDatePicker">Data:</label>
						<input type="date" class="form-control" id="incomeDatePicker" placeholder="dd-mm-yyyy">
						<script>TodayDate();</script>
					  </div>
					  <div class="form-group">
						<label for="kategoriaInput">Kategoria:</label>
						<select multiple class="form-control" name="kategoriaIncome" id="kategoriaIncome">						
							<?php
								foreach ($inc_cat_names as $inc_cat_name){
								echo "<option value=\"".$inc_cat_name."\" >$inc_cat_name </option>";
								}
							?>
						</select>
					  </div>
					  <div class="form-group">
						<label for="komentarzInput">Komentarz:</label>
						<textarea class="form-control" id="komentarzInput" rows="3"></textarea>
					  </div>
					</form>
				  </div>
				  <div class="modal-footer d-flex justify-content-center">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
					<button type="button" class="btn btn-primary">Dodaj</button>
				  </div>
				</div>
			  </div>
			</div>
			
			<!-- Modal Add EXPENSE -->
			<div class="modal fade  bd-example-modal-sm" id="modalExpense" tabindex="-1" role="dialog" aria-labelledby="modalExpense" aria-hidden="true">
			  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="modalIncomeTitle">Dodaj Wydatek</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>
				  <form method="post" id="add_expence">
					  <div class="modal-body">						
						  <div class="form-group">
							<label for="kwotaInput">Kwota:</label>
							<input type="number" min="0" step="0.01" data-number-to-fixed="2" data-number-stepfactor="100" class="form-control currency" name="expenceAmmount" id="expenceAmmount" />
								<?php
									if (isset($_SESSION['e_expenceAmmount']))
									{											
										echo '<div class="alert alert-warning mt-1" role="alert"><small>'.$_SESSION['e_expenceAmmount'].'</small></div>';
										unset($_SESSION['e_expenceAmmount']);
									}
								?>		
						  </div>
						  <div class="form-group">
							<label for="expenceDatePicker">Data:</label>
							<input type="date" class="form-control" name="expenceDatePicker" id="expenceDatePicker" placeholder="yyyy-mm-dd">
							<script>TodayDate();</script>
						  </div>
						  <div class="form-group">
								 <label for="radios">Forma płatności:</label>
										<div class="form-check">
										  <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios1" value="optionGotowka" checked>
										  <label class="form-check-label" for="gridRadios1">
											Gotówka
										  </label>
										</div>
										<div class="form-check">
										  <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios2" value="optionKartaPlatnicza">
										  <label class="form-check-label" for="gridRadios2">
											Karta Płatnicza
										  </label>
										</div>
										<div class="form-check">
										  <input class="form-check-input" type="radio" name="gridRadios" id="gridRadios3" value="OptionKartaDebetowa">
										  <label class="form-check-label" for="gridRadios3">
											Karta Debetowa
										  </label>
										</div>
						  </div>
						  <div class="form-group">
							<label for="kategoriaInput">Kategoria:</label>
							<select multiple class="form-control" name="kategoriaInput" id="kategoriaInput">
								<?php
								foreach ($exp_cat_names as $exp_cat_name){
								echo "<option value=\"".$exp_cat_name."\" >$exp_cat_name </option>";
								}
								?>
							</select>
						  </div>
						  <div class="form-group">
							<label for="komentarzInput">Komentarz:</label>
							<textarea class="form-control" name="komentarzInput" id="komentarzInput" rows="3"></textarea>
						  </div>						
					  </div>
					  <div class="modal-footer d-flex justify-content-center">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
						<button type="submit" class="btn btn-primary">Dodaj</button>
					  </div>
					</form>
				</div>
			  </div>
			</div>
			
			<!--MODAL Another Range of Date -->
			<div class="modal fade  modalAnotherRangeOfDate" id="modalAnotherRangeOfDate" tabindex="-1" role="dialog" aria-labelledby="modalAnotherRangeOfDate" aria-hidden="true">
			  <div class="modal-dialog modal-sm modal-dialog-centered" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<h5 class="modal-title" id="modalIncomeTitle">Wybierz Zakres Dat</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					  <span aria-hidden="true">&times;</span>
					</button>
				  </div>
				  <div class="modal-body">
					<form>					 
					  <div class="form-group">
						<label for="datePicker">Data początkowa:</label>
						<input type="date" class="form-control" id="dateStart" placeholder="dd-mm-yyyy">
					  </div>
					  <div class="form-group">
						<label for="datePicker">Data końcowa:</label>
						<input type="date" class="form-control" id="dateEnd" placeholder="dd-mm-yyyy">
					  </div>
					  
					</form>
				  </div>
				  <div class="modal-footer d-flex justify-content-center">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Anuluj</button>
					<button type="button" class="btn btn-primary">Dodaj</button>
				  </div>
				</div>
			  </div>
			</div>			
		</main>
	</div>
</div><!-- /.container -->


	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="../assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="bootstrap-4.0.0-dist/js/bootstrap.bundle.js"></script>
	<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script>window.jQuery || document.write('<script src="../assets/js/vendor/jquery.slim.min.js"><\/script>')</script><script src="bootstrap-4.0.0-dist/js/bundle.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.9.0/feather.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.3/Chart.min.js"></script>
    <script src="bootstrap-4.0.0-dist/js/dashboard.js"></script>
	<script src="bootstrap-4.0.0-dist/js/pieChart_wydatki.js"></script>
	<script src="bootstrap-4.0.0-dist/js/pieChart_wplywy.js"></script>

<?php $polaczenie->close(); ?>
</body>
</html>
