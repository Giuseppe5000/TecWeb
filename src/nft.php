<?php

require_once "./php/Database.php";

function getOpere($database) {
    $query = "SELECT * FROM opera";
    return $database->executeQuery($query);
}

function getOrderBy($ordina) {
    switch($ordina) {
    case "prezzoCrescente":
        return "ORDER BY prezzo ASC";
    case "prezzoDecresente":
        return "ORDER BY prezzo DESC";
    case "nomeA-Z":
        return "ORDER BY nome ASC";
    case "nomeZ-A":
        return "ORDER BY nome DESC";
    default:
        return "";
    }
}

function getOpereFiltered($database, $name, $prezzoMin, $prezzoMax, $ordina, $categorie) {
    $name = "%" . $name . "%";
    echo implode(" ", $categorie);

    $query = "SELECT * FROM opera, appartenenza
            WHERE opera.id = appartenenza.opera
            AND nome LIKE ?
            AND prezzo >= ?
            AND prezzo <= ? ";

    $connection = $database->getConnection();

    if (empty($categorie)){
        $query .= getOrderBy($ordina);
        $stmt = $connection->prepare($query);
        if (!$stmt) throw new PrepareStatementException($connection->error);
        $stmt->bind_param('sii', $name, $prezzoMin, $prezzoMax);
        return $database->executePreparedStatement($stmt);
    } else {
        $placeholders = implode(',', array_fill(0, count($categorie), '?'));
        $query .= "AND categoria IN ($placeholders) " . getOrderBy($ordina);

        $stmt = $connection->prepare($query);
        if (!$stmt) throw new PrepareStatementException($connection->error);

        $stmt->bind_param('sii' . str_repeat('s', count($categorie)), $name, $prezzoMin, $prezzoMax, ...$categorie);
        return $database->executePreparedStatement($stmt);
    }

}

function getOrFilter($database) {
    if(isset($_GET['submit'])){
        $name = $_GET['nft'];
        $prezzoMin = (int)$_GET['prezzoMin'];
        $prezzoMax = (int)$_GET['prezzoMax'];
        $ordina = $_GET['ordina'];

        $categorie = array();
        if (isset($_GET['abstract']) && $_GET['abstract'] == "on")
            array_push($categorie, 'Abstract');
        if (isset($_GET['animals']) && $_GET['animals'] == "on")
            array_push($categorie, 'Animals');
        if (isset($_GET['pixelArt']) && $_GET['pixelArt'] == "on")
            array_push($categorie, 'PixelArt');
        if (isset($_GET['blackAndWhite']) && $_GET['blackAndWhite'] == "on")
            array_push($categorie, 'Black&White');
        if (isset($_GET['photo']) && $_GET['photo'] == "on")
            array_push($categorie, 'Photo');

        return getOpereFiltered($database, $name, $prezzoMin, $prezzoMax, $ordina, $categorie);
    }
    else {
        return getOpere($database);
    }
}


$paginaHTML = file_get_contents('./static/nft.html');
$stringaOpere = '';

$database = new Database();
$connessioneOK = $database->openConnection();

if (!$connessioneOK) {
    $opere = getOrFilter($database);
    $database->closeConnection();

    if (count($opere) > 0) {
        foreach ($opere as $opera) {
            $stringaOpere .= '<div class="card">';
            $stringaOpere .= '<a href="nft.html?nft=TITOLO">';
            $stringaOpere .= '<div class="head-card">';
            $stringaOpere .= '<h2>' . $opera["nome"]  . '</h2>';
            $stringaOpere .= '<span>' . $opera["prezzo"] .'</span>';
            $stringaOpere .= '</div>';
            $stringaOpere .= '<img src="./' . $opera["path"] . '.webp" width="200" height="200">';
            $stringaOpere .= '</a>';
            $stringaOpere .= '</div>';
        }
    }

} else {
    $stringaOpere = "<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>";
}

echo str_replace('{{OPERE}}', $stringaOpere, $paginaHTML);
