<?php

class Navbar {
    private $currentLink;
    private const NAVBAR_FILE = "./static/navbar.txt";
    private const MENU_ITEMS_UNLOGGED = array("Home", "NFT", "Chi siamo", "Registrati", "Accedi");
    private const MENU_ITEMS_LOGGED = array("Home", "NFT", "Chi siamo", "Disconnettiti", "Profilo");

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

    private function substitute($allowed_items, $line) {
        $fields = explode(",", $line);

        $title = $fields[0];
        $href = $fields[1];
        $id = $fields[2];
        $lang = $fields[3];
        $abbrev = $fields[4];

        if (in_array($title, $allowed_items)) {
            if ($title == $this->currentLink)
                return $this->getMenuItemCurrentLink($title, $id, $lang, $abbrev);
            else
                return $this->getMenuItemLink($title, $href, $id, $lang, $abbrev);
        }
        else {
            return "";
        }
    }


    private function getUnlogged() {
        $navbar = "";
        foreach(file(Navbar::NAVBAR_FILE) as $line) {
            $navbar .= $this->substitute(Navbar::MENU_ITEMS_UNLOGGED, $line);
        }
        return $navbar;
    }

    private function getLogged() {
        $navbar = "";
        foreach(file(Navbar::NAVBAR_FILE) as $line) {
            $navbar .= $this->substitute(Navbar::MENU_ITEMS_LOGGED, $line);
        }
        return $navbar;
    }

    public function getNavbar() {
        if (isset($_SESSION['username']))
            return $this->getLogged();
        return $this->getUnlogged();
    }

}
