<?php

require_once "../../Database.php";
require_once "../../utils.php";
session_start();

function isSaldoOverflow($database, $utente, $saldo) {
    $query = "SELECT saldo FROM utente WHERE username = ?";
    $value = array($utente);
    $result = $database->executeSelectPreparedStatement($query,'s',$value);
    if(count($result) == 1){
        $saldoUtente = $result[0]["saldo"];
        return $saldoUtente + $saldo > 99999.99999;
    }
    else {
        // Non trovo l'utente o ne trovo più di uno,
        // quindi assumo che ci sia qualche errore di inconsistenza nel db => errore 500
        header('Location: ../../../500.php');
        exit;
    }
}

function checkMoney($money, &$messaggi) {
    $firstComma = strpos($money, ',');
    if ($firstComma) {
        $money = explode($money, ",");

        if (strlen($money[0])>5)
            $messaggi .= makeMessageParagraph("La valuta non può avere più di 5 cifre nella parte intera!");

        if (isset($money[1]) && strlen($money[1])>5)
            $messaggi .= makeMessageParagraph("La valuta non può avere più di 5 cifre nella parte decimale!");
    }
    else {
        if (strlen($money)>10)
            $messaggi .= makeMessageParagraph("La valuta non può avere più di 10 cifre nella parte intera!");
    }

    return strlen($messaggi)==0;
}

#post che  la recensione
function aggiungiSaldo($username,$database){
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aggiungi-saldo'])){
            $avvisoSaldo="";
            $saldo = $database->pulisciInput($_POST['saldo']);
            if (checkMoney($saldo, $avvisoSaldo)) {
                if (!isSaldoOverflow($database, $username, $saldo)) {
                    $query = "UPDATE utente SET saldo = saldo + ? WHERE username = ?";
                    $values = [$saldo, $username];
                    $avvisoSaldo .= $database->executeCRUDPreparedStatement($query, 'ds', $values);
                }
                else {
                    $avvisoSaldo .= makeMessageParagraph("Questa aggiunta potrebbe sforare il tetto massimo del saldo possedibile, che sarebbe 99999.99999!");
                }
            }
            
            $_SESSION['messaggioSaldo']=$avvisoSaldo;
            header('Location: ../../../'.$_POST['currentPage']);
            exit;
        }
}

#main
if(isset($_SESSION['username'])){
    $database = new Database();
    $connessioneOK = $database->openConnection();
    $username = $_SESSION['username'];
    if(!$connessioneOK){
        aggiungiSaldo($username,$database);
    }
    header('Location: ../../../500.php');
    exit;
    
}else{
    header('Location: ../../../accedi.php');
    exit;
}
