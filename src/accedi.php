<?php

require_once "./php/Navbar.php";
require_once "./php/utils.php";
session_start();
$messaggi = array("generico"=>"", "username"=>"", "password"=>"");
$username = "";
$password = "";

if(isset($_SESSION['username'])){
    header('Location: profilo.php');
    exit;
}

if(isset($_SESSION['user'])){
    $username=$_SESSION['user'];
    unset($_SESSION['user']);
}
if(isset($_SESSION['password'])){
    $password=$_SESSION['password'];
    unset($_SESSION['password']);
}
if(isset($_SESSION['mexGenerico'])){
    $messaggi["generico"]=$_SESSION['mexGenerico'];
    unset($_SESSION['mexGenerico']);
}
if(isset($_SESSION['mexUsername'])){
    $messaggi["username"]=$_SESSION['mexUsername'];
    unset($_SESSION['mexUsername']);
}
if(isset($_SESSION['mexPassword'])){
    $messaggi["password"]=$_SESSION['mexPassword'];
    unset($_SESSION['mexPassword']);
}

$navbar = new Navbar("Accedi");

$paginaHTML = file_get_contents('./static/accedi.html');
$find=['{{MESSAGGI_GENERICI}}', '{{MESSAGGI_USERNAME}}', '{{MESSAGGI_PASSWORD}}',
       '{{USERNAME}}', '{{PASSWD}}', '{{NAVBAR}}'];
$replacement=[$messaggi["generico"], $messaggi["username"], $messaggi["password"],
              $username, $password, $navbar->getNavbar()];

echo str_replace($find, $replacement, $paginaHTML);
