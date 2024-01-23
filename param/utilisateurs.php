<?php

require_once "__inc.php";


if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $oldId = $_POST['oldId'];
    $id = $_POST['id'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $mdp = $_POST['mdp'];
    $role = $_POST['role'];
    $groupe = $_POST['groupe'];

    $user = $db->getUtilisateur($oldId);

    if (empty($mdp))
        $mdp = $user->getMdp();
    else
        $mdp = password_hash($mdp, PASSWORD_DEFAULT);

    $result = false;

    if (!empty($oldId))
        $result = $db->updateUtilisateur($oldId, $id, $nom, $prenom, $mdp, $role, $groupe);
    else
        $result = $db->addUtilisateur($id, $nom, $prenom, $mdp, $role, $groupe);

    if (!$result) {
        $tpl = $twig->resolveTemplate("creation-utilisateur.twig");

        echo $tpl->render(array(
            "user" => $db->getUtilisateur($_SESSION['login']),
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "titre" => ($user == null ? "Création" : "Modification") . " d'un utilisateur",
            "usrEdit" => $user,
            "ErreurInscription" => "Une erreur est survenue lors de l'application des changements."
        ));
        die();

    } else {
        // Modification avec succès
        header("Location: " . basename(__FILE__));
    }
}


if (isset($_GET['id'])) {
    $tpl = $twig->resolveTemplate("creation-utilisateur.twig");

    $user = null;

    if ($_GET['id'] != "new")
        $user = $db->getUtilisateur($_GET['id']);

    echo $tpl->render(array(
        "user" => $db->getUtilisateur($_SESSION['login']),
        "sections" => getSidebarSections($_SESSION['login']),
        "options" => getSidebarOptions("param"),
        "titre" => ($user == null ? "Création" : "Modification") . " d'un utilisateur",
        "usrEdit" => $user,
    ));
} else {

    $tpl = $twig->resolveTemplate("liste-utilisateurs.twig");

    echo $tpl->render(
        array(
            "titre" => "Utilisateurs",
            "user" => $db->getUtilisateur($_SESSION['login']),
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "tabUser" => $db->getUtilisateurs(),
        )
    );

}