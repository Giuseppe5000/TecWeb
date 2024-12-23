<?php

require_once "./dbConnection.php";
use DB\DBAccess;

$paginaHTML = file_get_contents('./accedi.html');

$stringaUtenti = "";

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
		$stringaUtenti .= "<p>Username o password mancanti!</p>";
	}
	if (strlen($username)>30 || strlen($password)>30){
		$stringaUtenti .= "<p>Username e password non possono superare i 30 caratteri!</p>";
	}

	if($stringaUtenti == ""){

		$connessione = new DBAccess();
		$connessioneOK = $connessione->openDBConnection();

		if(!$connessioneOK){

			$utente = $connessione->getUtenteLogin($username, $password);

			if($utente != null){
				session_start();
				$_SESSION['username'] = $username;
				header('Location: ./index.html');
				exit;
			}
			else{
				$stringaUtenti = "<p>Username o password errati</p>";
			}
		} else {
			$stringaUtenti = "<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>";
		}

		$connessione->closeConnection();
	}

	str_replace('{{ACCEDI}}', $stringaUtenti, $paginaHTML);
	echo $paginaHTML;
}