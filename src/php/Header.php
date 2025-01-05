<?php

class Header {
    private $currentLink;
    private const HEADER_FILE = "static/header.html";
    private const MENU_ITEMS_UNLOGGED = array("Home", "NFT", "Chi siamo", "Registrati", "Accedi");
    private const MENU_ITEMS_LOGGED = array("Home", "NFT", "Chi siamo", "Disconnettiti", "Profilo");
    private const MENU_ITEMS_NUM = 7;


    public function __construct($currentLink){
        $this->currentLink = $currentLink;
    }

    private function getMenuItemLink($title, $href, $id, $lang="", $abbr="") {
        $link = '<li class="menu-item" {{LANG}}><a id="' . $id . '" href="' . $href . '">{{ABBR}}</a></li>';

        if ($lang != "it")
            $link = str_replace("{{LANG}}", 'lang="' . $lang .'"', $link);
        else
            $link = str_replace("{{LANG}}", '', $link);

        if ($abbr != "")
            $link = str_replace("{{ABBR}}", '<abbr lang="en" title="' . $abbr . '">' . $title . '</abbr>', $link);
        else
            $link = str_replace("{{ABBR}}", $title, $link);

        return $link;
    }

    private function getMenuItemCurrentLink($title, $id, $lang="", $abbr="") {
        $link = '<li class="menu-item" id="currentLink">{{ABBR}}</li>';

        if ($abbr != "")
            $link = str_replace("{{ABBR}}", '<abbr id="' . $id . '"{{LANG}} title="' . $abbr . '">' . $title . '</abbr>', $link);
        else
            $link = str_replace("{{ABBR}}", '<span id="' . $id . ' "{{LANG}}>' . $title . '</span>', $link);

        if ($lang != "it")
            $link = str_replace("{{LANG}}", 'lang="' . $lang . '"', $link);
        else
            $link = str_replace("{{LANG}}", '', $link);

        return $link;
    }

    private function substitute($allowed_items, $header) {
        preg_match('/{{([^{}]*)}}/', $header, $matches);
        $matchWithCurly = $matches[0];
        $matchStrings = explode(",", $matches[1]);

        $title = $matchStrings[0];
        $href = $matchStrings[1];
        $id = $matchStrings[2];
        $lang = $matchStrings[3];
        $abbrev = $matchStrings[4];

        if (in_array($title, $allowed_items)) {
            if ($title == $this->currentLink)
                return str_replace($matchWithCurly, $this->getMenuItemCurrentLink($title, $id, $lang, $abbrev), $header);
            else
                return str_replace($matchWithCurly, $this->getMenuItemLink($title, $href, $id, $lang, $abbrev), $header);
        }
        else {
            return str_replace($matchWithCurly, "", $header);
        }
    }


    public function getUnlogged() {
        $header = file_get_contents('../static/header.html');
        for ($i=0; $i<Header::MENU_ITEMS_NUM; $i++) {
            $header = $this->substitute(Header::MENU_ITEMS_UNLOGGED, $header);
        }
        return $header;
    }

    public function getLogged() {
        $header = file_get_contents('../static/header.html');
        for ($i=0; $i<Header::MENU_ITEMS_NUM; $i++) {
            $header = $this->substitute(Header::MENU_ITEMS_LOGGED, $header);
        }
        return $header;
    }

}
