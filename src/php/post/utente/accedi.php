<?php

require_once "../../Database.php";
require_once "../../utils.php";
require_once "../../Utente.php";
session_start();

function checkInput($username, $password, &$messaggi) {
	if (strlen($username)==0)
		$messaggi["username"] .= makeMessageParagraph('Il campo <span lang="en">username</span> non può essere vuoto!');
    if (strlen($username)>30)
        $messaggi["username"] .= makeMessageParagraph('Il campo <span lang="en">username</span> non può superare i 30 caratteri!');
    if (preg_match("/[\W]/", $username))
		$messaggi["username"] .= makeMessageParagraph('Il campo <span lang="en">username</span> può contenere solo lettere e numeri!');

	if (strlen($password)==0)
		$messaggi["password"] .= makeMessageParagraph('Il campo <span lang="en">password</span> non può essere vuoto!');
    if (strlen($password)>30)
        $messaggi["password"] .= makeMessageParagraph('Il campo <span lang="en">password</span> non può superare i 30 caratteri!');
    if (!preg_match("/^[a-zA-Z0-9!@#$]*$/", $password))
		$messaggi["password"] .= makeMessageParagraph('Il campo <span lang="en">password</span> può contenere solo lettere, numeri e i seguenti caratteri speciali: ! @ # $');

    return strlen($messaggi["username"])==0 && strlen($messaggi["password"])==0;
}

function accedi($database){
    if(isset($_POST['submit'])){
        $messaggi = array("generico"=>"", "username"=>"", "password"=>"");
        $username = "";
        $password = "";

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
                    header('Location: ../../../profilo.php');
                    exit;
                }
                else{
                    $messaggi["generico"] .= makeMessageParagraph('<span lang="en">Username</span> o <span lang="en">password</span> errati');
                }
            }
        }
        $_SESSION['user']=$username;
        $_SESSION['password']=$password;
        $_SESSION['mexGenerico']=$messaggi["generico"];
        $_SESSION['mexUsername']=$messaggi["username"];
        $_SESSION['mexPassword']=$messaggi["password"];
        header('Location: ../../../accedi.php');
        exit;
    }
}

#main
$database = new Database();
$connessioneOK = $database->openConnection();
if(!$connessioneOK){
    accedi($database);
}
header('Location: ../../../500.php');
exit;
    
