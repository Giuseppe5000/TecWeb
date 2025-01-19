<?php

require_once "./php/Database.php";
require_once "./php/Navbar.php";
require_once "./php/CardOpera.php";
require_once "./php/utils.php";
session_start();

$saldo = "";
$nftPosseduti = ""; 
$recensioni_html = "";
$avvisoSaldo = "";
$avvisoCaricaNFT = "";
$caricaNFT = "";
$linkNft = "";
$linkRecensioni = "";
$skipButton = "";


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
        header('Location: ./500.php');
    }
}


if(isset($_SESSION['username'])){
    $database = new Database();
    $connessioneOK = $database->openConnection();
    $username = $_SESSION['username'];

    if(!$connessioneOK){

        if(isset($_SESSION['messaggioCaricaNFT'])){
            $avvisoCaricaNFT=$_SESSION['messaggioCaricaNFT'];
            unset($_SESSION['messaggioCaricaNFT']);
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aggiungi-saldo'])){
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
        }


        // Query per ottenere il username e il saldo dell'utente, se l'utente è amministratore compare il form di insermento di una nuova opera
        $query  = "SELECT saldo, isAdmin FROM utente WHERE username = ?";
        $value = array($username);
        $result = $database->executeSelectPreparedStatement($query,'s',$value);
        if(count($result) > 0){
            foreach($result as $row){
                $saldo = "<span>" . $username . "</span>";
                $saldo .= "<span>Saldo: " . $row['saldo'] . "</span>";

                if($row['isAdmin']){
                    $caricaNFT = file_get_contents('./static/carica-nft.html');
                    $skipButton.='<nav aria-label="aiuti alla navigazione: aggiungi NFT" class="listHelp">
	<a href="#agg-nft" class="navigationHelp">Vai ad Aggiungi <abbr lang="en" title="Non-fungible token">NFT</abbr></a>
      </nav>';
                }else{
                    $skipButton.='<nav aria-label="aiuti alla navigazione: miei NFT" class="listHelp">
	<a href="#miei-nft" class="navigationHelp">Vai ai Miei <abbr lang="en" title="Non-fungible token">NFT</abbr></a>
      </nav>';
                }
            }
        }

        // Query per ottenere le opere e le recensioni dell'utente
        $query  = "SELECT * FROM opera WHERE possessore = ?";
        $value = array($username);
        $opere = $database->executeSelectPreparedStatement($query,'s',$value);

        $query  = "SELECT recensione.*, nome FROM recensione JOIN opera ON recensione.opera = opera.id WHERE utente = ? ORDER BY timestamp DESC";
        $recensioni = $database->executeSelectPreparedStatement($query,'s',$value);
        $database->closeConnection();

        if(count($opere) == 0){
            $nftPosseduti = '<p>Non possiedi ancora nessun <abbr lang="en" title="Non-fungible token">NFT</abbr></p>';
        }
        else{
            $count=0;
            while($count<6 && $count<count($opere)){
                $opera=$opere[$count];
                $card = new CardOpera($opera);
                $nftPosseduti .= $card->getProfileCard();
                $count++;
            }
            if($count<count($opere)){
                $linkNft.='<p class="center"><a href="miei-nft.php">Visualizza gli altri <abbr lang="en" title="Non-fungible token">NFT</abbr> posseduti</a></p>';
            }
        }

        if(count($recensioni) == 0){
            $recensioni_html = "<p>Non hai ancora fatto alcuna recensione</p>";
        }else{
            $count=0;
            while($count<5 && $count<count($recensioni)){
                $recensione=$recensioni[$count];

                $date = strtotime($recensione["timestamp"]);
                $date = date('d-m-Y',$date);
                $utente = $recensione["utente"];
                
                $recensioni_html.='<div class="comment">';
                $recensioni_html.='<div class="head-comment">';
                $recensioni_html.='<div class="user-comment">';
                $recensioni_html.= '<a href="singolo-nft.php?id=' . $recensione["opera"] . '">';
                $recensioni_html.='<span>'.$recensione["nome"].'</span>';
                $recensioni_html.= '</a>';
                $recensioni_html.='</div>';
                $recensioni_html .= '<div><span>' . $recensione["voto"] .' &#9733;</span></div>';
                $recensioni_html.= "{$date}";
                $recensioni_html.='<div class="user-comment">';

                $recensioni_html.='<form class="form_recensione" action="modifica-recensione.php">';
                $recensioni_html.='<div>';
                $recensioni_html.='<input type="hidden" name="currentPage" value="'.$_SERVER["PHP_SELF"].'"/>';
                $recensioni_html.='<input type="hidden" name="timestamp" value="'.$recensione["timestamp"].'"/>';
                $recensioni_html.='<input id="modifica" type="image" src="assets/edit_icon.svg" alt="modifica recensione" name="modifica">';
                $recensioni_html.='</div>';
                $recensioni_html.='</form>';

                $recensioni_html.='<form class="form_recensione" action="php/post/recensione/cancella-recensione.php" method="post">';
                $recensioni_html.='<div>';
                $recensioni_html.='<input type="hidden" name="currentPage" value="'.$_SERVER["PHP_SELF"].'"/>';
                $recensioni_html.='<input type="hidden" name="timestamp" value="'.$recensione["timestamp"].'"/>';
                $recensioni_html.='<input id="cancella" type="image" src="assets/delete_icon.svg" alt="cancella recensione" name="cancella">';
                $recensioni_html.='</div>';
                $recensioni_html.='</form>';

                $recensioni_html.='</div>';
                $recensioni_html.='</div>';
                $recensioni_html.='<p>'.$recensione["commento"].'</p>';
                $recensioni_html.='</div>';

                $count++;
            }
            if($count<count($recensioni)){
                $linkRecensioni.='<p class="center"><a href="mie-recensioni.php">Visualizza le altre recensioni effettuate</a></p>';
            }
        }
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

$navbar = new Navbar("Profilo");
$paginaHTML = file_get_contents('./static/profilo.html');

$find=['{{SALDO}}', '{{AVVISO_SALDO}}', '{{AVVISO_CARICA_NFT}}', '{{CARICA_NFT}}', '{{CARDS}}', '{{NAVBAR}}','{{RECENSIONI}}','{{LINK_NFT}}','{{LINK_RECENSIONI}}','{{SKIP_BUTTON}}'];
$replacemenet=[$saldo, $avvisoSaldo, $avvisoCaricaNFT, $caricaNFT, $nftPosseduti, $navbar->getNavbar(), $recensioni_html, $linkNft, $linkRecensioni,$skipButton];
echo str_replace($find,$replacemenet,$paginaHTML);
