<?php

require_once "__inc.php";

if ($_SERVER['REQUEST_METHOD'] != "POST")
{
    http_response_code(405);
    echo json_encode(array("error"=>"Invalid method"));
    die();
}

if (!isset($_POST['id']))
{
    http_response_code(400);
    echo json_encode(array("error"=>"Identifiant de séance manquant manquant"));
    die();
}

if ($db->getSeance($_POST['id'])->getUtilisateur() != $_SESSION['login'] && // Pas notre séance
    strpos($db->getUtilisateur($_SESSION['login'])->getRoles(), "A") == -1) // Pas un admin
{
    http_response_code(400);
    echo json_encode(array("error"=>"Impossible de supprimer une séance ne nous appartenant pas"));
    die();
}

$id = $_POST['id'];
try {
    $db->deleteSeance($id);
    echo json_encode(array("status"=>"ok"));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array("error"=>"Impossible de supprimer cette séance.."));
}


