<?php

require_once "__inc.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $oldCode = $_POST['old'];
    $code = $_POST['code'];
    $libelle = $_POST['libelle'];
    $color = $_POST['color'];
    $droits = implode($_POST['droits']);

    if (!empty($oldCode))
        $result = $db->updateModule($oldCode, $code, $libelle, $color, $droits);
    else
        $result = $db->addModule($code, $libelle, $color, $droits);

    if (!$result) {
        $module = $db->getModule($oldCode);

        echo $twig->resolveTemplate("creation-module.twig")->render(array(
            "user" => $db->getUtilisateur($_SESSION['login']),
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "titre" => ($module == null ? "Création" : "Modification") . " d'un module",
            "module" => $module,
            "message" => "Une erreur est survenue lors de l'application des changements"
        ));
    } else
        header("Location: " . basename(__FILE__));
} else {
    if (isset($_GET['id'])) {

        $id = $_GET['id'];

        $module = null;

        if ($id != "new") {
            $module = $db->getModule($id);
        }

        echo $twig->resolveTemplate("creation-module.twig")->render(array(
            "user" => $db->getUtilisateur($_SESSION['login']),
            "titre" => ($module == null ? "Création" : "Modification") . " d'un module",
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "module" => $module
        ));
    } else {
        echo $twig->resolveTemplate("liste-modules.twig")->render(array(
            "user" => $db->getUtilisateur($_SESSION['login']),
            "titre" => "Gestion des modules",
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "modules" => $db->getModules()
        ));
    }
}