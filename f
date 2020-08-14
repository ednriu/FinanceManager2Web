[1mdiff --git a/Report.php b/Report.php[m
[1mindex e0cdf83..6b5093d 100644[m
[1m--- a/Report.php[m
[1m+++ b/Report.php[m
[36m@@ -149,31 +149,38 @@[m
 [m
 		//-----------------------------------------[m
 		//Wylistowanie używanech kategorii wydatków[m
[31m-		$sql = $polaczenie->query('SELECT expence_categories.cat_name FROM expence_categories, expences WHERE expences.user_id='.$_SESSION['id'].' AND expences.category_id=expence_categories.cat_id');[m
[31m-		$KategorieWydatkowNiezerowych = array(); [m
[31m-		While ($liniaDanych = $sql->fetch_assoc()) {[m
[31m-			$KategorieWydatkowNiezerowych[] = $liniaDanych['cat_name'];[m
[31m-		}[m
[31m-		$unikalneKategorieWydatkowNiezerowych = array_unique($KategorieWydatkowNiezerowych);[m
[31m-		[m
[31m-		//Wypełnianie wykresu wydatków[m
[31m-		$daneWykresuWydatkow = array();[m
[31m-		foreach ($unikalneKategorieWydatkowNiezerowych as $k => $etykietaWydatkow) {[m
[31m-			$sql_2 = $polaczenie->query("SELECT SUM(expences.ammount) as total FROM expences, expence_categories WHERE expences.user_id=".$_SESSION['id']."  AND expence_categories.cat_name='$etykietaWydatkow' AND expence_categories.cat_id=expences.category_id");[m
[31m-			if ($sql_2)[m
[31m-			{[m
[31m-				$row = $sql_2->fetch_assoc();		[m
[31m-				$sumaWydatkowDanejKategorii = $row['total'];[m
[31m-				$wydatkiWProcentach=round(($sumaWydatkowDanejKategorii*100)/$_SESSION['suma_wydatkow'],2);[m
[31m-				$new_array=array("label"=>$etykietaWydatkow, "y"=>$wydatkiWProcentach);[m
[31m-				array_push($daneWykresuWydatkow, $new_array);[m
[32m+[m		[32mif ($_SESSION['suma_wydatkow']!=0) {[m
[32m+[m			[32m$sql = $polaczenie->query('SELECT expence_categories.cat_name FROM expence_categories, expences WHERE expences.user_id='.$_SESSION['id'].' AND expences.category_id=expence_categories.cat_id');[m
[32m+[m			[32m$KategorieWydatkowNiezerowych = array();[m[41m [m
[32m+[m			[32mWhile ($liniaDanych = $sql->fetch_assoc()) {[m
[32m+[m				[32m$KategorieWydatkowNiezerowych[] = $liniaDanych['cat_name'];[m
 			}[m
[31m-			else[m
[31m-			{[m
[31m-				echo "Brak danych lub błąd połączenia z Bazą.";				[m
[32m+[m			[32m$unikalneKategorieWydatkowNiezerowych = array_unique($KategorieWydatkowNiezerowych);[m
[32m+[m[41m			[m
[32m+[m			[32m//Wypełnianie wykresu wydatków[m
[32m+[m			[32m$daneWykresuWydatkow = array();[m
[32m+[m			[32mforeach ($unikalneKategorieWydatkowNiezerowych as $k => $etykietaWydatkow) {[m
[32m+[m				[32m$sql_2 = $polaczenie->query("SELECT SUM(expences.ammount) as total FROM expences, expence_categories WHERE expences.user_id=".$_SESSION['id']."  AND expence_categories.cat_name='$etykietaWydatkow' AND expence_categories.cat_id=expences.category_id");[m
[32m+[m				[32mif ($sql_2)[m
[32m+[m				[32m{[m
[32m+[m					[32m$row = $sql_2->fetch_assoc();[m[41m		[m
[32m+[m					[32m$sumaWydatkowDanejKategorii = $row['total'];[m
[32m+[m					[32m$wydatkiWProcentach=round(($sumaWydatkowDanejKategorii*100)/$_SESSION['suma_wydatkow'],2);[m
[32m+[m					[32m$new_array=array("label"=>$etykietaWydatkow, "y"=>$wydatkiWProcentach);[m
[32m+[m					[32marray_push($daneWykresuWydatkow, $new_array);[m
[32m+[m				[32m}[m
[32m+[m				[32melse[m
[32m+[m				[32m{[m
[32m+[m					[32mecho "Brak danych lub błąd połączenia z Bazą.";[m[41m				[m
[32m+[m				[32m}[m
[32m+[m[41m							[m
 			}[m
[31m-						[m
 		}[m
[32m+[m		[32melse[m
[32m+[m		[32m{[m
[32m+[m			[32m$daneWykresuWydatkow = array(array("brak danych", 100));[m
[32m+[m		[32m}[m
[32m+[m[41m		[m
 [m
 		//-----------------------------------------[m
 		//Wylistowanie używanech kategorii przychodow[m
