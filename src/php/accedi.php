<?php

session_start();

require_once "./dbConnection.php";
use DB\DBAccess;

$paginaHTML = file_get_contents('./accedi.html');

$messaggiPerForm = "";

function pulisciInput($value)
{
 	$value = trim($value);
  	$value = strip_tags($value);
	$value = htmlentities($value);
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

		$connessione = new DBAccess();
		$connessioneOK = $connessione->openDBConnection();

		if(!$connessioneOK){
			
			$utente = $connessione->getUtenteLogin($username, $password);
			$connessione->closeConnection();

			if($utente != null){
				$_SESSION['username'] = $username;
				header('Location: ./index.html');
				exit;
			}
			else{
				$messaggiPerForm = "<p>Username o password errati</p>";
			}
		} else {
			$messaggiPerForm = "<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>";
		}
	}

	$paginaHTML = str_replace('{{ACCEDI}}', $messaggiPerForm, $paginaHTML);
	echo $paginaHTML;
}