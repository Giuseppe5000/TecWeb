<?php

require_once "./php/Database.php";
require_once "./php/Navbar.php";
require_once "./php/utils.php";
session_start();

$recensioni_html = "";
$linkPaginaPrecedente ="";
$linkPaginaSuccessiva ="";

if(isset($_SESSION['username'])){
    $database = new Database();
    $connessioneOK = $database->openConnection();
    $username = $_SESSION['username'];


    $query  = "SELECT recensione.*, nome FROM recensione JOIN opera ON recensione.opera = opera.id WHERE utente = ? ORDER BY timestamp DESC";
    $value = array($username);
    $recensioni = $database->executeSelectPreparedStatement($query,'s',$value);
    $database->closeConnection();


    if(count($recensioni) == 0){
        $recensioni_html = "<p>Non hai ancora fatto alcuna recensione</p>";
    }else{
        foreach($recensioni as $recensione){
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
            $recensioni_html .= '<div>' . str_repeat('<span>&#9733;</span>', $recensione["voto"]) . '</div>';
            $recensioni_html.='<div class="user-comment">';
            $recensioni_html.= "{$date}";

            $recensioni_html.='<form class="form_recensione" action="modifica-recensione.php">';
            $recensioni_html.='<div>';
            $recensioni_html.='<input type="hidden" name="timestamp" value="'.$recensione["timestamp"].'"/>';
            $recensioni_html.='<input id="modifica" type="image" src="assets/edit_icon.svg" alt="modifica recensione" name="modifica">';
            $recensioni_html.='</div>';
            $recensioni_html.='</form>';

            $recensioni_html.='<form class="form_recensione" action="profilo.php" method="post">';
            $recensioni_html.='<div>';
            $recensioni_html.='<input type="hidden" name="timestamp" value="'.$recensione["timestamp"].'"/>';
            $recensioni_html.='<input id="cancella" type="image" src="assets/delete_icon.svg" alt="cancella recensione" name="cancella">';
            $recensioni_html.='</div>';
            $recensioni_html.='</form>';

            $recensioni_html.='</div>';
            $recensioni_html.='</div>';
            $recensioni_html.='<p>'.$recensione["commento"].'</p>';
            $recensioni_html.='</div>';
        }
    }


}
else{
    header('Location: ./accedi.php');
    exit;
}

$navbar = new Navbar("");
$paginaHTML = file_get_contents('./static/mie-recensioni.html');

$find=['{{NAVBAR}}','{{RECENSIONI}}','{{PAGINA_PRECEDENTE}}', '{{PAGINA_SUCCESSIVA}}', '{{PAGINA_CORRENTE}'];
$replacemenet=[$navbar->getNavbar(), $recensioni_html, $linkPaginaPrecedente, $linkPaginaSuccessiva, "<span class='page-number'>Pagina: {$pageNumber}</span>"];
echo str_replace($find,$replacemenet,$paginaHTML);
