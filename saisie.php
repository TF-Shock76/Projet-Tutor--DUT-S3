<?php

require_once "__inc.php";

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    /*
     * Champs POST:
     *
     * date
     * module
     * type_s
     * groupe
     *
     * eventcount
     *
     * [PAR EVENEMENT]
     *
     * type_e#
     * libelle#
     * pj#[] ($_FILES)
     * duree#
     * date#
     */

    $date = $_POST["date"];
    $module = $_POST["module"];
    $typeSeance = $_POST["type_s"];
    $groupe = $_POST["groupe"];

    $nbEvents = $_POST["eventcount"];

    $events = [];

    for ($i = 0; $i < $nbEvents; $i++)
    {
        $events[] = array(
            "type"=>$_POST["type_e".$i],
            "libelle"=>$_POST["libelle".$i],
            "pj"=>$_FILES["pj".$i],
            "duree"=>$_POST["duree".$i],
            "date"=>$_POST["date".$i],
        );
    }

    $seance = $db->addSeance($module, $date, $typeSeance, $groupe, $_SESSION['login']);
    header("Content-Type: text/plain");
    foreach ($events as $event)
    {
        if (empty($event["date"])) $event["date"] = null;
        if (empty($event["duree"])) $event["duree"] = null;

        $eventId = $db->addEvenement($event["type"], $event["libelle"], $event["date"], $event["duree"], $seance);

        $tmpDate = new DateTime($date);

        $dossier = "/documents/".$tmpDate->format("Y")."/".$tmpDate->format("m");
        if (!file_exists(ROOT_PATH.$dossier))
            mkdir(ROOT_PATH.$dossier, 0777, true);

        $fileresult = true;

        foreach ($event["pj"]["error"] as $key => $error) {
            if ($error == UPLOAD_ERR_OK) {
                $tmp_name = $event["pj"]["tmp_name"][$key];
                // basename() peut empêcher les attaques de système de fichiers;
                // la validation/assainissement supplémentaire du nom de fichier peut être approprié
                $name = basename($event["pj"]["name"][$key]);
                $fileresult &= move_uploaded_file($tmp_name, ROOT_PATH . "$dossier/$name");

                echo "$tmp_name / $name / $fileresult <br>";

                $db->addPJ(basename($event["pj"]["name"][$key]), "$dossier/$name", $eventId);
            }
        }
    }

    // header("Location: /etat.php");
} else {

echo $twig->render("saisie.twig", array(
    "user"=>$db->getUtilisateur($_SESSION['login']),
    "titre"=>"Saisie d'une nouvelle activité",
    "param"=>$db->getParametres(),
    "typeEvenements"=>$db->getTypesEvenementPourUtilisateur($_SESSION['login']),
    "typeSeances"=>$db->getTypesSeancePourUtilisateur($_SESSION['login']),
    "modules"=>$db->getModulesPourUtilisateur($_SESSION['login']),
    "groupes"=>$db->getGroupesPourUtilisateur($_SESSION['login']),
    "sections" => getSidebarSections($_SESSION['login']),
    "options" => getSidebarOptions("param"),
));

}