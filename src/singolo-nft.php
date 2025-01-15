<?php
session_start();

require_once "./php/Database.php";
require_once "./php/Navbar.php";
require_once "./php/utils.php";

function getRecensioni($recensioni, $pageNumber, $pageSize) {
    $recensioni_html = "";
    if(count($recensioni)>0){
        $previousPages = $pageNumber*$pageSize;
        for ($i = $previousPages; $i < $previousPages + $pageSize && $i < count($recensioni); $i++) {
            $recensione = $recensioni[$i];
            $voto = $recensione["voto"];
            $commento = $recensione["commento"];
            $utente = $recensione["utente"];
            $date = strtotime($recensione["timestamp"]);
            $date = date('d-m-Y',$date);
            $recensioni_html.='<div class="comment">';
            $recensioni_html.='<div class="head-comment">';
            $recensioni_html.='<div class="user-comment">';
            $recensioni_html.='<img class="logo_utente" src="assets/user.svg" alt="Logo profilo utente"/>';
            $recensioni_html.='<span>'.$utente.'</span>';
            $recensioni_html.='</div>';
            $recensioni_html .= '<div><span>' . $recensione["voto"] .' &#9733;</span></div>';
            $recensioni_html.= "<div>{$date}</div>";
            $recensioni_html.='</div>';
            $recensioni_html.='<p>'.$commento.'</p>';
            $recensioni_html.='</div>';
        }
        return $recensioni_html;
    }
}

function mostraAggiungiRecensione(&$aggiungi_recensione_html, $id) {
    $aggiungi_recensione_html.='<nav aria-label="aiuti alla navigazione" class="listHelp">
	<a href="#recensioni" class="navigationHelp">Vai alle recensioni</a>
      </nav>';
    $aggiungi_recensione_html.='<form id="agg-recensione" class="user-form" action="php/post/recensione/aggiungi-recensione.php" method="post">';
    $aggiungi_recensione_html.='<fieldset>';
    $aggiungi_recensione_html.='<legend>Aggiungi recensione</legend>';
    $aggiungi_recensione_html.= '<fieldset id="stelle-recensione">';
    $aggiungi_recensione_html.= '<legend>Dai un voto in stelle</legend>';
    $aggiungi_recensione_html.= '<input type="radio" id="voto-1" name="voto" value="1" checked/>';
    $aggiungi_recensione_html.= '<label class="star" for="voto-1">&#9733;</label>';
    $aggiungi_recensione_html.= '<input type="radio" id="voto-2" name="voto" value="2"/>';
    $aggiungi_recensione_html.= '<label class="star" for="voto-2">&#9733;</label>';
    $aggiungi_recensione_html.= '<input type="radio" id="voto-3" name="voto" value="3"/>';
    $aggiungi_recensione_html.= '<label class="star" for="voto-3">&#9733;</label>';
    $aggiungi_recensione_html.= '<input type="radio" id="voto-4" name="voto" value="4"/>';
    $aggiungi_recensione_html.= '<label class="star" for="voto-4">&#9733;</label>';
    $aggiungi_recensione_html.= '<input type="radio" id="voto-5" name="voto" value="5"/>';
    $aggiungi_recensione_html.= '<label class="star" for="voto-5">&#9733;</label>';
    $aggiungi_recensione_html.= '</fieldset>';
    $aggiungi_recensione_html.='<label for="recensione">Recensione:</label>';
    $aggiungi_recensione_html.='<textarea id="recensione" name="recensione" required></textarea>';
    $aggiungi_recensione_html.='<input type="hidden" name="id" value="'.$id.'"/>';
    $aggiungi_recensione_html.='<input type="hidden" name="currentPage" value="'.$_SERVER["PHP_SELF"].'?'.$_SERVER['QUERY_STRING'].'"/>';
    $aggiungi_recensione_html.='<input type="submit" value="Aggiungi" class="button" name="aggiungi"></input>';
    $aggiungi_recensione_html.='</fieldset>';
    $aggiungi_recensione_html.='</form>';
}

$paginaHTML = file_get_contents('./static/singolo-nft.html');
$database = new Database();
$connessioneOK = $database->openConnection();
$username='';
$id='';

$opera_html='';
$descr_html='';
$recensioni_html='';
$aggiungi_recensione_html='';
$acquisto_res='';

$pageSize = 4;
$pageNumber = 0;
if (isset($_GET['page']))
    $pageNumber = intval($_GET['page']);
$recensioniDaMostrare = 0;

if (!$connessioneOK) {

    #richiesta get e salvo il parametro di sessione
    if(isset($_GET['id'])) {
        $id=$_GET["id"];
    }

    #Se acquista l'opera
    if(isset($_SESSION['acquistato'])){
        if($_SESSION['acquistato']){
            $acquisto_res.='<p class="center">Opera acquistata con successo!</p>';
        }else{
            $acquisto_res.='<p class="center">Non hai abbastanza ETH per acquistare l\'opera!</p>';
        }
        unset($_SESSION['risultato_acquisto']);
    }
    
    #QUERY
    $query='SELECT * FROM opera WHERE id='.$id;
    $opera=$database->executeQuery($query);

    if(count($opera)==1){
        $nome_opera=$opera[0]["nome"];
        
        $prezzo=$opera[0]["prezzo"];
        $possessore=$opera[0]["possessore"];

        $opera_html.='<h1>'.$nome_opera.'</h1>';
        $opera_html.='<div>';
        $opera_html.='<img id="immagine-contenuto" src="./'.$opera[0]["path"].'.webp" alt="decr-img">';
        $opera_html.='</div>';
        $opera_html.='<span>Prezzo: '.$prezzo.'ETH</span>';
        $descr_html.='<p id="descr">'.$opera[0]["descrizione"].'</p>';

        #se l'opera è acquista si vedrà da chi è stata acquistata, per essere acquistata il possessore deve essere DIVERSO da admin
        if(strcmp($possessore,'admin')!=0){
            if(!isset($_SESSION['username']) || strcmp($possessore,$_SESSION['username'])!=0){
                $opera_html.='<p class="center">L\'opera è stata acquistata da: '.$possessore.'</p>';
            }else{
                $opera_html.='<p class="center">L\'opera è in tuo possesso!</p>';
            }
        }else{
            #se l'utente è loggato vede il bottone acquista
            if(isset($_SESSION['username'])){
                if(strcmp($_SESSION['username'],'admin')!=0){
                    $opera_html.='<nav aria-label="aiuti alla navigazione" class="listHelp">
	<a href="#recensione" class="navigationHelp">Vai ad aggiungi recensioni</a>
      </nav>';
                    $opera_html.='<form id="acq-nft" action="php/post/opera/acquisto.php" method="post">';
                    $opera_html.='<input type="hidden" name="id" value="'.$id.'"/>';
                    $opera_html.='<input type="hidden" name="prezzo" value="'.$prezzo.'"/>';
                    $opera_html.='<input type="hidden" name="currentPage" value="'.$_SERVER["PHP_SELF"].'?'.$_SERVER['QUERY_STRING'].'"/>';
                    $opera_html.='<input type="submit" value="Acquista" class="button" name="acquista">';
                    $opera_html.='</form>';
                }else{
                    $opera_html.='<p class="center">L\'opera non è ancora stata acquista</p>';
                }
            }else{
                $opera_html.='<p class="center">Se vuoi acquistare l\'opera <a href="accedi.php">Accedi</a> al tuo profilo</p>';
            }
        }
    }

    $query='SELECT * FROM recensione WHERE opera="' . $id . '" ORDER BY timestamp DESC';
    $recensioni=$database->executeQuery($query);
    $database->closeConnection();
    $recensioni_html = getRecensioni($recensioni, $pageNumber, $pageSize);
    $recensioniDaMostrare = count($recensioni) - $pageNumber*$pageSize - $pageSize;
}

#SE LOGGATO
if(isset($_SESSION['username'])){
    mostraAggiungiRecensione($aggiungi_recensione_html, $id);
}else{
    $aggiungi_recensione_html.='<p class="center">Se vuoi aggiungere una recensione <a href="accedi.php">Accedi</a> al tuo profilo</p>';
}

$linkPaginaPrecedente = "";
if ($pageNumber > 0) {
    $prevPageNumber = $pageNumber - 1;
    $queryString = generatePageNumber($prevPageNumber);
    if(isset($_GET['id']))
        $linkPaginaPrecedente =  "<a class='prev-page' href=\"singolo-nft.php?{$queryString}\">&#10094;</a>";
    else
        $linkPaginaPrecedente =  "<a class='prev-page' href=\"singolo-nft.php?id={$id}{$queryString}\">&#10094;</a>";
}

$linkPaginaSuccessiva = "";
if ($recensioniDaMostrare > 0) {
    $nextPageNumber = $pageNumber + 1;
    $queryString = generatePageNumber($nextPageNumber);
    if(isset($_GET['id']))
        $linkPaginaSuccessiva = "<a class='next-page' href=\"singolo-nft.php?{$queryString}\">&#10095;</a>";
    else
        $linkPaginaSuccessiva = "<a class='next-page' href=\"singolo-nft.php?id={$id}{$queryString}\">&#10095;</a>";
}

$navbar = new Navbar("");

$find=['{{OPERA}}','{{DESCRIZIONE}}','{{RECENSIONI}}','{{AGGIUNGI_RECENSIONE}}','{{ACQUISTO_RES}}', '{{NAVBAR}}','{{NOME_NFT}}', '{{PAGINA_PRECEDENTE}}', '{{PAGINA_SUCCESSIVA}}', '{{PAGINA_CORRENTE}}'];
$replacement=[$opera_html,$descr_html,$recensioni_html,$aggiungi_recensione_html,$acquisto_res, $navbar->getnavbar(),$nome_opera,$linkPaginaPrecedente, $linkPaginaSuccessiva,"<span class='page-number'>Pagina: {$pageNumber}</span>"];
echo str_replace($find, $replacement, $paginaHTML);
