<?php

require_once "./php/Database.php";
require_once "./php/Navbar.php";
require_once "./php/utils.php";
require_once "./php/CardOpera.php";
session_start();

function getOpere($database) {
    $query = "SELECT * FROM opera WHERE possessore=?";
    $value = array($_SESSION['username']);
    return $database->executeSelectPreparedStatement($query,'s',$value);
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
    $connection = $database->getConnection();
    
    if (empty($categorie)){
        $query = "SELECT * FROM opera
                WHERE nome LIKE ?
                AND prezzo >= ?
                AND prezzo <= ?
                AND possessore = ? ";
        $query .= getOrderBy($ordina);
        $value = array($name, $prezzoMin, $prezzoMax, $_SESSION['username']);
        return $database->executeSelectPreparedStatement($query,'siis',$value);
        
    } else {
        $query = "SELECT * FROM opera, appartenenza
                WHERE opera.id = appartenenza.opera
                AND nome LIKE ?
                AND prezzo >= ?
                AND prezzo <= ?
                AND possessore = ? ";
        $placeholders = implode(',', array_fill(0, count($categorie), '?'));
        $query .= "AND categoria IN ($placeholders)" . getOrderBy($ordina);
        
        $value = array($name, $prezzoMin, $prezzoMax, $_SESSION['username'], ...$categorie);
        return $database->executeSelectPreparedStatement($query,'siis'. str_repeat('s', count($categorie)),$value);
    }
}

function getOrFilter($database) {
    if(isset($_GET['submit'])){
        $name = $_GET['nft'];
        $prezzoMin = intval($_GET['prezzoMin']);
        $prezzoMax = intval($_GET['prezzoMax']);
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

function mostraOpere($opere, $pageNumber, $pageSize) {
    $stringaOpere = "";
    if (count($opere) > 0) {
        $previousPages = $pageNumber*$pageSize;
        for ($i = $previousPages; $i < $previousPages + $pageSize && $i < count($opere); $i++) {
            $opera = $opere[$i];
            $card = new CardOpera($opera);
            $stringaOpere .= $card->getMieiNFTCard();
        }
    }
    return $stringaOpere;
}

function setFormValues(&$nomeNft, &$prezzoMin, &$prezzoMax, &$ordinaPrezzo) {
    if(isset($_GET['submit'])){
        $nomeNft = $_GET['nft'];
        $prezzoMin = intval($_GET['prezzoMin']);
        $prezzoMax = intval($_GET['prezzoMax']);
        $ordinaPrezzo = $_GET['ordina'];
    }
}

function getOrdinaSelect($selectedValue) {
    return '<select id="ordina" class="filtro-aggiuntivo" name="ordina">
        <optgroup label="Prezzo"> '
        .getOrdinaSelectOption("prezzoCrescente", "Prezzo crescente", $selectedValue). ''
        .getOrdinaSelectOption("prezzoDescrescente", "Prezzo decrescente", $selectedValue).'
        </optgroup>

        <optgroup label="Nome"> '
        . getOrdinaSelectOption("nomeA-Z", "Nome A-Z", $selectedValue) . ''
        . getOrdinaSelectOption("nomeZ-A", "Nome Z-A", $selectedValue) . '
        </optgroup>
        </select>';
}
if(isset($_SESSION['username'])){
    $pageSize = 8;
    $pageNumber = 0;
    if (isset($_GET['page']))
        $pageNumber = intval($_GET['page']);
    
    $database = new Database();
    $connessioneOK = $database->openConnection();
    $stringaOpere = '';
    $opereDaMostrare = 0;
    

    $nomeNft = "";
    $prezzoMin = 0;
    $prezzoMax = 100;
    $ordinaPrezzo = "prezzoCrescente";
    
    $abstractCheckbox = "";
    $animalsCheckbox = "";
    $pixelArtCheckbox = "";
    $blackAndWhiteCheckbox = "";
    $photoCheckbox = "";
    
    if (isset($_GET['abstract']) && $_GET['abstract'] == "on")
        $abstractCheckbox = "checked";
    if (isset($_GET['animals']) && $_GET['animals'] == "on")
        $animalsCheckbox = "checked";
    if (isset($_GET['pixelArt']) && $_GET['pixelArt'] == "on")
        $pixelArtCheckbox = "checked";
    if (isset($_GET['blackAndWhite']) && $_GET['blackAndWhite'] == "on")
        $blackAndWhiteCheckbox = "checked";
    if (isset($_GET['photo']) && $_GET['photo'] == "on")
        $photoCheckbox = "checked";

    setFormValues($nomeNft, $prezzoMin, $prezzoMax, $ordinaPrezzo);
    $selectForm = getOrdinaSelect($ordinaPrezzo);

    if (!$connessioneOK) {
        $opere = getOrFilter($database);
        $database->closeConnection();
        $stringaOpere = mostraOpere($opere, $pageNumber, $pageSize);
        $opereDaMostrare = count($opere) - $pageNumber*$pageSize - $pageSize;
    } else {
        $stringaOpere = "<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>";
    }
    
    $linkPaginaPrecedente = "";
    if ($pageNumber > 0) {
        $prevPageNumber = $pageNumber - 1;
        $queryString = generatePageNumber($prevPageNumber);
        $linkPaginaPrecedente =  "<a class='prev-page' href=\"miei-nft.php?{$queryString}\">&#10094;</a>";
    }
    
    $linkPaginaSuccessiva = "";
    if ($opereDaMostrare > 0) {
        $nextPageNumber = $pageNumber + 1;
        $queryString = generatePageNumber($nextPageNumber);
        $linkPaginaSuccessiva = "<a class='next-page' href=\"miei-nft.php?{$queryString}\">&#10095;</a>";
    }
}
else{
    header('Location: ./accedi.php');
    exit;
}

$navbar = new Navbar("");

$paginaHTML = file_get_contents('./static/miei-nft.html');
$find=['{{OPERE}}', '{{PAGINA_PRECEDENTE}}', '{{PAGINA_SUCCESSIVA}}', '{{PAGINA_CORRENTE}}', '{{NAVBAR}}', '{{NOME_NFT}}', '{{PREZZO_MINIMO}}', '{{PREZZO_MASSIMO}}', '{{ORDINA}}', '{{ABSTRACT_CHECKED}}', '{{ANIMALS_CHECKED}}', '{{PIXELART_CHECKED}}', '{{BLACKANDWHITE_CHECKED}}', '{{PHOTO_CHECKED}}'];
$replacement=[$stringaOpere, $linkPaginaPrecedente, $linkPaginaSuccessiva, "<span class='page-number'>Pagina: {$pageNumber}</span>", $navbar->getNavbar(),$nomeNft, $prezzoMin, $prezzoMax, $selectForm,$abstractCheckbox, $animalsCheckbox, $pixelArtCheckbox, $blackAndWhiteCheckbox, $photoCheckbox];

echo str_replace($find, $replacement, $paginaHTML);
