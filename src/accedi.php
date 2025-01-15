<?php

require_once "./php/Database.php";
require_once "./php/Utente.php";
require_once "./php/Navbar.php";
require_once "./php/utils.php";
session_start();

$messaggi = array("generico"=>"", "username"=>"", "password"=>"");
$username = "";
$password = "";

function checkInput($username, $password, &$messaggi) {
	if (strlen($username)==0)
		$messaggi["username"] .= makeMessageParagraph("Il campo username non può essere vuoto!");
    if (strlen($username)>30)
        $messaggi["username"] .= makeMessageParagraph("Il campo username non può superare i 30 caratteri!");
    if (preg_match("/[\W]/", $username))
		$messaggi["username"] .= makeMessageParagraph("Il campo username può contenere solo lettere e numeri!");

	if (strlen($password)==0)
		$messaggi["password"] .= makeMessageParagraph("Il campo password non può essere vuoto!");
    if (strlen($password)>30)
        $messaggi["password"] .= makeMessageParagraph("Il campo password non può superare i 30 caratteri!");
    if (preg_match("/^[^a-zA-Z0-9!@#$]*$/", $password))
		$messaggi["password"] .= makeMessageParagraph("Il campo password può contenere solo lettere, numeri e i seguenti caratteri speciali: ! @ # $ ");

    return strlen($messaggi["username"])==0 && strlen($messaggi["password"])==0;
}

if(isset($_POST['submit'])){
    $database = new Database();
    $username = $database->pulisciInput($_POST['username']);
	$password = $database->pulisciInput($_POST['password']);

    if(checkInput($username, $password, $messaggi)){
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
				$messaggi["generico"] .= makeMessageParagraph("Username o password errati");
			}
		} else {
            header('Location: ./500.php');
		}
	}
}

$navbar = new Navbar("Accedi");

$paginaHTML = file_get_contents('./static/accedi.html');
$find=['{{MESSAGGI_GENERICI}}', '{{MESSAGGI_USERNAME}}', '{{MESSAGGI_PASSWORD}}',
       '{{USERNAME}}', '{{PASSWD}}', '{{NAVBAR}}'];
$replacement=[$messaggi["generico"], $messaggi["username"], $messaggi["password"],
              $username, $password, $navbar->getNavbar()];

echo str_replace($find, $replacement, $paginaHTML);
