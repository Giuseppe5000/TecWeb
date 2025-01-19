<?php

require_once "../../Database.php";
require_once "../../utils.php";
require_once "../../Utente.php";
session_start();

function checkInput($username, $email, $password, $confirmPassword, &$messaggi) {
    if (strlen($username)==0)
        $messaggi["username"] .= makeMessageParagraph("Il campo <span lang='en'>username</span> non può essere vuoto!");
    if (strlen($username)>30)
        $messaggi["username"] .= makeMessageParagraph("Il campo <span lang='en'>username</span> non può superare i 30 caratteri!");
    if (preg_match("/[\W]/", $username))
		$messaggi["username"] .= makeMessageParagraph("Il campo <span lang='en'>username</span> può contenere solo lettere e numeri!");

    if (strlen($password)==0 || strlen($confirmPassword)==0)
        $messaggi["password"] .= makeMessageParagraph("I campi <span lang='en'>password</span> e ripeti <span lang='en'>password</span> non possono essere vuoti!");
    if (strlen($password)>30 || strlen($confirmPassword)>30)
        $messaggi["password"] .= makeMessageParagraph("I campi <span lang='en'>password</span> e ripeti <span lang='en'>password</span> non possono superare 30 caratteri!");
    if($password != $confirmPassword)
        $messaggi["password"] .= makeMessageParagraph("I campi <span lang='en'>password</span> e ripeti <span lang='en'>password</span> non corrispondono!");
    if (!preg_match("/^[a-zA-Z0-9!@#$]*$/", $password) || !preg_match("/^[a-zA-Z0-9!@#$]*$/", $confirmPassword))
		$messaggi["password"] .= makeMessageParagraph("I campi <span lang='en'>password</span> e ripeti <span lang='en'>password</span> possono contenere solo lettere, numeri e i seguenti caratteri speciali: ! @ # $");

    if (strlen($email)==0)
        $messaggi["email"] .= makeMessageParagraph("Il campo <span lang='en'>email</span> non può essere vuoto!");
    if (strlen($email)>30)
        $messaggi["email"] .= makeMessageParagraph("Il campo <span lang='en'>email</span> non può superare i 30 caratteri!");
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $messaggi["email"] .= makeMessageParagraph("Formato <span lang='en'>email</span> non valido!");

    return strlen($messaggi["username"])==0 && strlen($messaggi["email"])==0 && strlen($messaggi["password"])==0;
}

function registrati($database){
    if(isset($_POST['submit'])){
        $messaggi = array("generico"=>"", "username"=>"", "email"=>"", "password"=>"");

        $username = $database->pulisciInput($_POST['username']);
        $email = $database->pulisciInput($_POST['email']);
        $password = $database->pulisciInput($_POST['password']);
        $confirmPassword = $database->pulisciInput($_POST['confirm-password']);
        
        if(checkInput($username, $email, $password, $confirmPassword, $messaggi)){
            $connessioneOK = $database->openConnection();
            
            if(!$connessioneOK){
                $utente = new Utente($database->getConnection(), $username, $password, $email);
                try {
                    $utente->register();
                    $database->closeConnection();
                    $_SESSION['username'] = $utente->getUsername();
                    header('Location: ../../../profilo.php');
                    exit;
                }
                catch(UserAlredyExistsException $e) {
                    $messaggi["generico"] .= makeMessageParagraph("Lo <span lang='en'>username</span> o la <span lang='en'>email</span> inserite sono già usate da un altro utente!");
                }
                catch(UserRegisterGenericException $e) {
                    $messaggi["generico"] .= makeMessageParagraph("È avvenuto un errore durante la registrazione, per favore riprova più tardi.");
                }
                catch(PrepareStatementException $e) {
                    header('Location: ../../../500.php');
                    exit;
                }
            }else{
                header('Location: ../../../500.php');
                exit;
            }
        }
        $_SESSION['user']=$username;
        $_SESSION['email']=$email;
        $_SESSION['password']=$password;
        $_SESSION['confimPassword']=$confirmPassword;
        $_SESSION['mexGenerico']=$messaggi["generico"];
        $_SESSION['mexUsername']=$messaggi["username"];
        $_SESSION['mexEmail']=$messaggi["email"];
        $_SESSION['mexPassword']=$messaggi["password"];
        header('Location: ../../../registrati.php');
        exit;
    }
}

#main
$database = new Database();
$connessioneOK = $database->openConnection();
if(!$connessioneOK){
    registrati($database);
}
header('Location: ../../../500.php');
exit;
    
