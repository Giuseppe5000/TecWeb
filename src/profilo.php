<?php

require_once "./php/Database.php";
require_once "./php/Navbar.php";
session_start();

$saldo = "";
$nftPosseduti = ""; 
$avviso = "";

function pulisciInput($value)
{
 	$value = trim($value);
  	$value = strip_tags($value);
	$value = htmlentities($value);
  	return $value;
}

if(isset($_SESSION['username'])){
    $database = new Database();
    $connessioneOK = $database->openConnection();
    $username = $_SESSION['username'];

    if(!$connessioneOK){
        // Se il form di aggiunta nuovo nft è stato inviato
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
            // Recupera i valori dal form
            $nome = pulisciInput($_POST['nome']);
            $descrizione = pulisciInput($_POST['descrizione']);
            $prezzo = pulisciInput($_POST['prezzo']);

            // Gestione upload immagine
            $target_dir = "./assets/";
            $target_file = $target_dir . basename($_FILES["immagine"]["name"]);
            $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
            $check = getimagesize($_FILES["immagine"]["tmp_name"]);

            if($check === false) {
                $avviso .= "<p>Il file non è un'immagine.</p>";
            } elseif (file_exists($target_file)) {
                $avviso .= "<p>Nome del file non valido</p>";
            } elseif ($_FILES["immagine"]["size"] > 500000) {
                $avviso .= "<p>Il file è troppo grande.</p>";
            } elseif($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" && $imageFileType != "webp") {
                $avviso .= "<p>Sono permessi solo file JPG, JPEG, PNG & GIF.</p>";
            } else {           
                if (strlen($nome) == 0 || strlen($descrizione) == 0 || strlen($prezzo) == 0) {
                $avviso .= "<p>Compila tutti i campi.</p>";
                } else {
                    if (move_uploaded_file($_FILES["immagine"]["tmp_name"], $target_file)) {
                $query = "INSERT INTO opera (path, nome, descrizione, prezzo) VALUES (?, ?, ?, ?)";
                $stmt = $database->getConnection()->prepare($query);
                if (!$stmt) {
                    throw new PrepareStatementException($database->getConnection()->error);
                }
                        $path = $target_dir . pathinfo($_FILES["immagine"]["name"], PATHINFO_FILENAME);
                        $stmt->bind_param('sssd', $path, $nome, $descrizione, $prezzo);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $avviso .= "<p>NFT aggiunto con successo!</p>";
                } else {
                    $avviso = "<p>Errore durante l'aggiunta dell'NFT.</p>";
                }

                $stmt->close();
                    } else {
                        $avviso .= "<p>Errore durante il caricamento dell'immagine.</p>";
                    }
                } 
            }
        }

        // Query per ottenere il username e il saldo dell'utente, se l'utente è amministratore compare il form di insermento di una nuova opera
        $query  = "SELECT saldo, isAdmin FROM utente WHERE username = ?";
        $value = array($username);
        $result = $database->executeSelectPreparedStatement($query,'s',$value);
        if(count($result) > 0){
            foreach($result as $row){
                $saldo = "<span>" . $username . "</span>
                <span>Saldo: " . $row['saldo'] . "</span>";
                if($row['isAdmin']){
                    $saldo .= $avviso;
                    $saldo .= "<form id='add-nft' class='user-form' action='profilo.php' method='post' enctype='multipart/form-data'>
                        <fieldset>
                        <legend>Aggiungi NFT</legend>
                        <label for='immagine'>Immagine:</label>
                        <input type='file' id='immagine' name='immagine' required>

                        <label for='nome'>Nome:</label>
                        <input type='text' id='nome' name='nome' maxlength='30' required>

                        <label for='descrizione'>Descrizione:</label>
                        <input type='text' id='descrizione' name='descrizione' maxlength='150' required>

                        <label for='prezzo'>Prezzo:</label>
                        <input type='number' id='prezzo' name='prezzo' step='0.001' min='0' required>

                        <input type='submit' value='Aggiungi' class='button' name='submit'>
                        </fieldset>
                      </form>";
                }
            }
        }

        // Query per ottenere le opere possedute dall'utente
        $query  = "SELECT * FROM opera WHERE possessore = ?";
        $value = array($username);
        $result = $database->executeSelectPreparedStatement($query,'s',$value);
        $database->closeConnection();

        if(count($result) == 0){
            $nftPosseduti = "<p>Non possiedi ancora nessun NFT</p>";
        }
        else{
            foreach($result as $row){
                $nftPosseduti .= '<div class="card">';
                $nftPosseduti .= '<a href="singolo-nft.php?id='.$row["id"].'">';
                $nftPosseduti .= '<h3>' . $row["nome"]  . '</h3>';
                $nftPosseduti .= '<img src="./' . $row["path"] . '.webp" width="140" height="140">';
                $nftPosseduti .= '</a>';
                $nftPosseduti .= '</div>';
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

$find=['{{SALDO}}','{{CARDS}}', '{{NAVBAR}}'];
$replacemenet=[$saldo,$nftPosseduti, $navbar->getNavbar()];
echo str_replace($find,$replacemenet,$paginaHTML);
