<?php

require_once "DB.php";

function getSidebarSections($login) {
    global $db;
    $roles = $db->getUtilisateur($login)->getRoles();

    $liens = array(
        array("nom"=>"Profil", "url"=>"/profil.php"),
        array("nom"=>"Etat", "url"=>"/etat.php"),
        array("nom"=>"Journal de bord", "url"=>"javascript:alert('Non implémenté')"),
        array("nom"=>"Paramétrage", "url"=>"/param/index.php")
    );

    return $liens;
}

function getSidebarOptions($page) {
    switch ($page)
    {
        case "param":
            return array(
                array("url"=>"/param/options.php", "nom"=>"Options générales"),
                array("url"=>"/param/affectations.php", "nom"=>"Affectations"),
                array("url"=>"/param/evenements.php", "nom"=>"Evenements"),
                array("url"=>"/param/groupes.php", "nom"=>"Groupes"),
                array("url"=>"/param/modules.php", "nom"=>"Modules"),
                array("url"=>"/param/seances.php", "nom"=>"Seances"),
                array("url"=>"/param/types_evenement.php", "nom"=>"Types d'évènement"),
                array("url"=>"/param/types_seance.php", "nom"=>"Types de séance"),
                array("url"=>"/param/utilisateurs.php", "nom"=>"Utilisateurs"),
            );
        case "etat":
            return array(
                array("url"=>"/etat.php","nom"=>"Réinitialiser les filtres")
            );
        default: return array();
    }
}

function getCurrentPeriode($date = null) {
    $debut = new DateTime("09/01");
    $fin   = new DateTime("06/30");

    $today = new DateTime();

    while ($debut > $today)
    {
        $debut = $debut->sub(new DateInterval("P1Y"));
        $fin = $fin->sub(new DateInterval("P1Y"));
    }

    while ($fin < $today)
        $fin = $fin->add(new DateInterval("P1Y"));

    return [$debut, $fin];
}

function getPeriode($anneeDebut) {
    return getCurrentPeriode(new DateTime("09/01/$anneeDebut"));
}