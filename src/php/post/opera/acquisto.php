<?php

require_once "../../Database.php";
session_start();
#post che cancella la recensione
function acquistaOpera($username,$database){
    if (isset($_POST['acquista']) && isset($_SESSION['username'])) {
        //recupera valori del form
        $username=$_SESSION['username'];
        $id=$_POST['id'];
        $prezzo=(double)$_POST['prezzo'];

        #controllo che possa acquistare l'opera tramite controllo del saldo
        $query = "SELECT saldo FROM utente WHERE username = ?";
        $value=array($username);
        $saldo = $database->executeSelectPreparedStatement($query,'s',$value);
        
        if ($saldo[0]["saldo"]>=$prezzo){
            $query = "INSERT INTO acquisto (utente, opera, prezzo, data) VALUES (?, ?, ?, ?)";
            $value = array($username, $id, $prezzo, date("Y-m-d h:i:s"));
            $database->executeCRUDPreparedStatement($query,'sids',$value);

            #modifico il possessore dell'opera e il saldo dell'utente
            $query = "UPDATE opera SET possessore = ? WHERE id = ?";
            $value = array($username, $id);
            $database->executeCRUDPreparedStatement($query,'si',$value);
            
            $query = "UPDATE utente SET saldo = ? WHERE username = ?";
            $nuovo_saldo=$saldo[0]["saldo"]-$prezzo;
            $value = array($nuovo_saldo,$username);
            $database->executeCRUDPreparedStatement($query,'ds',$value);
            $_SESSION['acquistato']=true;
        }else{
            $_SESSION['acquistato']=false;
        }
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
        acquistaOpera($username,$database);
    }
    header('Location: ../../../500.php');
    exit;
    
}else{
    header('Location: ../../../accedi.php');
    exit;
}
