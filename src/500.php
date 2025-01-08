<?php

require_once "./php/Navbar.php";
session_start();

$navbar = new Navbar("500");
$paginaHTML = file_get_contents('./static/500.html');
$find=['{{NAVBAR}}'];
$replacement=[$navbar->getNavbar()];

echo str_replace($find, $replacement, $paginaHTML);