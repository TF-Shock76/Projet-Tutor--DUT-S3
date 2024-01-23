<?php

require_once "__inc.php";


if ($_SERVER['REQUEST_METHOD'] == "POST") {

    $id = $_POST['id'];
    $type = $_POST['Categorie'];
    $libelle = $_POST['Description'];
    $date = $_POST['Date'];
    $duree = $_POST['duree'];
    $seance = $_POST['seance'];

    if (empty($date)) $date = null;

    if (empty($duree)) $duree = null;

    $ev = null;

    if (!empty($id))
        $ev = $db->getEvenement($id);

    $result = false;

    if (!empty($id))
        $newId = $db->updateEvenement($id, $type, $libelle, $date, $duree, $seance);
    else
        $newId = $db->addEvenement($type, $libelle, $date, $duree, $seance);

    $tmpDate = new DateTime($date);

    $dossier = "/documents/".$tmpDate->format("Y")."/".$tmpDate->format("m");
    if (!file_exists(ROOT_PATH.$dossier))
        mkdir(ROOT_PATH.$dossier, 0777, true);

    $fileresult = true;

    foreach ($_FILES["pj"]["error"] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
            $tmp_name = $_FILES["pj"]["tmp_name"][$key];
            // basename() peut empêcher les attaques de système de fichiers;
            // la validation/assainissement supplémentaire du nom de fichier peut être approprié
            $name = basename($_FILES["pj"]["name"][$key]);
            $fileresult &= move_uploaded_file($tmp_name, ROOT_PATH . "$dossier/$name");
            $db->addPJ(basename($_FILES["pj"]["name"][$key]), "$dossier/$name", $newId);
        }
    }

    if (!$newId || !$fileresult) {
        $tpl = $twig->resolveTemplate("creation-evenement.twig");

        $seances = $db->getSeances();

        foreach ($seances as $seance)
            $seance->objmodule = $db->getModule($seance->getModule());

        echo $tpl->render(array(
            "user" => $db->getUtilisateur($_SESSION['login']),
            "titre" => ($ev == null ? "Création" : "Modification") . " d'un évènement",
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "evenement" => $ev,
            "types" => $db->getTypesEvenementForRoles($db->getUtilisateur($_SESSION["login"])->getRoles()),
            "dateMin" => date("Y-m-d"),
            "dateMax" => getCurrentPeriode()[1]->format("Y-m-d"),
            "date" => ($ev == null ? date("Y-m-d") : $ev->getEcheance()),
            "seances" => $seances,
            "message" => "Une erreur est survenue lors de l'application des changements.",
        ));

    } else {
        // Modification avec succès
        header("Location: " . basename(__FILE__));
    }
}


if (isset($_GET['id'])) {
    $tpl = $twig->resolveTemplate("creation-evenement.twig");

    /* @var $ev Evenement */
    $ev = null;

    if ($_GET['id'] != "new")
        $ev = $db->getEvenement($_GET['id']);

    $seances = $db->getSeances();

    foreach ($seances as $seance)
        $seance->objmodule = $db->getModule($seance->getModule());

    echo $tpl->render(array(
        "user" => $db->getUtilisateur($_SESSION['login']),
        "titre" => ($ev == null ? "Création" : "Modification") . " d'un évènement",
        "sections" => getSidebarSections($_SESSION['login']),
        "options" => getSidebarOptions("param"),
        "evenement" => $ev,
        "types" => $db->getTypesEvenementForRoles($db->getUtilisateur($_SESSION["login"])->getRoles()),
        "dateMin" => date("Y-m-d"),
        "dateMax" => getCurrentPeriode()[1]->format("Y-m-d"),
        "date" => ($ev == null ? date("Y-m-d") : $ev->getEcheance()),
        "seances" => $seances,
    ));
} else {

    $tpl = $twig->resolveTemplate("liste-evenements.twig");

    $events = $db->getEvenements();

    $heures = 0;

    foreach ($events as $event)
    {
        /* @var $event Evenement */
        $event->pj =$db->getPJpourEvenement($event->getId());
        $heures += $event->getDuree();
    }

    $duree = sprintf("%d:%02d", floor($heures), (($heures - floor($heures)) * 60));



    echo $tpl->render(
        array(
            "titre" => "Évènements",
            "user" => $db->getUtilisateur($_SESSION['login']),
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "tabEvent" => $events,
            "duree" => $duree,
        )
    );

}