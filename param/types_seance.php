<?php

require_once "__inc.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $oldId = $_POST['old'];
    $libelle = $_POST['libelle'];
    $droits = implode($_POST['droits']);

    if (!empty($oldId))
        $result = $db->updateTypeSeance($oldId, $libelle, $droits);
    else
        $result = $db->addTypeSeance($libelle, $droits);

    if (!$result) {
        $ts = $db->getTypeSeance($oldId);

        echo $twig->resolveTemplate("creation-type.twig")->render(array(
            "user" => $db->getUtilisateur($_SESSION['login']),
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "titre" => ($ts == null ? "Création" : "Modification") . " d'un type de séance",
            "type" => $ts,
            "message" => "Une erreur est survenue lors de l'application des changements"
        ));
    } else
        header("Location: " . basename(__FILE__));
} else {
    if (isset($_GET['id'])) {

        $id = $_GET['id'];

        $ts = null;

        if ($id != "new") {
            $ts = $db->getTypeSeance($id);
        }

        echo $twig->resolveTemplate("creation-type.twig")->render(array(
            "user" => $db->getUtilisateur($_SESSION['login']),
            "titre" => ($ts == null ? "Création" : "Modification") . " d'un type de séance",
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "type" => $ts
        ));
    } else {
        echo $twig->resolveTemplate("liste-typeseance.twig")->render(array(
            "user" => $db->getUtilisateur($_SESSION['login']),
            "titre" => "Gestion des types de séance",
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "tabTypes" => $db->getTypesSeance()
        ));
    }
}