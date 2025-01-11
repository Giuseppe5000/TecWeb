<?php

function makeMessageParagraph($str) {
    return '<p class="messaggi-form">' . $str . '</p>';
}

function trimName($nome,$max=10){
    if(strlen($nome)>$max){
        $nome=substr($nome,0,($max-3));
        $nome.='...';
    }
    return $nome;
}

function generatePageNumber($pageNumber) {
    if (isset($_GET['page']))
        return preg_replace("/page=(\d)*/", "page={$pageNumber}", $_SERVER['QUERY_STRING']);
    return $queryString = $_SERVER['QUERY_STRING'] . "&page={$pageNumber}";
}

function getOrdinaSelectOption($value, $displayName, $ordina) {
    if ($value == $ordina)
        return '<option value="'. $value .'" selected>'. $displayName.'</option>';
    else
        return '<option value="'. $value .'">' . $displayName . '</option>';
}
