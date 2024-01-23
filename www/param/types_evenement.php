<?php

require_once "__inc.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $oldId = $_POST['old'];
    $libelle = $_POST['libelle'];
    $droits = implode($_POST['droits']);

    if (!empty($oldId))
        $result = $db->updateTypeEvenement($oldId, $libelle, $droits);
    else
        $result = $db->addTypeEvenement($libelle, $droits);

    if (!$result) {
        $te = $db->getTypeEvenement($oldId);

        echo $twig->resolveTemplate("creation-type.twig")->render(array(
            "user" => $db->getUtilisateur($_SESSION['login']),
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "titre" => ($te == null ? "Création" : "Modification") . " d'un type d'évènement",
            "type" => $te,
            "message" => "Une erreur est survenue lors de l'application des changements"
        ));
    } else
        header("Location: " . basename(__FILE__));
} else {
    if (isset($_GET['id'])) {

        $id = $_GET['id'];

        $te = null;

        if ($id != "new") {
            $te = $db->getTypeEvenement($id);
        }

        echo $twig->resolveTemplate("creation-type.twig")->render(array(
            "user" => $db->getUtilisateur($_SESSION['login']),
            "titre" => ($te == null ? "Création" : "Modification") . " d'un type d'évènement",
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "type" => $te
        ));
    } else {
        echo $twig->resolveTemplate("liste-typeevenement.twig")->render(array(
            "user" => $db->getUtilisateur($_SESSION['login']),
            "titre" => "Gestion des types d'évènement",
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "tabTypes" => $db->getTypesEvenement(),

        ));
    }
}