<?php

session_start();

require_once "./dbConnection.php";
use DB\DBAccess;

$paginaHTML = file_get_contents('./registrati.html');

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
    $email = pulisciInput($_POST['email']);
    $password = pulisciInput($_POST['password']);
    $confirmpassword = pulisciInput($_POST['confirm-password']);

    if (strlen($username)==0 || strlen($email)==0 || strlen($password)==0 || strlen($confirmpassword)==0){
        $messaggiPerForm .= "<p>Compilare tutti i campi</p>";
    }
    if (strlen($username)>30 || strlen($email)>30 || strlen($password)>30 || strlen($confirmpassword)>30){
        $messaggiPerForm .= "<p>Username, email e password non possono superare i 30 caratteri</p>";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $messaggiPerForm .= "<p>Formato email non valido</p>";
    }
    if($password != $confirmpassword){
        $messaggiPerForm .= "<p>Le password non corrispondono</p>";
    }

    if($messaggiPerForm == ""){
        $connessione = new DBAccess();
        $connessioneOK = $connessione->openDBConnection();

        if(!$connessioneOK){
            $connessione->hashConverter();
            
            if($connessione->verificaRegistrazione($username, $email) == false){
                $messaggiPerForm = "<p>Username o email già in uso</p>";
            }
            else{
                $registrazione = $connessione->registraUtente($username, $email, $password);

                if($registrazione == 0){
                    $messaggiPerForm = "<p>Errore durante la registrazione</p>";
                }
                else{
                    $_SESSION['username'] = $username;
                    header('Location: ./index.html');
                    exit;
                }
            }
        }
        else{
            $messaggiPerForm = "<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>";
        }

        $connessione->closeConnection();
    }

    $paginaHTML = str_replace('{{REGISTRATI}}', $messaggiPerForm, $paginaHTML);
    echo $paginaHTML;
}