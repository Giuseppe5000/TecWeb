<?php

require_once "../../Database.php";
require_once "../../utils.php";
session_start();
#post che cancella la recensione
function aggiungiRecensione($username,$database){
    
    if (isset($_POST['aggiungi']) && isset($_SESSION['username'])) {
        //recupera valori del form
        $username=$_SESSION['username'];
        $opera=$_POST['id'];
        $voto=$database->pulisciInput($_POST['voto']);
        $recensione=$database->pulisciInput($_POST['recensione']);

        $query = "INSERT INTO recensione (timestamp, utente, commento, opera, voto) VALUES (?, ?, ?, ?, ?)";
        
        $value = array(date("Y-m-d h:i:s"),$username, $recensione, $opera, $voto);
        $database->executeCRUDPreparedStatement($query,'sssii',$value);
        header('Location: ../../..'.$_POST['currentPage']);
        exit;
    }
}

#main
if(isset($_SESSION['username'])){
    $database = new Database();
    $connessioneOK = $database->openConnection();
    $username = $_SESSION['username'];
    if(!$connessioneOK){
        aggiungiRecensione($username,$database);
    }
    header('Location: ../../../500.php');
    exit;
    
}else{
    header('Location: ../../../accedi.php');
    exit;
}
