<?php

require_once "__inc.php";


if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $groupe = $_POST['nomGroupe'];
    $groupeP = $_POST['groupePere'];
    $oldNomGroupe = $_POST['oldNomGroupe'];

    $result = false;

    if (!empty($oldNomGroupe))
        $result = $db->updateGroupe($groupe, $groupeP, $oldNomGroupe);
    else
        $result = $db->addGroupe($groupe, $groupeP);

    if (!$result) {
        $tpl = $twig->resolveTemplate("creation-groupe.twig");

        echo $tpl->render(array(
            "titre" => "Modification d'un groupe",
            "user" => $db->getUtilisateur($_SESSION['login']),
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "gp" => $groupe,
            "allGroupes" => $db->getGroupes(),
            "ErreurInscription" => "Une erreur est survenue lors de l'application des changements."
        ));
        die();

    } else {
        // Modification avec succès
        header("Location: " . basename(__FILE__));
    }
}


if (isset($_GET['id'])) {
    $tpl = $twig->resolveTemplate("creation-groupe.twig");

    $grp = null;

    if ($_GET['id'] != "new")
        $grp = $db->getGroupe($_GET['id']);

    echo $tpl->render(array(
        "user" => $db->getUtilisateur($_SESSION['login']),
        "titre" => ($grp == null ? "Création" : "Modification") . " d'un groupe",
        "sections" => getSidebarSections($_SESSION['login']),
        "options" => getSidebarOptions("param"),
        "gp" => $grp,
        "allGroupes" => $db->getGroupes()
    ));
} else {

    $tpl = $twig->resolveTemplate("liste-groupes.twig");

    echo $tpl->render(
        array(
            "titre" => "Groupes",
            "user" => $db->getUtilisateur($_SESSION['login']),
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "groupes" => $db->getGroupes(),
        )
    );

}