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

        $query  = "SELECT saldo FROM utente WHERE username = ?";
        $stmt = $database->getConnection()->prepare($query);
        if (!$stmt) {
            throw new PrepareStatementException($database->getConnection()->error);
        }
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $saldo = "<span>" . $username . "</span>
                <span>Saldo: " . $result->fetch_assoc()['saldo'] . "</span>";


        $query  = "SELECT path, nome FROM opera WHERE possessore = ?";
        $stmt = $database->getConnection()->prepare($query);
        if (!$stmt) {
            throw new PrepareStatementException($database->getConnection()->error);
        }
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if($result->num_rows == 0){
            $nftPosseduti = "<p>Non possiedi ancora nessun NFT</p>";
        }
        else{
            while($row = $result->fetch_assoc()){
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
    header('Location: ./accedi.php');
    exit;
}

$find=['{{SALDO}}','{{CARDS}}'];
$replacemenet=[$saldo,$nftPosseduti];
echo str_replace($find,$replacemenet,$paginaHTML);