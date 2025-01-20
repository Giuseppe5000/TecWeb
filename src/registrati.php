<?php

require_once "./php/Navbar.php";
require_once "./php/utils.php";
session_start();

$messaggi = array("generico"=>"", "username"=>"", "email"=>"", "password"=>"");
$username = "";
$email = "";
$password = "";
$confirmPassword = "";


if(isset($_SESSION['username'])){
    header('Location: profilo.php');
    exit;
}

if(isset($_SESSION['user'])){
    $username=$_SESSION['user'];
    unset($_SESSION['user']);
}
if(isset($_SESSION['email'])){
    $email=$_SESSION['email'];
    unset($_SESSION['email']);
}
if(isset($_SESSION['password'])){
    $password=$_SESSION['password'];
    unset($_SESSION['password']);
}
if(isset($_SESSION['confimPassword'])){
    $confirmPassword=$_SESSION['confimPassword'];
    unset($_SESSION['confimPassword']);
}
if(isset($_SESSION['mexGenerico'])){
    $messaggi["generico"]=$_SESSION['mexGenerico'];
    unset($_SESSION['mexGenerico']);
}
if(isset($_SESSION['mexUsername'])){
    $messaggi["username"]=$_SESSION['mexUsername'];
    unset($_SESSION['mexUsername']);
}
if(isset($_SESSION['mexEmail'])){
    $messaggi["email"]=$_SESSION['mexEmail'];
    unset($_SESSION['mexEmail']);
}
if(isset($_SESSION['mexPassword'])){
    $messaggi["password"]=$_SESSION['mexPassword'];
    unset($_SESSION['mexPassword']);
}

$navbar = new Navbar("Registrati");
$paginaHTML = file_get_contents('./static/registrati.html');
$find=['{{MESSAGGI_GENERICI}}', '{{MESSAGGI_USERNAME}}', '{{MESSAGGI_EMAIL}}', '{{MESSAGGI_PASSWORD}}',
       '{{USERNAME}}', '{{EMAIL}}', '{{PASSWD}}', '{{RIPETI_PASSWD}}', '{{NAVBAR}}'];
$replacement=[$messaggi["generico"], $messaggi["username"], $messaggi["email"], $messaggi["password"],
              $username, $email, $password, $confirmPassword, $navbar->getNavbar()];

echo str_replace($find, $replacement, $paginaHTML);
