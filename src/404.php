<?php

require_once "./php/Navbar.php";
session_start();

$navbar = new Navbar("404");
$paginaHTML = file_get_contents('./static/404.html');
$find=['{{NAVBAR}}'];
$replacement=[$navbar->getNavbar()];

echo str_replace($find, $replacement, $paginaHTML);