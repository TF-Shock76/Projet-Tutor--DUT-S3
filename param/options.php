<?php

require_once "__inc.php";

if ($_SERVER['REQUEST_METHOD'] == "POST")
{
    $db->updateParametres(array(
        "SeanceMaxParFiltre"=>$_POST['seancesMax'],
        "EvenementMaxParActivite"=>$_POST['eventsMax'],
        "PjMaxParEvenement"=>$_POST['pjMax'],
    ));
}

$param = $db->getParametres();

echo $twig->render("options.twig", array(
    "titre"=>"Options générales",
    "user"=>$db->getUtilisateur($_SESSION['login']),
    "sections"=>getSidebarSections($_SESSION['login']),
    "options"=>getSidebarOptions("param"),
    "seancesMax"=>$param["SeanceMaxParFiltre"],
    "eventsMax"=>$param["EvenementMaxParActivite"],
    "pjMax"=>$param["PjMaxParEvenement"]

));