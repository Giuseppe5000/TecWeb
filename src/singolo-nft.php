<?php
session_start();

require_once "./php/Database.php";

$paginaHTML = file_get_contents('./static/singolo-nft.html');
$database = new Database();
$connessioneOK = $database->openConnection();
$username=$_SESSION['username'];
$id='';

$opera_html='';
$descr_html='';
$recensioni_html='';
$aggiungi_recensione_html='';



if (!$connessioneOK) {
    #Se aggiunge una recensione
    if (isset($_POST['submit'])) {
        //recupera valori del form
        $voto=$database->pulisciInput($_POST['voto']);
        $recensione=$database->pulisciInput($_POST['recensione']);
        
        $query = "INSERT INTO recensione (timestamp, utente, commento, opera, voto) VALUES (?, ?, ?, ?, ?)";
        
        $queryString = $_SERVER['QUERY_STRING'];
        $value = array(date("Y-m-d h:i:s"),$username, $recensione, (int)$id, $voto);
        #$database->executeStatement($query,'sssii',$value);
        #$location="./singolo-nft.php?id=$id";
        #header("Location:$location");
    }else{
        $id=$_GET["id"];
        #QUERY
        $query='SELECT * FROM opera WHERE id='.$_GET["id"];
        $opera=$database->executeQuery($query);

        $query='SELECT * FROM recensione WHERE opera='.$_GET["id"];
        $recensioni=$database->executeQuery($query);

        if(count($opera)==1){
            $opera_html.='<h1>'.$opera[0]["nome"].'</h1>';
            $opera_html.='<div>';
            $opera_html.='<img id="immagine-contenuto" src="./'.$opera[0]["path"].'.webp" alt="decr-img">';
            $opera_html.='</div>';
            $opera_html.='<span>Prezzo: '.$opera[0]["prezzo"].'ETH</span>';
            $descr_html.='<p id="descr">'.$opera[0]["descrizione"].'</p>';
        }

        if(count($recensioni)>0){
            #inserisco le recensioni
            foreach($recensioni as $recensione){
                $recensioni_html.='<div class="comment">';
                $recensioni_html.='<div class="head-comment">';
                $recensioni_html.='<div class="user-comment">';
                $recensioni_html.='<img class="logo_utente" src="assets/user.svg" alt="Logo profilo utente"/>';
                $recensioni_html.='<span>'.$recensione["utente"].'</span>';
                $recensioni_html.='</div>';
                $recensioni_html.='<span>'.$recensione["voto"].'</span>';
                $recensioni_html.='</div>';
                $recensioni_html.='<p>'.$recensione["commento"].'</p>';
                $recensioni_html.='</div>';
            }
        }

        #SE LOGGATO
        if(isset($_SESSION['username'])){
            $aggiungi_recensione_html.='<form id="agg-recensione" class="user-form" action="singolo-nft.php" method="post">';
            $aggiungi_recensione_html.='<fieldset>';
            $aggiungi_recensione_html.='<legend>Aggiungi recensione</legend>';
            $aggiungi_recensione_html.='<label for="voto">Voto:</label>';
            $aggiungi_recensione_html.='<input type="number" id="voto" name="voto" min="1" max="5" required/>';
            $aggiungi_recensione_html.='<label for="recensione">Recensione:</label>';
            $aggiungi_recensione_html.='<textarea id="recensione" name="recensione" required></textarea>';
            $aggiungi_recensione_html.='<input type="submit" value="Aggiungi" class="button" name="submit"></input>';
            $aggiungi_recensione_html.='</fieldset>';
            $aggiungi_recensione_html.='</form>';
        }else{
            $aggiungi_recensione_html.='<p>Se vuoi aggiungere una recensione <a href="accedi.php">Accedi</a> al tuo profilo</p>';
        }
    }

}else{

}

$find=['{{OPERA}}','{{DESCRIZIONE}}','{{RECENSIONI}}','{{AGGIUNGI_RECENSIONE}}'];
$replacement=[$opera_html,$descr_html,$recensioni_html,$aggiungi_recensione_html];
echo str_replace($find, $replacement, $paginaHTML);
