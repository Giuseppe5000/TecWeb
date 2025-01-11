<?php

require_once "./php/Database.php";
require_once "./php/Navbar.php";
require_once "./php/utils.php";
session_start();


function getRecensioni($database){
    $query  = "SELECT recensione.*, nome FROM recensione JOIN opera ON recensione.opera = opera.id WHERE utente = ? ORDER BY timestamp DESC";
    $value = array($_SESSION['username']);
    return $database->executeSelectPreparedStatement($query,'s',$value);
}
function getOrderBy($ordina) {
    switch($ordina) {
    case "piuRecente":
        return "ORDER BY recensione.timestamp DESC";
    case "menoRecente":
        return "ORDER BY timestamp ASC";
    case "piuAlto":
        return "ORDER BY voto DESC";
    case "piuBasso":
        return "ORDER BY voto ASC";
    default:
        return "";
    }
}

function getRecensioniFiltered($database, $opera, $ordina) {
    $opera = "%" . $opera . "%";
    $connection = $database->getConnection();

    $query = "SELECT recensione.*, nome FROM recensione JOIN opera ON recensione.opera = opera.id WHERE utente = ? AND nome LIKE ? ";
    $query .= getOrderBy($ordina);
    $value = array($_SESSION['username'],$opera);
    return $database->executeSelectPreparedStatement($query,'ss',$value);
}

function getOrFilter($database, &$filtro_opera){
    if(isset($_GET['submit'])){
        $opera=$_GET['nft'];
        $filtro_opera=$opera;
        $ordina=$_GET['ordina'];

        return getRecensioniFiltered($database, $opera, $ordina);
    }else{
        return getRecensioni($database);
    }
}

function printRecensioni($recensioni, $pageNumber, $pageSize) {
    $recensioni_html = "";
    if(count($recensioni) == 0){
        $recensioni_html = "<p>Non hai ancora fatto alcuna recensione</p>";
    }else{
        $previousPages = $pageNumber*$pageSize;
        for ($i = $previousPages; $i < $previousPages + $pageSize && $i < count($recensioni); $i++) {
            $recensione=$recensioni[$i];
            $date = strtotime($recensione["timestamp"]);
            $date = date('d-m-Y',$date);
            $utente = $recensione["utente"];

            $recensioni_html.='<div class="comment">';
            $recensioni_html.='<div class="head-comment">';
            $recensioni_html.='<div class="user-comment">';
            $recensioni_html.= '<a href="singolo-nft.php?id=' . $recensione["opera"] . '">';
            $recensioni_html.='<span>'.$recensione["nome"].'</span>';
            $recensioni_html.= '</a>';
            $recensioni_html.='</div>';
            $recensioni_html .= '<div><span>' . $recensione["voto"] .' &#9733;</span></div>';
            $recensioni_html.= "{$date}";
            $recensioni_html.='<div class="user-comment">';
            $recensioni_html.='<form class="form_recensione" action="modifica-recensione.php">';
            $recensioni_html.='<div>';
            $recensioni_html.='<input type="hidden" name="timestamp" value="'.$recensione["timestamp"].'"/>';
            $recensioni_html.='<input id="modifica" type="image" src="assets/edit_icon.svg" alt="modifica recensione" name="modifica">';
            $recensioni_html.='</div>';
            $recensioni_html.='</form>';

            $recensioni_html.='<form class="form_recensione" action="cancella-recensione.php" method="post">';
            $recensioni_html.='<div>';
            $recensioni_html.='<input type="hidden" name="currentPage" value="'.$_SERVER["PHP_SELF"].'?'.$_SERVER['QUERY_STRING'].'"/>';
            $recensioni_html.='<input type="hidden" name="timestamp" value="'.$recensione["timestamp"].'"/>';
            $recensioni_html.='<input id="cancella" type="image" src="assets/delete_icon.svg" alt="cancella recensione" name="cancella">';
            $recensioni_html.='</div>';
            $recensioni_html.='</form>';

            $recensioni_html.='</div>';
            $recensioni_html.='</div>';
            $recensioni_html.='<p>'.$recensione["commento"].'</p>';
            $recensioni_html.='</div>';
        }
        return $recensioni_html;
    }
}

$recensioni_html = "";
$linkPaginaPrecedente ="";
$linkPaginaSuccessiva ="";
$pageSize = 3;
$pageNumber = 0;
if (isset($_GET['page']))
    $pageNumber = intval($_GET['page']);
$recensioniDaMostrare = 0;
$filtro_opera="";

if(isset($_SESSION['username'])){
    $database = new Database();
    $connessioneOK = $database->openConnection();
    $username = $_SESSION['username'];


    if(!$connessioneOK){

        $recensioni = getOrFilter($database,$filtro_opera);
        $database->closeConnection();
        $recensioni_html = printRecensioni($recensioni, $pageNumber, $pageSize);
        $recensioniDaMostrare = count($recensioni) - $pageNumber*$pageSize - $pageSize;
        
        
        if ($pageNumber > 0) {
            $prevPageNumber = $pageNumber - 1;
            $queryString = generatePageNumber($prevPageNumber);
            $linkPaginaPrecedente =  "<a class='prev-page' href=\"mie-recensioni.php?{$queryString}\">&#10094;</a>";
        }
        
        if ($recensioniDaMostrare > 0) {
            $nextPageNumber = $pageNumber + 1;
            $queryString = generatePageNumber($nextPageNumber);
            $linkPaginaSuccessiva = "<a class='next-page' href=\"mie-recensioni.php?{$queryString}\">&#10095;</a>";
        }
    }else{
        header('Location: ./500.php');
        exit;
    }

}
else{
    header('Location: ./accedi.php');
    exit;
}

$navbar = new Navbar("");
$paginaHTML = file_get_contents('./static/mie-recensioni.html');

$find=['{{NAVBAR}}','{{RECENSIONI}}','{{PAGINA_PRECEDENTE}}', '{{PAGINA_SUCCESSIVA}}', '{{PAGINA_CORRENTE}}', '{{OPERA}}'];
$replacement=[$navbar->getNavbar(), $recensioni_html, $linkPaginaPrecedente, $linkPaginaSuccessiva, "<span class='page-number'>Pagina: {$pageNumber}</span>", $filtro_opera];
echo str_replace($find,$replacement,$paginaHTML);
