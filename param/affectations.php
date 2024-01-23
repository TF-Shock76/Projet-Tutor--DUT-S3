<?php

require_once "__inc.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $prof = $_POST['Enseignant'];
    $module = $_POST['Module'];

    $result = $db->addAffectation($prof, $module);

    if (!$result) {
        $tpl = $twig->resolveTemplate("creation-affectation.twig");

        echo $tpl->render(array(
            "user" => $db->getUtilisateur($_SESSION['login']),
            "titre" => ($user == null ? "Création" : "Modification") . " d'un utilisateur",
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "profs" => array_filter($db->getUtilisateurs(), function ($user) {
                return strpos($user->getRoles(), "E") != -1;
            }),
            "modules" => $db->getModules(),
            "ErreurInscription" => "Une erreur est survenue lors de l'application des changements."
        ));
        die();

    } else {
        // Modification avec succès
        header("Location: " . basename(__FILE__));
    }
}


if (isset($_GET['id'])) {
    $tpl = $twig->resolveTemplate("creation-affectation.twig");

    $utilisateurs = $db->getUtilisateurs();

    $profs = [];

    foreach ($utilisateurs as $user) {
        if (strpos($user->getRoles(), "E") > -1)
            $profs[] = $user;
    }

    echo $tpl->render(array(
        "user" => $db->getUtilisateur($_SESSION['login']),
        "titre" => "Ajout d'une affectation",
        "sections" => getSidebarSections($_SESSION['login']),
        "options" => getSidebarOptions("param"),
        "profs" => $profs,
        "modules" => $db->getModules(),
    ));
} else {

    $tpl = $twig->resolveTemplate("liste-affectations.twig");

    $modules = $db->getModules();

    $libs = [];

    /* @var $m Module */
    foreach ($modules as $m) {
        $libs[$m->getCode()] = $m->getLibelle();
    }

    echo $tpl->render(
        array(
            "titre" => "Affectations",
            "user" => $db->getUtilisateur($_SESSION['login']),
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "tabAffectations" => $db->getAffectations(),
            "tabLibModules" => $libs
        )
    );

}