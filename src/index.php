<?php

require_once "./php/Database.php";

$paginaHTML = file_get_contents('./static/index.html');
$ultimeUscite = '';
$top3 = '';
$categorie = '';

$database = new Database();
$connessioneOK = $database->openConnection();

if (!$connessioneOK) {

  #ULTIME USCITE
  $opere = $database->getUltimeUscite();
  $database->closeConnection();

  if (count($opere) > 0) {
    foreach ($opere as $opera) {
        $ultimeUscite .= '<div class="card">';
        $ultimeUscite .= '<a href="singolo-nft.html">';
        $ultimeUscite .= '<h3>' . $opera["path"]  . '</h3>';
        $ultimeUscite .= '<img src="./' . $opera["path"] . '.webp" width="200" height="200">';
        $ultimeUscite .= '</a>';
        $ultimeUscite .= '</div>';
    }
  }

  #TOP 3

  #CATEGORIE

} else {
  $ultimeUscite = '<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>';
}

echo str_replace('{{ULTIME_USCITE}}', $ultimeUscite, $paginaHTML);
