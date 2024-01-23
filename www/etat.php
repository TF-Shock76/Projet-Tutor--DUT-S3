<?php

require_once "__inc.php";

$tpl = $twig->resolveTemplate("etat.twig");
/*
 * changement mois et année avec boutons
 */

if (!isset($_POST['month+']) && !isset($_POST['month-']))
{
    $moisActuel    = intval(date('m'))-1;
    $anneeActuelle = intval(date('Y'));
}
else
{
    $moisActuel = $_POST['month'];
    $anneeActuelle = $_POST['year'];
}

$tabMois = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");

/* Pour changer le mois courant */
if (isset($_POST['month+']))
{
    $moisActuel = $moisActuel+1;
}
if (isset($_POST['month-']))
{
    $moisActuel = $moisActuel-1;
}
/*------------------------------*/

/* Pour changer l'année si besoin */
if ($moisActuel < 0)
{
    $moisActuel    = 11;
    $anneeActuelle = $anneeActuelle-1;
}
if ($moisActuel > 11)
{
    $moisActuel    = 0;
    $anneeActuelle = $anneeActuelle+1;
}
/*------------------------------*/



///////////////////////////////////////////////////////



/* @var $seances Seance */
$allSeances     = $db->getSeances();
$modules        = $db->getModules();
$types          = $db->getTypesSeance();
$groupes        = $db->getGroupes();
$users          = $db->getUtilisateurs();
$typesEvenement = $db->getTypesEvenement();

$date = new DateTime(date("Y-m-d"));

/* Dates de début et de fin d'année */
$dateMin = new DateTime('09/01');
if (intval($date->diff($dateMin)->format("%R%d")) > 0)
    $dateMin->setDate(intval($dateMin->format("Y"))-1, 9, 1);

$dateMax = new DateTime('06/30');
if (intval($date->diff($dateMax)->format("%R%d")) < 0)
    $dateMax->setDate(intval($dateMax->format("Y"))+1, 6, 30);

/* (Filtres) Dates de début et fin de mois */
$dateCreaMinMois = new DateTime();
$dateCreaMinMois->setDate($anneeActuelle, $moisActuel+1, 1);

$dateCreaMaxMois = new DateTime();
$dateCreaMaxMois->setDate($anneeActuelle, $moisActuel+2, 1);

$crea    = $db->getUtilisateur($_SESSION['login'])->getDateCreation();
list($year,$month,$day) = explode('-', $crea);
$date2 = new DateTime();
$date2->setDate( $year, 9, 1);


/* (Filtres) Dates de création minimales et maximales */
$dateCreaMin  = new DateTime();
$dateCreaMin->setDate(date("Y"), date('m'), 1);
/* De façon à ne pas pourvoir entrer une date incohérente : */
if (intval($date->diff($dateCreaMin)->format("%R%d")) > 0)
    $dateCreaMin->setDate(intval($dateCreaMin->format("Y"))-1, 9, 1);

$dateCreaMax  = new DateTime();
$dateCreaMax->setDate(date("Y"), date('m')+1, 1);
/* De façon à ne pas pourvoir entrer une date incohérente : */
if (intval($date->diff($dateCreaMax)->format("%R%d")) < 0)
    $dateCreaMax->setDate(intval($dateCreaMax->format("Y"))+1, 6, 30);


/* (Filtres) Dates d'échéance minimales et maximales */
$echeanceMin  = new DateTime('09/01');
/* De façon à ne pas pourvoir entrer une date incohérente : */
if (intval($date->diff($echeanceMin)->format("%R%d")) > 0)
    $echeanceMin->setDate(intval($echeanceMin->format("Y"))-1, 9, 1);

$echeanceMax  = new DateTime('06/30');
/* De façon à ne pas pourvoir entrer une date incohérente : */
if (intval($date->diff($echeanceMax)->format("%R%d")) < 0)
    $echeanceMax->setDate(intval($echeanceMax->format("Y"))+1, 6, 30);

///////////////////////////////////////////////////////



/* Initialisation des filtres à vide */
$tabModules      = array();
$tabGroupes      = array();
$tabTypes        = array();
$tabTypesEv      = array();
$tabCreateurs    = array();
$tabSemaphores   = array();
$tabPiece_jointe = array();

/* Si le filtre a été renseigné, alors sa valeur est prise */
if (isset($_POST['DateCreaMin'])) {
    $dateCreaMin = new DateTime($_POST['DateCreaMin']);
}
else {
    $dateCreaMin = $dateCreaMinMois;
}
if (isset($_POST['DateCreaMax'])) {
    $dateCreaMax = new DateTime($_POST['DateCreaMax']);
}
else {
    $dateCreaMax = $dateCreaMaxMois;
}
if (isset($_POST['EcheanceMin'])) {
    $echeanceMin = new DateTime($_POST['EcheanceMin']);
}
if (isset($_POST['EcheanceMax'])) {
    $echeanceMax = new DateTime($_POST['EcheanceMax']);
}
if (isset($_POST['Modules'])) {
    $tabModules = $_POST['Modules'];
}
if (isset($_POST['Types'])) {
    $tabTypes = $_POST['Types'];
}
if (isset($_POST['TypesEv'])) {
    $tabTypesEv = $_POST['TypesEv'];
}
if (isset($_POST['Groupes'])) {
    $tabGroupes = $_POST['Groupes'];
}
if (isset($_POST['Createurs'])) {
    $tabCreateurs = $_POST['Createurs'];
}

////////////////////////////////////////////////////////////////////

if (isset($_POST['month'])) {
    $dateCreaMin = new DateTime();
    $dateCreaMin->setDate($anneeActuelle,$moisActuel+1,1);
}
if (isset($_POST['month'])) {
    $dateCreaMax = new DateTime();
    $dateCreaMax->setDate($anneeActuelle,$moisActuel+2,0);
}

////////////////////////////////////////////////////////////////////


/* ajout des groupes fils à la liste des groupes sélectionnés */

/* @var $g Groupe */
foreach ($groupes as $g) {
    if (in_array($g->getPere(), $tabGroupes) && !in_array($g->getNom(), $tabGroupes)) {
        array_push($tabGroupes, $g->getGroupe());
    }
}

/* Selection des seances par rapport aux filtres */

$seancesFiltrees = array();

/* @var $s Seance */
foreach ($allSeances as $s) {
    $dateCrea = new DateTime($s->getDate());
    if ( intval($dateCrea->diff($dateCreaMin)->format("%R%d")) <= 0 &&
        intval($dateCrea->diff($dateCreaMax)->format("%R%d")) >= 0 &&
        (in_array($s->getModule(),      $tabModules  ) || empty($tabModules  )) &&
        (in_array($s->getGroupe(),      $tabGroupes  ) || empty($tabGroupes  )) &&
        (in_array($s->getType(),        $tabTypes    ) || empty($tabTypes    )) &&
        (in_array($s->getUtilisateur(), $tabCreateurs) || empty($tabCreateurs)))
    {
        array_push($seancesFiltrees, $s);
    }
}

/* @var $seance Seance */
foreach ($seancesFiltrees as $seance)
{
    $seance->obj_module = $db->getModule($seance->getModule());
    $seance->allEvenements = $db->getEvenementsPourSeance($seance->getId());

    /* Selection des évènements par rapport aux filtres */
    $seance->evenements = array();


    foreach ($seance->allEvenements as $e) {
        $echeance = new DateTime($e->getEcheance());
        if ( intval($echeance->diff($echeanceMin)->format("%R%d")) <= 0 &&
            intval($echeance->diff($echeanceMax)->format("%R%d")) >= 0 &&
            (in_array($e->getType(), $tabTypesEv ) || empty($tabTypesEv)))
        {
            array_push($seance->evenements, $e);
        }
    }
    /*--------------------------------------------------*/

    /* @var $event Evenement */
    foreach ($seance->evenements as $event)
    {
        $event->nom_type = $db->getTypeEvenement($event->getType())->getLibelle();
        $event->pj = $db->getPjPourEvenement($event->getId());
    }

    $seance->nom_type   = $db->getTypeSeance($seance->getType())->getLibelle();
    $seance->obj_user   = $db->getUtilisateur($seance->getUtilisateur());
    $seance->semaphore  = $db->getSemaphore($seance->getId(), $_SESSION['login']);

    $tabSemaphores[] = $seance->semaphore;
}

/* association des séances avec leur numero de semaine */

$tabSeancesFiltrees = array();

/* @var $seance Seance */
foreach ($seancesFiltrees as $seance) {
    if (!array_key_exists(date($seance->getDate()), $tabSeancesFiltrees)) {
        $date = new DateTime(date($seance->getDate()));
        $numSemaine     = intval($date->format("W"));
        $seancesSemaine = array();

        foreach ($seancesFiltrees as $s) {
            $dateSeance       = new DateTime(date($s->getDate()));
            $numSemaineSeance = $dateSeance->format("W");
            if ($numSemaineSeance == $numSemaine) {
                array_push($seancesSemaine, $s);
            }
        }

        $tabSeancesFiltrees[$numSemaine] = $seancesSemaine;
    }
}

ksort($tabSeancesFiltrees, SORT_NUMERIC);
$tabSeancesFiltrees = array_reverse($tabSeancesFiltrees, true);

/* sauvegarde des sémaphores */

if (isset($_POST['save'])) {
    /* @var $semaphore Semaphore */
    foreach ($tabSemaphores as $semaphore) {
        if ($semaphore !== null) {
            if (isset($_POST['sem'])) {
                if (in_array($semaphore->getSeance(), $_POST['sem'])) {
                    $db->updateSemaphore($semaphore->getSeance(), $_SESSION['login'], "t");
                    $semaphore->setEtat(true);
                }
                else {
                    $db->updateSemaphore($semaphore->getSeance(), $_SESSION['login'], "f");
                    $semaphore->setEtat(false);
                }
            }
            else {
                $db->updateSemaphore($semaphore->getSeance(), $_SESSION['login'], "t");
                $semaphore->setEtat(false);
            }
        }
    }
}

echo $tpl->render(array("titre"=>"Etat des séances",
    "sections"=>getSidebarSections($_SESSION['login']),
    "options"=>getSidebarOptions("etat"),
    "tabSeances"        => $tabSeancesFiltrees,
    "modules"        => $modules,
    "types"          => $types,
    "groupes"        => $groupes,
    "users"          => $users,
    "typesEvenement" => $typesEvenement,
    "tabModules"     => $tabModules,
    "tabTypes"       => $tabTypes,
    "tabTypesEv"     => $tabTypesEv,
    "tabGroupes"     => $tabGroupes,
    "sections" => getSidebarSections($_SESSION['login']),
    "options" => getSidebarOptions("param"),
    "tabCreateurs"   => $tabCreateurs,
    "tabSemaphores"  => $tabSemaphores,
    "tabPiece_jointe"=> $tabPiece_jointe,
    "debutAnnee"     => strval($dateMin->format("Y-m-d")),
    "finAnnee"       => strval($dateMax->format("Y-m-d")),
    "dateCreaMinMois"=> strval($dateCreaMinMois->format("Y-m-d")),
    "dateCreaMaxMois"=> strval($dateCreaMaxMois->format("Y-m-d")),
    "dateCreaMin"    => strval($dateCreaMin->format("Y-m-d")),
    "dateCreaMax"    => strval($dateCreaMax->format("Y-m-d")),
    "dateEvMin"      => strval($echeanceMin->format("Y-m-d")),
    "dateEvMax"      => strval($echeanceMax->format("Y-m-d")),
    "moisActuel"     => $tabMois[$moisActuel]." ".$anneeActuelle,
    "chiffreMois"    => $moisActuel,
    "chiffreAnnee"   => $anneeActuelle
));
