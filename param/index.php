<?php

require_once "__inc.php";

$user = DB::getInstance()->getUtilisateur($_SESSION['login']);


echo $twig->resolveTemplate("parametrage.twig")
    ->render(array(
        "user" => $user,
        "titre" => "Paramétrage",
        "sections" => getSidebarSections($_SESSION['login']),
        "options" => getSidebarOptions("param")
    ));