<?php


class Seance
{
    // Attributs pour PDO
    private $id;
    private $module;
    private $date;
    private $date_creation;
    private $date_modification;
    private $type;
    private $groupe;
    private $utilisateur;

    public $obj_module;
    public $allEvenements;
    public $evenements;
    public $nom_type;
    public $obj_user;
    public $semaphore;

    /**
     * Seance constructor.
     * @param $id
     * @param $module
     * @param $date
     * @param $date_creation
     * @param $date_modification
     * @param $type
     * @param $groupe
     * @param $utilisateur
     */
    public function __construct($id = -1, $module = "", $date = null, $date_creation = null,
                                $date_modification = null, $type = -1, $groupe = "", $utilisateur = "")
    {
        $this->id = $id;
        $this->module = $module;
        $this->date = $date;
        $this->date_creation = $date_creation;
        $this->date_modification = $date_modification;
        $this->type = $type;
        $this->groupe = $groupe;
        $this->utilisateur = $utilisateur;
    }

    public function getId(){return $this->id;}
    public function getModule(){return $this->module;}
    public function getDate(){return $this->date;}
    public function getDateCreation(){return $this->date_creation;}
    public function getDateModification(){return $this->date_modification;}
    public function getType(){return $this->type;}
    public function getGroupe(){return $this->groupe;}
    public function getUtilisateur(){return $this->utilisateur;}

    public function getObjmodule() { return $this->objmodule; }

    public function getDateFormatee($format)
    {
        return date_format(new DateTime($this->date), $format);
    }

}