<?php

require_once "./php/Database.php";

$paginaHTML = file_get_contents('./static/index.html');
$ultimeUscite = '';
$top3 = '';
$categorieAcquistate = '';

$database = new Database();
$connessioneOK = $database->openConnection();

if (!$connessioneOK) {

  #QUERY AL DB
  $query = "SELECT * FROM opera ORDER BY id DESC LIMIT 10";
  $opereU = $database->executeQuery($query);

  $query = "SELECT opera.*,AVG(r.voto) as media FROM opera JOIN recensione as r ON r.opera=opera.id GROUP BY opera.id ORDER BY media DESC LIMIT 3";
  $opereT = $database->executeQuery($query);

  $query = "SELECT a.categoria as categoria, COUNT(ac.opera) as opereA, COUNT(a.opera) as opereT FROM appartenenza as a LEFT JOIN acquisto as ac ON ac.opera=a.opera GROUP BY a.categoria ORDER BY opereA DESC,opereT DESC";
  $categorie = $database->executeQuery($query);
  $database->closeConnection();

  #INSERISCO ULTIME USCITE
  if (count($opereU) > 0) {
    foreach ($opereU as $opera) {
        $ultimeUscite .= '<div class="card">';
        $ultimeUscite .= '<a href="singolo-nft.php?id='.$opera["id"].'">';
        $ultimeUscite .= '<h3>' . $opera["nome"]  . '</h3>';
        $ultimeUscite .= '<img src="./' . $opera["path"] . '.webp" width="200" height="200">';
        $ultimeUscite .= '</a>';
        $ultimeUscite .= '</div>';
    }
  }

  #INSERISCO TOP 3
  $nTop=count($opereT);
  if ( $nTop > 0) {
      $count=1;
    foreach ($opereT as $opera) {
        switch($count){
        case 1:
            #inserisco il primo
            $top3 .= '<div class="card" id="primo">';
            $top3 .= '<a href="singolo-nft.php?id='.$opera["id"].'"><div class="head-card"><span>1°</span>';
            break;
        case 2:
            #inserisco il secondo
            $top3 .= '<div class="card" id="secondo">';
            $top3 .= '<a href="singolo-nft.php?id='.$opera["id"].'"><div class="head-card"><span>2°</span>';
            break;
        case 3:
            #inserisco il terzo
            $top3 .= '<div class="card" id="terzo">';
            $top3 .= '<a href="singolo-nft.php?id='.$opera["id"].'"><div class="head-card"><span>3°</span>';
            break;
        }
        $top3 .= '<h3>' . $opera["nome"]  . '</h3></div>';
        $top3 .= '<img src="./' . $opera["path"] . '.webp" width="200" height="200">';
        $top3 .= '</a>';
        $top3 .= '</div>';

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
          $categorieAcquistate.='<tr>';
          $categorieAcquistate.='<th scope="row" lang="en">'.$categoria["categoria"].'</th>';
          $categorieAcquistate.='<td>'.$categoria["opereA"].'</td>';
          $categorieAcquistate.='<td>'.$categoria["opereT"].'</td>';
          $categorieAcquistate.='</tr>';
      }
  }

} else {
  $ultimeUscite = '<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>';
  $top3 = '<p>I sistemi sono momentaneamente fuori servizio, ci scusiamo per il disagio.</p>';
}

$find=['{{ULTIME_USCITE}}','{{TOP3}}','{{CATEGORIE}}'];
$replacement=[$ultimeUscite,$top3,$categorieAcquistate];
echo str_replace($find, $replacement, $paginaHTML);
