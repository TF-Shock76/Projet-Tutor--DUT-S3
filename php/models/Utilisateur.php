<?php

class Utilisateur
{
    // Attributs pour PDO
    private $id;
    private $mdp;
    private $nom;
    private $prenom;
    private $roles;
    private $groupes;
    private $date_creation;
    private $date_modification;
    private $mdp_new;

    /**
     * Utilisateur constructor.
     * @param $id
     * @param $mdp
     * @param $nom
     * @param $prenom
     * @param $roles
     * @param $groupes
     * @param $date_creation
     * @param $date_modification
     * @param $mdp_new
     */
    public function __construct($id="", $mdp="", $nom="", $prenom="", $roles="", $groupes="", $date_creation=null, $date_modification=null, $mdp_new=false)
    {
        $this->id = $id;
        $this->mdp = $mdp;
        $this->nom = $nom;
        $this->prenom = $prenom;
        $this->roles = $roles;
        $this->groupes = $groupes;
        $this->date_creation = DateTime::createFromFormat("Y-m-D", $date_creation);
        $this->date_modification = $date_modification;
        $this->mdp_new = $mdp_new;
    }

    public function getId(){return $this->id;}
    public function getMdp(){return $this->mdp;}
    public function getNom(){return $this->nom;}
    public function getPrenom(){return $this->prenom;}
    public function getRoles(){return $this->roles;}
    public function getGroupes() { return $this->groupes; }
    public function getDateCreation(){return $this->date_creation;}
    public function getDateModification(){return $this->date_modification;}
    public function isMdpNew(){return $this->mdp_new;}

    public function mdpEstValide($mdp)
    {
        return password_verify($mdp, $this->mdp);
    }

    public function getRoleComplet()
    {
        $roles = [];

        if (strpos($this->roles, 'A') > -1)
            $roles[] = "Administrateur";

        if (strpos($this->roles, 'E') > -1)
            $roles[] = "Enseignant";

        if (strpos($this->roles, 'T') > -1)
            $roles[] = "Tuteur";

        $txt = "";

        foreach ($roles as $role)
            $txt = "$txt$role, ";

        return trim($txt, ", ");
    }
}