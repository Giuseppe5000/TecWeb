<?php

require_once "./php/Navbar.php";
session_start();

$navbar = new Navbar("Chi siamo");
$paginaHTML = file_get_contents('./static/chi-siamo.html');
$find=['{{NAVBAR}}'];
$replacement=[$navbar->getNavbar()];

echo str_replace($find, $replacement, $paginaHTML);
