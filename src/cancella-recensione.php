<?php

require_once "./php/Database.php";
session_start();
#post che cancella la recensione
function cancellaRecensione($username,$database){
    if(isset($_POST['cancella_x'])) {
        //recupera valori del form
        $date=$_POST['timestamp'];
        
        $query = "DELETE FROM recensione WHERE utente=? AND timestamp=?";
        
        $value = array($username,$date);
        $database->executeCRUDPreparedStatement($query,'ss',$value);
        header('Location: .'.$_POST['currentPage']);
        exit;
    }
}

#main
if(isset($_SESSION['username'])){
    $database = new Database();
    $connessioneOK = $database->openConnection();
    $username = $_SESSION['username'];
    if(!$connessioneOK){
        cancellaRecensione($username,$database);
    }
    header('Location: ./500.php');
    exit;
    
}else{
    header('Location: ./accedi.php');
    exit;
}
