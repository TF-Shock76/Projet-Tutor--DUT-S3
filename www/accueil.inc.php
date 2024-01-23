<?php

require_once "__inc.php";

$tpl = $twig->resolveTemplate("accueil.twig");
$user = $db->getUtilisateur($_SESSION['login']);

echo $tpl->render(array("user"=>$user, "titre"=>"Accueil",
    "sections"=>getSidebarSections($_SESSION['login']),
    "options"=>getSidebarOptions("accueil")
));