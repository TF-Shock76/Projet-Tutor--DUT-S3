<?php

require_once "__inc.php";

if ($_SERVER['REQUEST_METHOD'] != "POST")
{
    http_response_code(405);
    echo json_encode(array("error"=>"Invalid method"));
    die();
}

if (!isset($_POST['user']) || !isset($_POST['module']))
{
    http_response_code(400);
    echo json_encode(array("error"=>"Informations manquantes"));
    die();
}

$user = $_POST['user'];
$module = $_POST['module'];
try {
    $db->deleteAffectation($user, $module);
    echo json_encode(array("status"=>"ok"));
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(array("error"=>"Impossible de supprimer cette affectation."));
}


