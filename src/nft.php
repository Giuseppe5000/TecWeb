<?php

require_once "./php/Database.php";

$paginaHTML = file_get_contents('./static/nft.html');
$stringaOpere = '';

$database = new Database();
$connessioneOK = $database->openConnection();

if (!$connessioneOK) {
  $opere = $database->getOpere();
  $database->closeConnection();

  if (count($opere) > 0) {
    foreach ($opere as $opera) {
        $stringaOpere .= '<div class="card">';
        $stringaOpere .= '<a href="nft.html?nft=TITOLO">';
        $stringaOpere .= '<h2>' . $opera["path"]  . '</h2>';
        $stringaOpere .= '</a>';
        $stringaOpere .= '<img src="./' . $opera["path"] . '.webp" width="200" height="200">';
        $stringaOpere .= '</div>';
    }
  }

} else {
  $stringaOpere = "<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>";
}

echo str_replace('{{OPERE}}', $stringaOpere, $paginaHTML);
