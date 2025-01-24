<?php

require_once "./php/Database.php";
require_once "./php/Navbar.php";
session_start();

function getForm($voto, $commento, $timestamp,$prevPage) {
    $form = '';
    $form .= '<form id="mod-recensione" class="user-form" action="modifica-recensione.php" method="post">';
    $form .= '<fieldset>';
    $form .= '<legend>Modifica recensione</legend>';
    $form .= '<label for="voto">Dai un voto in stelle:</label>';
    $form .= '<input type="range" min="1" max="5" id="voto" name="voto" list="values"  value="'.$voto.'">';
    $form .= '<datalist aria-hidden="true" id="values">';
    $form .= '<option value="1" label="1&#9733;"></option>';
    $form .= '<option value="2" label="2&#9733;"></option>';
    $form .= '<option value="3" label="3&#9733;"></option>';
    $form .= '<option value="4" label="4&#9733;"></option>';
    $form .= '<option value="5" label="5&#9733;"></option>';
    $form .= '</datalist>';
    $form .= '<label for="commento">Recensione:</label>';
    $form .= '<textarea id="commento" name="commento" maxlength="200" required>' . $commento . '</textarea>';
    $form.='<input type="hidden" name="currentPage" value="'.$prevPage.'">';
    $form .= '<input type="hidden" name="timestamp" value="' . $timestamp . '">';
    $form .= '<input type="submit" value="Modifica" class="button" name="modifica">';
    $form .= '</fieldset>';
    $form .= '</form>';
    return $form;
}

function getRecensione($database, $utente, $timestamp,$prevPage) {
        $query = "SELECT * FROM recensione WHERE utente=? AND timestamp=?";
        $value = array($utente,$timestamp);
        $recensione = $database->executeSelectPreparedStatement($query,'ss',$value);

        if (count($recensione) == 1) {
            $recensione = $recensione[0];
            $voto = $recensione["voto"];
            $commento = $recensione["commento"];
            $timestamp = $recensione["timestamp"];

            return getForm($voto, $commento, $timestamp,$prevPage);
        }else{
            header('Location: ./404.php');
            exit;
        }
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

        header('Location: .'.$_POST['currentPage']);
        exit;
    }else{
        header('Location: ./500.php');
        exit;
    }
}

if (isset($_GET['modifica_x']) && isset($_SESSION['username'])) {
    $database = new Database();
    $connessioneOK = $database->openConnection();

    if (!$connessioneOK) {
        $username = $_SESSION['username'];
        $date = $_GET['timestamp'];
        $prevPage = $_GET['currentPage'];
        $recensione_html .= getRecensione($database, $username, $date,$prevPage);
    }else{
        header('Location: ./500.php');
        exit;
    }
}

$navbar = new Navbar("");
$paginaHTML = file_get_contents('./static/modifica-recensione.html');

$find=['{{AVVISO_RECENSIONE}}', '{{RECENSIONE}}', '{{NAVBAR}}'];
$replacement=[$avvisoRecensione, $recensione_html, $navbar->getNavbar()];
echo str_replace($find,$replacement,$paginaHTML);
