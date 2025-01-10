<?php

require_once "./php/Database.php";
require_once "./php/Navbar.php";
session_start();

function getForm($voto, $commento, $timestamp) {
    $form = '';
    $form .= '<form "mod-recensione" class="user-form" action="modifica-recensione.php" method="post">';
    $form .= '<fieldset>';
    $form .= '<legend>Modifica recensione</legend>';
    $form .= '<fieldset id="stelle-recensione">';
    $form .= '<legend>Dai un voto in stelle</legend>';
    $form .= '<input type="radio" id="voto-1" name="voto" value="1"' . ($voto == 1 ? "checked" : ""). '/>';
    $form .= '<label class="star" for="voto-1">&#9733;</label>';
    $form .= '<input type="radio" id="voto-2" name="voto" value="2"' . ($voto == 2 ? "checked" : ""). '/>';
    $form .= '<label class="star" for="voto-2">&#9733;</label>';
    $form .= '<input type="radio" id="voto-3" name="voto" value="3"' . ($voto == 3 ? "checked" : ""). '/>';
    $form .= '<label class="star" for="voto-3">&#9733;</label>';
    $form .= '<input type="radio" id="voto-4" name="voto" value="4"' . ($voto == 4 ? "checked" : ""). '/>';
    $form .= '<label class="star" for="voto-4">&#9733;</label>';
    $form .= '<input type="radio" id="voto-5" name="voto" value="5"' . ($voto == 5 ? "checked" : ""). '/>';
    $form .= '<label class="star" for="voto-5">&#9733;</label>';
    $form .= '</fieldset>';
    $form .= '<label for="recensione">Recensione:</label>';
    $form .= '<textarea id="commento" name="commento" maxlength="200" required>' . $commento . '</textarea>';
    $form .= '<input type="hidden" name="timestamp" value="' . $timestamp . '"/>';
    $form .= '<input type="submit" value="Modifica" class="button" name="modifica"></input>';
    $form .= '</fieldset>';
    $form .= '</form>';
    return $form;
}

function getRecensione($database, $utente, $timestamp) {
        $query = "SELECT * FROM recensione WHERE utente=? AND timestamp=?";
        $value = array($utente,$timestamp);
        $recensione = $database->executeSelectPreparedStatement($query,'ss',$value);

        if (count($recensione) == 1) {
            $recensione = $recensione[0];
            $voto = $recensione["voto"];
            $commento = $recensione["commento"];
            $timestamp = $recensione["timestamp"];

            return getForm($voto, $commento, $timestamp);
        }
        # THROW 404
}

$recensione_html = "";
$avvisoRecensione = "";

if (isset($_POST['modifica']) && isset($_SESSION['username'])) {
    $database = new Database();
    $connessioneOK = $database->openConnection();
    if (!$connessioneOK) {
        $voto = $_POST["voto"];
        $commento = $_POST["commento"];
        $timestamp = $_POST["timestamp"];
        $utente = $_SESSION['username'];
        $newTimestamp = date("Y-m-d h:i:s");

        $query = "UPDATE recensione SET commento = ?, voto = ?, timestamp = ? WHERE utente = ? AND timestamp = ?";
        $value = array($commento, $voto, $newTimestamp, $utente, $timestamp);
        $avvisoRecensione .= $database->executeCRUDPreparedStatement($query, 'sisss', $value);

        # Se non va bene l'execute mostro l'avviso altrimenti redirect
        #$recensione_html .= getRecensione($database, $_SESSION["username"], $newTimestamp);

        header('Location: ./profilo.php');
    }
}

if (isset($_GET['modifica_x']) && isset($_SESSION['username'])) {
    $database = new Database();
    $connessioneOK = $database->openConnection();

    if (!$connessioneOK) {
        $username = $_SESSION['username'];
        $date = $_GET['timestamp'];
        $recensione_html .= getRecensione($database, $username, $date);
    }
}

$navbar = new Navbar("");
$paginaHTML = file_get_contents('./static/modifica-recensione.html');

$find=['{{AVVISO_RECENSIONE}}', '{{RECENSIONE}}', '{{NAVBAR}}'];
$replacement=[$avvisoRecensione, $recensione_html, $navbar->getNavbar()];
echo str_replace($find,$replacement,$paginaHTML);
