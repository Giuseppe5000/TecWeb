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

function generateUniqueFilename($extension) {
    $uniqueId = substr(uniqid(), -5); //Prendo solo gli ultimi 5 perchè senno è troppo lungo
    return "nft" . $uniqueId . "." . $extension;
}

function checkInput($nome, $descrizione, $prezzo, &$messaggi) {
	if (strlen($nome)==0)
		$messaggi .= makeMessageParagraph("Il campo nome non può essere vuoto!");

    if (strlen($nome)>30)
        $messaggi .= makeMessageParagraph("Il campo nome non può superare i 30 caratteri!");

	if (strlen($descrizione)==0)
		$messaggi .= makeMessageParagraph("Il campo descrizione non può essere vuoto!");

	if (strlen($descrizione)>300)
		$messaggi .= makeMessageParagraph("Il campo descrizione non può superare i 300 caratteri!");

    checkDescrizione($descrizione, $messaggi);
    checkMoney($prezzo, $messaggi);

    return strlen($messaggi)==0;
}

function checkDescrizione($descrizione, &$messaggi) {
    $firstDot = strpos($descrizione, '.');
    if ($firstDot != false) {
        $alt = substr($descrizione, 0, $firstDot);
        if (strlen($alt)>100)
            $messaggi .= makeMessageParagraph("La prima frase del campo descrizione (dall'inizio fino al primo punto) deve essere lunga al massimo 150 caratteri!");
    }
    else {
            $messaggi .= makeMessageParagraph("Il campo descrizione deve avere almeno un punto alla fine!");
    }
}

function checkMoney($money, &$messaggi) {
    $firstComma = strpos($money, ',');
    if ($firstComma) {
        $money = explode($money, ",");

        if (strlen($money[0])>10)
            $messaggi .= makeMessageParagraph("La valuta non può avere più di 10 cifre nella parte intera!");

        if (isset($money[1]) && strlen($money[1])>2)
            $messaggi .= makeMessageParagraph("La valuta non può avere più di 2 cifre nella parte decimale!");
    }
    else {
        if (strlen($money)>10)
            $messaggi .= makeMessageParagraph("La valuta non può avere più di 10 cifre nella parte intera!");
    }

    return strlen($messaggi)==0;
}


if(isset($_SESSION['username'])){
    $database = new Database();
    $connessioneOK = $database->openConnection();
    $username = $_SESSION['username'];

    if(!$connessioneOK){
        // Se il form di aggiunta nuovo nft è stato inviato
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aggiungi-opera'])) {
            $nome = $database->pulisciInput($_POST['nome']);
            $descrizione = $database->pulisciInput($_POST['descrizione']);
            $prezzo = $database->pulisciInput($_POST['prezzo']);

            if (checkInput($nome, $descrizione, $prezzo, $avvisoCaricaNFT)) {
                $target_dir = "./assets/";
                $imageFileType = strtolower(pathinfo($_FILES["immagine"]["name"], PATHINFO_EXTENSION));
                $target_file = $target_dir . generateUniqueFilename($imageFileType);
        
                if ($_FILES["immagine"]["size"] > 500000) {
                    $avvisoCaricaNFT .= "<p>L'immagine è di dimensioni troppo grandi.</p>";
                } elseif ($imageFileType !== "webp") {
                    $avvisoCaricaNFT .= "<p>Sono permessi solo immagini in formato WebP.</p>";
                } elseif (empty($nome) || empty($descrizione) || empty($prezzo)) {
                    $avvisoCaricaNFT .= "<p>Compila tutti i campi.</p>";
                } elseif (!move_uploaded_file($_FILES["immagine"]["tmp_name"], $target_file)) {
                    $avvisoCaricaNFT .= "<p>Errore durante il caricamento dell'immagine.</p>";
                } else {
                    $path = rtrim($target_file, '.webp');
                    $query = "INSERT INTO opera (path, nome, descrizione, prezzo) VALUES (?, ?, ?, ?)";
                    $format_string = 'sssd';
                    $values = [$path, $nome, $descrizione, $prezzo];
                    $avvisoCaricaNFT = $database->executeCRUDPreparedStatement($query, $format_string, $values);
        
                    if (strpos($avvisoCaricaNFT, 'successo') !== false && !empty($_POST['categorie']) && is_array($_POST['categorie'])) {
                        $id_opera = $database->getConnection()->insert_id;

                        foreach ($_POST['categorie'] as $categoria) {
                            $query = "INSERT INTO appartenenza (categoria, opera) VALUES (?, ?)";
                            $format_string = 'si';
                            $values = [$categoria, $id_opera];
                            $database->executeCRUDPreparedStatement($query, $format_string, $values);
                        }
                    }
                }
            }

            echo $avvisoCaricaNFT;

        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aggiungi-saldo'])){
            $saldo = $database->pulisciInput($_POST['saldo']);
            if (checkMoney($saldo, $avvisoSaldo)) {
                $query = "UPDATE utente SET saldo = saldo + ? WHERE username = ?";
                $values = [$saldo, $username];
                $avvisoSaldo .= $database->executeCRUDPreparedStatement($query, 'ds', $values);
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
                    $caricaNFT = file_get_contents('./static/carica-nft.html');;
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
                $recensioni_html.='<input type="hidden" name="timestamp" value="'.$recensione["timestamp"].'"/>';
                $recensioni_html.='<input id="modifica" type="image" src="assets/edit_icon.svg" alt="modifica recensione" name="modifica">';
                $recensioni_html.='</div>';
                $recensioni_html.='</form>';

                $recensioni_html.='<form class="form_recensione" action="cancella-recensione.php" method="post">';
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

$find=['{{SALDO}}', '{{AVVISO_SALDO}}', '{{AVVISO_CARICA_NFT}}', '{{CARICA_NFT}}', '{{CARDS}}', '{{NAVBAR}}','{{RECENSIONI}}','{{LINK_NFT}}','{{LINK_RECENSIONI}}'];
$replacemenet=[$saldo, $avvisoSaldo, $avvisoCaricaNFT, $caricaNFT, $nftPosseduti, $navbar->getNavbar(), $recensioni_html, $linkNft, $linkRecensioni];
echo str_replace($find,$replacemenet,$paginaHTML);
