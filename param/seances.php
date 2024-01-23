<?php

require_once "__inc.php";


if ($_SERVER['REQUEST_METHOD'] == "POST") {
    $id = $_POST['id'];
    $module = $_POST['Module'];
    $date = $_POST['Date'];
    $type = $_POST['Type'];
    $groupe = $_POST['Groupe'];

    $seance = $db->getSeance($id);

	$dateMin = new DateTime('09/01');
	$dateMax = new DateTime();
	$crea    = getUtilisateur($_SESSION['login'])->getCreeLe();
	list($year,$month,$day) = explode('-', $crea);
	$date2 = new DateTime();
	$date2->setDate( $year, 9, 1);
	
    $result = false;

    if (!empty($id))
        $result = $db->updateSeance($id, $module, $date, $type, $groupe);
    else
        $result = $db->addSeance($module, $date, $type, $groupe, $_SESSION['login']);

    if (!$result) {
        $tpl = $twig->resolveTemplate("creation-seance.twig");

        echo $tpl->render(array(
            "user" => $db->getUtilisateur($_SESSION['login']),
            "titre" => ($seance == null ? "Création" : "Modification") . " d'une seance",
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "modules" => $db->getModules(),
            "types" => $db->getTypesSeance(),
            "seance" => $seance,
            "groupes" => $db->getGroupes(),
            "dateMin" => $date2,
            "dateMax" => $dateMax,
            "date" =>date("Y-m-d"),
            "message" => "Une erreur est survenue lors de l'application des changements."
        ));
        die();

    } else {
        // Modification avec succès
        header("Location: " . basename(__FILE__));
    }
}


if (isset($_GET['id'])) {
    $tpl = $twig->resolveTemplate("creation-seance.twig");

    $seance = null;

    if ($_GET['id'] != "new")
        $seance = $db->getSeance($_GET['id']);

    echo $tpl->render(array(
        "user" => $db->getUtilisateur($_SESSION['login']),
        "titre" => ($seance == null ? "Création" : "Modification") . " d'une seance",
        "sections" => getSidebarSections($_SESSION['login']),
        "options" => getSidebarOptions("param"),
        "modules" => $db->getModules(),
        "types" => $db->getTypesSeance(),
        "seance" => $seance,
        "groupes" => $db->getGroupes(),
        "date" => date("Y-m-d"),
    ));
} else {

    $tpl = $twig->resolveTemplate("liste-seances.twig");

    $users = $db->getUtilisateurs();
    $profs = [];
    /* @var $u Utilisateur */
    foreach ($users as $u) {
        $profs[$u->getId()] = $u->getPrenom() . " " . $u->getNom();
    }

    $ts = $db->getTypesSeance();
    $types = [];

    /* @var $t TypeSeance */
    foreach ($ts as $t) {
        $types[$t->getId()] = $t->getLibelle();
    }

    // Tab modules
    $modules = $db->getModules();

    $libs = [];

    /* @var $m Module */
    foreach ($modules as $m) {
        $libs[$m->getCode()] = $m->getLibelle();
    }

    echo $tpl->render(
        array(
            "titre" => "Séances",
            "user" => $db->getUtilisateur($_SESSION['login']),
            "sections" => getSidebarSections($_SESSION['login']),
            "options" => getSidebarOptions("param"),
            "tabSeance" => $db->getSeances(),
            "tabTypesSeance" => $types,
            "tabNomsModules" => $libs,
            "tabNomsPrenomsProfs" => $profs
        )
    );

}