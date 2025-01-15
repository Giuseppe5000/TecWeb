<?php

require_once "./php/Navbar.php";
session_start();

$navbar = new Navbar("Disconnettiti");
$paginaHTML = file_get_contents('./static/logout.html');

if(isset($_SESSION['username'])){
    if(isset($_POST['permanenza'])){
        header('Location: ./profilo.php');
        exit;
    }
    if(isset($_POST['disconnessione'])){
        session_unset();
        session_destroy();
        header('Location: ./index.php');
        exit;
    }
}
else{
    header('Location: ./accedi.php');
    exit;
}

echo str_replace('{{NAVBAR}}', $navbar->getNavbar(), $paginaHTML);