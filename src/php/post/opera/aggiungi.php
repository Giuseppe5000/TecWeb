<?php

require_once "../../Database.php";
require_once "../../utils.php";
session_start();

function generateUniqueFilename($extension) {
    $uniqueId = substr(uniqid(), -5); //Prendo solo gli ultimi 5 perchè senno è troppo lungo
    return "nft" . $uniqueId . "." . $extension;
}

function checkInput($nome, $descrizione, $prezzo, &$messaggi) {
	if (strlen($nome)==0)
		$messaggi .= makeMessageParagraph("Il campo nome non può essere vuoto!");
    if (preg_match("/[\W]/", $nome))
		$messaggi .= makeMessageParagraph("Il campo nome può contenere solo lettere e numeri!");

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
            $messaggi .= makeMessageParagraph("La prima frase del campo descrizione (dall'inizio fino al primo punto) deve essere lunga al massimo 100 caratteri!");
    }
    else {
            $messaggi .= makeMessageParagraph("Il campo descrizione deve avere almeno un punto alla fine!");
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
function aggiungiOpera($username,$database){
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aggiungi-opera'])) {
            $avvisoCaricaNFT="";
            $nome = $database->pulisciInput($_POST['nome']);
            $descrizione = $database->pulisciInput($_POST['descrizione']);
            $prezzo = $database->pulisciInput($_POST['prezzo']);

            if (checkInput($nome, $descrizione, $prezzo, $avvisoCaricaNFT)) {
                $target_dir = "../../../assets/";
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
            $_SESSION['messaggioCaricaNFT']=$avvisoCaricaNFT;
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
        aggiungiOpera($username,$database);
    }
    header('Location: ../../../500.php');
    exit;
    
}else{
    header('Location: ../../../accedi.php');
    exit;
}
