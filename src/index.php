<?php

require_once "./php/Database.php";
require_once "./php/Navbar.php";
require_once "./php/CardOpera.php";
require_once "./php/utils.php";
session_start();

$paginaHTML = file_get_contents('./static/index.html');
$ultimeUscite = '';
$top3 = '';
$categorieAcquistate = '';

$database = new Database();
$connessioneOK = $database->openConnection();

function getRequestCategoryName($categoria) {
    switch($categoria) {
    case "Abstract":
        return "abstract";
    case "Animals":
        return "animals";
    case "Black&White":
        return "blackAndWhite";
    case "Photo":
        return "photo";
    case "PixelArt":
        return "pixelArt";
    default:
        throw new Exception("Unreachable");
    }
}

if (!$connessioneOK) {

    #QUERY AL DB
    $query = "SELECT * FROM opera ORDER BY id DESC LIMIT 4";
    $opereU = $database->executeQuery($query);

    $query = "SELECT opera.*,AVG(r.voto) as media FROM opera JOIN recensione as r ON r.opera=opera.id GROUP BY opera.id ORDER BY media DESC LIMIT 3";
    $opereT = $database->executeQuery($query);

    $query = "SELECT a.categoria as categoria, COUNT(ac.opera) as opereA, COUNT(a.opera) as opereT FROM appartenenza as a LEFT JOIN acquisto as ac ON ac.opera=a.opera GROUP BY a.categoria ORDER BY opereA DESC,opereT DESC";
    $categorie = $database->executeQuery($query);
    $database->closeConnection();

    #INSERISCO ULTIME USCITE
    if (count($opereU) > 0) {
        foreach ($opereU as $opera) {
            $card = new CardOpera($opera, 17);
            $ultimeUscite .= $card->getHomeCard();
        }
    }

    #INSERISCO TOP 3
    $nTop=count($opereT);
    if ( $nTop > 0) {
        $count=1;
        foreach ($opereT as $opera) {
            switch($count){
            case 1:
                $card = new CardOpera($opera, 15);
                $top3 .= $card->getHomeTopCard(1);
                break;
            case 2:
                $card = new CardOpera($opera, 15);
                $top3 .= $card->getHomeTopCard(2);
                break;
            case 3:
                $card = new CardOpera($opera, 15);
                $top3 .= $card->getHomeTopCard(3);
                break;
            }

            #se sono presenti poche opere con recensioni mostra solo quelle con le recensioni
            if($count<$nTop){
                $count++;
            }
        }
    }

    #INSERISCO LE CATEGORIE
    if(count($categorie)>0){
        #inserisco le categorie
        foreach($categorie as $categoria){
            $nomeCategoria = $categoria["categoria"];
            $hrefCategoria = getRequestCategoryName($nomeCategoria);

            $categorieAcquistate.='<tr>';
            $categorieAcquistate.='<th scope="row" lang="en"><a href="nft.php?nft=&prezzoMin=0&prezzoMax=100&ordina=prezzoCrescente&submit=Cerca&' . $hrefCategoria . '=on">' . $nomeCategoria . '</a></th>';
            $categorieAcquistate.='<td>'.$categoria["opereA"].'</td>';
            $categorieAcquistate.='<td>'.$categoria["opereT"].'</td>';
            $categorieAcquistate.='</tr>';
        }
    }

} else {
    $ultimeUscite = '<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>';
    $top3 = '<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>';
}

$navbar = new Navbar("Home");

$find=['{{ULTIME_USCITE}}','{{TOP3}}','{{CATEGORIE}}', '{{NAVBAR}}'];
$replacement=[$ultimeUscite,$top3,$categorieAcquistate, $navbar->getNavbar()];
echo str_replace($find, $replacement, $paginaHTML);
