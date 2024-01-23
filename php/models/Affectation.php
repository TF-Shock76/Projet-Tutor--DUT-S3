<?php


class Affectation
{
    // Attributs pour PDO
    private $utilisateur;
    private $module;

    public function __construct($utilisateur="",$module="")
    {
        $this->utilisateur = $utilisateur;
        $this->module = $module;
    }

    public function getUtilisateur(){return $this->utilisateur;}
    public function getModule(){return $this->module;}

}