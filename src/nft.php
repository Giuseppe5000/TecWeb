<?php

require_once "./dbConnection.php";
use DB\DBAccess;

$paginaHTML = file_get_contents('./nft.html');
$stringaOpere = '';

$connessione = new DBAccess();
$connessioneOK = $connessione->openDBConnection();

if (!$connessioneOK) {
  $opere = $connessione->getOpere();
  $connessione->closeConnection();

  if (count($opere) > 0) {
    foreach ($opere as $opera) {
        $stringaOpere .= '<div class="card">';
        $stringaOpere .= '<a href="nft.html?nft=TITOLO">';
        $stringaOpere .= '<h2>' . $opera["path"]  . '</h2>';
        $stringaOpere .= '</a>';
        $stringaOpere .= '<img src="../' . $opera["path"] . '.png" width="200" height="200">';
        $stringaOpere .= '</div>';
    }
  }

} else {
  $connessione->closeConnection();
  $stringaOpere = "<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>";
}

echo str_replace('{{OPERE}}', $stringaOpere, $paginaHTML);
