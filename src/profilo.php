<?php

session_start();

require_once "./php/Database.php";

$paginaHTML = file_get_contents('./static/profilo.html');

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
            $pathImmagine = pulisciInput($_POST['path-immagine']);
            $nome = pulisciInput($_POST['nome']);
            $descrizione = pulisciInput($_POST['descrizione']);
            $prezzo = pulisciInput($_POST['prezzo']);

            if (strlen($pathImmagine) == 0 || strlen($nome) == 0 || strlen($descrizione) == 0 || strlen($prezzo) == 0) {
                $avviso .= "<p>Compila tutti i campi.</p>";
            } 
            else {
                $query = "INSERT INTO opera (path, nome, descrizione, prezzo) VALUES (?, ?, ?, ?)";
                $stmt = $database->getConnection()->prepare($query);
                if (!$stmt) {
                    throw new PrepareStatementException($database->getConnection()->error);
                }
                $stmt->bind_param('sssd', $pathImmagine, $nome, $descrizione, $prezzo);
                $stmt->execute();

                if ($stmt->affected_rows > 0) {
                    $avviso .= "<p>NFT aggiunto con successo!</p>";
                } else {
                    $avviso = "<p>Errore durante l'aggiunta dell'NFT.</p>";
                }

                $stmt->close();
            }
        }

        // Query per ottenere il username e il saldo dell'utente, se l'utente è amministratore compare il form di insermento di una nuova opera
        $query  = "SELECT saldo, isAdmin FROM utente WHERE username = ?";
        $value = array($username);
        $result = $database->executePreparedStatement($query,'s',$value);
        if(count($result) > 0){
            foreach($result as $row){
                $saldo = "<span>" . $username . "</span>
                <span>Saldo: " . $row['saldo'] . "</span>";
                if($row['isAdmin']){
                    $saldo .= $avviso;
                    $saldo .= "<form id='add-nft' class='user-form' action='profilo.php' method='post'>
                        <fieldset>
                        <legend>Aggiungi NFT</legend>
                        <label for='path-immagine'>Path immagine:</label>
                        <input type='text' id='path-immagine' name='path-immagine' maxlength='30' required>

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
        $result = $database->executePreparedStatement($query,'s',$value);
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

$find=['{{SALDO}}','{{CARDS}}'];
$replacemenet=[$saldo,$nftPosseduti];
echo str_replace($find,$replacemenet,$paginaHTML);
