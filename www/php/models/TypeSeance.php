<?php


class TypeSeance
{
    // Attributs pour PDO
    private $id;
    private $libelle;
    private $roles;
    private $actif;

    /**
     * TypeSeance constructor.
     * @param $id
     * @param $libelle
     * @param $roles
     * @param $actif
     */
    public function __construct($id=-1, $libelle="", $roles="", $actif=false)
    {
        $this->id = $id;
        $this->libelle = $libelle;
        $this->roles = $roles;
        $this->actif = $actif;
    }

    public function getId(){return $this->id;}
    public function getLibelle(){return $this->libelle;}
    public function getRoles() { return $this->roles; }
    public function getActif(){return $this->actif;}

}