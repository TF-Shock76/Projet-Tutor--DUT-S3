<?php


class Groupe
{
    // Attributs pour PDO
    private $nom;
    private $pere;

    /**
     * Groupe constructor.
     * @param string $nom
     * @param null $pere
     */
    public function __construct($nom = "", $pere = null)
    {
        $this->nom = $nom;
        $this->pere = $pere;
    }

    public function getNom() { return $this->nom; }
    public function getPere() { return $this->pere; }
}