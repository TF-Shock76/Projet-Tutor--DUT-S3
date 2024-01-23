<?php


class Semaphore
{
    // Attributs pour PDO
    private $utilisateur;
    private $seance;
    private $marque;

    public function __construct($utilisateur = "", $seance = -1, $marque = false)
    {
        $this->utilisateur = $utilisateur;
        $this->seance = $seance;
        $this->marque = $marque;
    }

    public function getUtilisateur(){return $this->utilisateur;}
    public function getSeance(){return $this->seance;}
    public function getMarque(){return $this->marque;}

    public function setEtat($etat)
    {
        $this->marque = $etat;
    }
}