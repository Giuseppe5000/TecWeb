<?php

require_once "./php/Database.php";
require_once "./php/Navbar.php";
require_once "./php/utils.php";
session_start();

$saldo = "";
$nftPosseduti = ""; 
$avviso = "";

function generateUniqueFilename($extension) {
    $uniqueId = substr(uniqid(), -5); //Prendo solo gli ultimi 5 perchè senno è troppo lungo
    return "nft" . $uniqueId . "." . $extension;
}

if(isset($_SESSION['username'])){
    $database = new Database();
    $connessioneOK = $database->openConnection();
    $username = $_SESSION['username'];

    if(!$connessioneOK){
        // Se il form di aggiunta nuovo nft è stato inviato
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
            $nome = $database->pulisciInput($_POST['nome']);
            $descrizione = $database->pulisciInput($_POST['descrizione']);
            $prezzo = $database->pulisciInput($_POST['prezzo']);
        
            $target_dir = "./assets/";
            $imageFileType = strtolower(pathinfo($_FILES["immagine"]["name"], PATHINFO_EXTENSION));
            $target_file = $target_dir . generateUniqueFilename($imageFileType);
        
            if ($_FILES["immagine"]["size"] > 500000) {
                $avviso .= "<p>L'immagine è di dimensioni troppo grandi.</p>";
            } elseif ($imageFileType !== "webp") {
                $avviso .= "<p>Sono permessi solo immagini in formato WebP.</p>";
            } elseif (empty($nome) || empty($descrizione) || empty($prezzo)) {
                $avviso .= "<p>Compila tutti i campi.</p>";
            } elseif (!move_uploaded_file($_FILES["immagine"]["tmp_name"], $target_file)) {
                $avviso .= "<p>Errore durante il caricamento dell'immagine.</p>";
            } else {
                $path = rtrim($target_file, '.webp');
                $query = "INSERT INTO opera (path, nome, descrizione, prezzo) VALUES (?, ?, ?, ?)";
                $format_string = 'sssd';
                $values = [$path, $nome, $descrizione, $prezzo];
                $avviso = $database->executeCRUDPreparedStatement($query, $format_string, $values);
        
                if (strpos($avviso, 'successo') !== false && !empty($_POST['categorie']) && is_array($_POST['categorie'])) {
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
                        <input type='file' id='immagine' name='immagine' accept='image/*' required>

                        <label for='nome'>Nome:</label>
                        <input type='text' id='nome' name='nome' maxlength='30' required>

                        <label for='descrizione'>Descrizione:</label>
                        <input type='text' id='descrizione' name='descrizione' maxlength='150' required>

                        <label for='prezzo'>Prezzo:</label>
                        <input type='number' id='prezzo' name='prezzo' step='0.001' min='0' required>

                        <fieldset id='categorie'>
                        <legend>Categorie</legend>
                        <div>
                            <input type='checkbox' id='Abstract' name='categorie[]' value='Abstract'/>
                            <label for='Abstract'>Abstract</label>
                        </div>
                        <div>
                            <input type='checkbox' id='Animals' name='categorie[]' value='Animals'/>
                            <label for='Animals'>Animals</label>
                        </div>
                        <div>
                            <input type='checkbox' id='PixelArt' name='categorie[]' value='PixelArt'/>
                            <label for='PixelArt'>PixelArt</label>
                        </div>
                        <div>
                            <input type='checkbox' id='Black&White' name='categorie[]' value='Black&White'/>
                            <label for='Black&White'>Black&White</label>
                        </div>
                        <div>
                            <input type='checkbox' id='Photo' name='categorie[]' value='Photo'/>
                            <label for='Photo'>Photo</label>
                        </div>
                        </fieldset>

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
                $nftPosseduti .= '<h3>' . trimName($row["nome"])  . '</h3>';
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
