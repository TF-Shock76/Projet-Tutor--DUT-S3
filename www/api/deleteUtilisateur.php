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
    echo json_encode(array("error"=>"Nom d'utilisateur manquant"));
    die();
}

if ($_POST['id'] == $_SESSION['login'])
{
    http_response_code(400);
    echo json_encode(array("error"=>"Impossible de supprimer l'utilisateur courant!"));
    die();
}

$id = $_POST['id'];
try {
    $db->deleteUtilisateur($id);
    echo json_encode(array("status"=>"ok"));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array("error"=>"Impossible de supprimer cet utilisateur."));
}


