<?php

require_once "./php/Database.php";
require_once "./php/Utente.php";
require_once "./php/Navbar.php";
session_start();

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
        $database = new Database();
        $connessioneOK = $database->openConnection();

        if(!$connessioneOK){

            $utente = new Utente($database->getConnection(), $username, $password, $email);
            try {
                $utente->register();
                $database->closeConnection();
                $_SESSION['username'] = $utente->getUsername();
                header('Location: ./index.php');
                exit;
            }
            catch(UserAlredyExistsException $e) {
                $messaggiPerForm = "<p>" . $e->errorMessage() . "</p>";
            }
            catch(UserRegisterGenericException $e) {
                $messaggiPerForm = "<p>" . $e->errorMessage() . "</p>";
            }
            catch(PrepareStatementException $e) {
                $messaggiPerForm = "<p>" . $e->errorMessage() . "</p>";
            }

        }else{
            $messaggiPerForm = "<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>";
        }
    }
}

$navbar = new Navbar("Registrati");
$paginaHTML = file_get_contents('./static/registrati.html');
$find=['{{REGISTRATI}}', '{{NAVBAR}}'];
$replacement=[$messaggiPerForm, $navbar->getNavbar()];

echo str_replace($find, $replacement, $paginaHTML);

