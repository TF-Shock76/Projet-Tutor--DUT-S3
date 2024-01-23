<?php


class Evenement
{
    // Attributs pour PDO
    private $id;
    private $type;
    private $libelle;
    private $duree;
    private $echeance;
    private $seance;

    public $pj;
    public $nom_type;

    /**
     * Evenement constructor.
     * @param $id
     * @param $type
     * @param $libelle
     * @param $duree
     * @param $echeance
     * @param $seance
     */
    public function __construct($id = -1, $type = -1, $libelle = "", $duree = NAN, $echeance = null, $seance = -1)
    {
        $this->id = $id;
        $this->type = $type;
        $this->libelle = $libelle;
        $this->duree = $duree;
        $this->echeance = $echeance;
        $this->seance = $seance;
    }

    public function getId(){return $this->id;}
    public function getType(){return $this->type;}
    public function getLibelle(){return $this->libelle;}
    public function getDuree(){return $this->duree;}
    public function getEcheance(){return $this->echeance;}
    public function getSeance(){return $this->seance;}

    public function getDureeFormat() {
        if ($this->duree == null) return null;

        $heures = $this->duree;
        return sprintf("%d:%02d", floor($heures), (($heures - floor($heures)) * 60));
    }

}