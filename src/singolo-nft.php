<?php

require_once "./php/Database.php";

$paginaHTML = file_get_contents('./static/singolo-nft.html');
$database = new Database();
$connessioneOK = $database->openConnection();

$opera_html='';
$descr_html='';
$recensioni_html='';

if (!$connessioneOK) {
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


}else{

}

$find=['{{OPERA}}','{{DESCRIZIONE}}','{{RECENSIONI}}'];
$replacement=[$opera_html,$descr_html,$recensioni_html];
echo str_replace($find, $replacement, $paginaHTML);
