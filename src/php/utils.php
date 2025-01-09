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
