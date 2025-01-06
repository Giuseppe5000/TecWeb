<?php

require_once "./php/Database.php";
require_once "./php/Utente.php";
require_once "./php/Navbar.php";

session_start();

$messaggiPerForm = "";

function pulisciInput($value){
    $value = trim($value);
    $value = strip_tags($value);
    #$value = htmlentities($value);
    return $value;
}
if(isset($_POST['submit'])){
    $username = pulisciInput($_POST['username']);
	$password = pulisciInput($_POST['password']);

	if (strlen($username)==0 || strlen($password)==0){
		$messaggiPerForm .= "<p>Username o password mancanti!</p>";
	}
	if (strlen($username)>30 || strlen($password)>30){
		$messaggiPerForm .= "<p>Username e password non possono superare i 30 caratteri!</p>";
	}

	if($messaggiPerForm == ""){

		$database = new Database();
		$connessioneOK = $database->openConnection();

		if(!$connessioneOK){

			$utente = new Utente($database->getConnection(), $username, $password, "");
			$loginSuccessfull = $utente->login();
			$database->closeConnection();

			if( $loginSuccessfull ){
				$_SESSION['username'] = $utente->getUsername();
				header('Location: ./profilo.php');
				exit;
			}
			else{
				$messaggiPerForm = "<p>Username o password errati</p>";
			}
		} else {
			$messaggiPerForm = "<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>";
		}
	}
}


$navbar = new Navbar("Accedi");

$paginaHTML = file_get_contents('./static/accedi.html');
$find=['{{ACCEDI}}', '{{NAVBAR}}'];
$replacement=[$messaggiPerForm, $navbar->getNavbar()];

echo str_replace($find, $replacement, $paginaHTML);
