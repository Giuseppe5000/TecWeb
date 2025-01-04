<?php

session_start();

require_once "./php/Database.php";

$paginaHTML = file_get_contents('./static/profilo.html');

$saldo = "";
$nftPosseduti = ""; 

if(isset($_SESSION['username'])){
    $database = new Database();
    $connessioneOK = $database->openConnection();
    $username = $_SESSION['username'];

    if(!$connessioneOK){

        $query  = "SELECT saldo FROM utente WHERE username = " . $username;
        $result = $database->executeQuery($query);
        $saldo = "<span>" . $username . "</span>
                <span>Saldo: " . $result[0]['saldo'] . "</span>";
    

        $query  = "SELECT path, nome FROM opera WHERE possessore = " . $username;
        $result = $database->executeQuery($query);
        if(count($result) == 0){
            $nftPosseduti = "<p>Non possiedi ancora nessun NFT</p>";
        }
        else{
            foreach($result as $row){
                $nftPosseduti .= "<div class='card'>
                                    <a href='nft.html?nft=TITOLO'>
                                    <h3>" . $row['nome'] . "</h3>
                                    <img src='" . $row['path'] . "' width='140' height='140'>
                                </div>";
            }
        }
        $database->closeConnection();
    }
    else{
        $saldo = "<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>";
        $nftPosseduti = "<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>";
    }
}
else{
    header('Location: ./login.html');
    exit;
}

$find=['{{SALDO}}','{{CARDS}}'];
$replacemenet=[$saldo,$nftPosseduti];
echo str_replace($find,$replacemenet,$paginaHTML);
