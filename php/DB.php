<?php

// TODO: classes, ajouter des attributs pour les entités liées

require_once "models/Affectation.php";
require_once "models/Semaphore.php";
require_once "models/Evenement.php";
require_once "models/Groupe.php";
require_once "models/PieceJointe.php";
require_once "models/Module.php";
require_once "models/Seance.php";
require_once "models/TypeEvenement.php";
require_once "models/TypeSeance.php";
require_once "models/Utilisateur.php";

// TODO: Retourner l'identifiant de l'objet créé après sa création


class DB
{
    /**
     * @var PDO Objet maintenant la connexion à la base de données.
     */
    private $pdo = null;

    /**
     * @var DB Singleton de l'objet DB
     */
    private static $db = null;

    /**
     * Obtient une instance de la classe DB
     * @return DB une instance de la classe DB
     */
    public static function getInstance()
    {
        if (is_null(DB::$db))
            self::$db = new DB();

        return DB::$db;
    }

    /**
     * DB constructor.
     */
    private function __construct()
    {
        // CONSTANTES DE CONNEXION
        $hostname = "diskus.top";
        $port = 5432;

        $dbName = "diskus2";
        $username = "diskus2";
        $password = "diskus2";

        $this->pdo = new PDO("pgsql:host=$hostname port=$port dbname=$dbName", $username, $password);

        $this->pdo->setAttribute(PDO::ATTR_CASE, PDO::CASE_LOWER);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    }

    private function query($requete, $param, $nomClasse, $customFetchMode = null)
    {
        $req = $this->pdo->prepare($requete);

        if (is_null($param))
            $req->execute();
        else
            $req->execute($param);

        if (!is_null($nomClasse))
            return $req->fetchAll(PDO::FETCH_CLASS|PDO::FETCH_PROPS_LATE, $nomClasse);
        else if (!is_null($customFetchMode))
            return $req->fetchAll($customFetchMode);
        else
            return $req->fetchAll();
    }

    private function update($ordre, $param)
    {
        $req = $this->pdo->prepare($ordre);
        $res = $req->execute($param); //execution de l'ordre SQL
        return $req->rowCount();
    }

    // METHODES PUBLIQUES

    // ===== UTILISATEURS =====

    public function getUtilisateurs()
    {
        return $this->query("SELECT * FROM Utilisateur", null, Utilisateur::class);
    }

    /**
     * Récupère un utilisateur de la base de données.
     * @param string $login Identifiant de l'utilisateur
     * @return Utilisateur|null Utilisateur correspondant s'il existe.
     */
    public function getUtilisateur($login)
    {
        $results = $this->query("SELECT * FROM Utilisateur WHERE Id = ?",
            array($login), Utilisateur::class);

        if (sizeof($results) == 1) return $results[0];
        else return null;
    }

    public function addUtilisateur($id,$nom,$prenom,$mdp,$role,$groupe)
    {
        return $this->update("INSERT INTO Utilisateur (Id, Mdp, Nom, Prenom, Roles, Groupes) VALUES (?,?,?,?,?,?)",
                array($id,$mdp,$nom,$prenom,$role,$groupe)) > 0;
    }

    public function updateUtilisateur($oldId,$id,$nom,$prenom,$mdp,$role,$groupe)
    {
        return $this->update("UPDATE Utilisateur SET Id = ?, Nom = ?, Prenom = ?, Mdp = ?, Roles = ?, Groupes = ? WHERE Id = ?",
                array($id,$nom,$prenom,$mdp,$role,$groupe,$oldId)) > 0;
    }

    public function updateProfil($id, $nom, $prenom)
    {
        return $this->update("UPDATE Utilisateur SET Nom = ?, Prenom = ? WHERE Id = ?",
            array($nom, $prenom, $id)) > 0;
    }

    public function updateMdp($id, $mdp)
    {
        return $this->update("UPDATE Utilisateur SET Mdp = ? WHERE Id = ?",
            array($mdp, $id)) > 0;
    }

    public function deleteUtilisateur($id)
    {
        return $this->update("DELETE FROM Utilisateur WHERE Id = ?", array($id)) > 0;
    }

    // ===== GROUPES =====

    /**
     * @return array Obtenir tous les groupes présents dans la base
     */
    public function getGroupes()
    {
        return $this->query("SELECT * FROM Groupe", null, Groupe::class);
    }

    public function getGroupe($nom)
    {
        $results = $this->query("SELECT * FROM Groupe WHERE Nom = ?",
            array($nom), Groupe::class);

        if (sizeof($results) == 1) return $results[0];
        else return null;
    }

    public function getGroupesPourUtilisateur($login) {
        $user = $this->getUtilisateur($login);

        return explode(":", $user->getGroupes());
    }

    public function addGroupe($nom, $pere)
    {
        return $this->update("INSERT INTO Groupe VALUES (?, ?)", array($nom, $pere)) > 0;
    }

    public function updateGroupe($nom, $pere, $ancienNom)
    {
        return $this->update("UPDATE Groupe SET Nom = ?, Pere = ? WHERE Nom = ?",
            array($nom, $pere, $ancienNom)) > 0;
    }

    /**
     * Supprime un groupe et ses fils.
     * @param $id string Nom du groupe à supprimer
     * @return bool Vrai si une ligne a été supprimée.
     */
    public function deleteGroupe($id)
    {
        return $this->update("DELETE FROM Groupe WHERE Nom = ? ", array($id)) > 0;
    }

    // ===== MODULES =====

    public function getModule($code)
    {
        if (empty($code)) return null;

        $results = $this->query("SELECT * FROM Module WHERE Code = ?", array($code), Module::class);

        if (sizeof($results) == 1) return $results[0];
        else return null;
    }

    public function getModules()
    {
        return $this->query("SELECT * FROM Module", null, Module::class);
    }

    public function getModulesPourUtilisateur($login)
    {
        if (strpos($this->getUtilisateur($login)->getRoles(), "A") != -1)
            return $this->getModules();

        return $this->query("SELECT * FROM Module WHERE Code in 
                           (Select Module from Affectation WHERE Utilisateur = ?)",
            array($login), Module::class);
    }

    public function addModule($code, $libelle, $couleur, $droits)
    {
        try {
            return $this->update("INSERT INTO Module (code, libelle, couleur, droits) VALUES (?,?,?,?)",
                array($code, $libelle, $couleur, $droits)) > 0;
        } catch (PDOException $e) {
            return false;
        }
    }

    public function updateModule($oldCode, $code, $libelle, $color, $droits)
    {
        return $this->update("UPDATE Module SET Code = ?, Libelle = ?, Couleur = ?, Droits = ?, 
                  Date_Modification = now() WHERE Code = ?",
            array( $code, $libelle, $color, $droits, $oldCode)) > 0;
    }

    public function deleteModule($code)
    {
        return $this->update("DELETE FROM Module WHERE Code = ?", array($code)) > 0;
    }

    // ===== AFFECTATIONS =====

    public function getAffectations()
    {
        return $this->query("SELECT * FROM Affectation", null, Affectation::class);
    }

    public function addAffectation($user, $module)
    {
        return $this->update("INSERT INTO Affectation VALUES (?, ?)", array($user, $module)) > 0;
    }

    public function deleteAffectation($utilisateur, $module)
    {
        return $this->update("DELETE FROM Affectation WHERE Utilisateur = ? AND Module = ?",
            array($utilisateur, $module)) > 0;
    }

    // ===== SEANCES =====

    public function getSeance($id)
    {
        if (empty($id)) return null;

        $results = $this->query("SELECT * FROM Seance WHERE Id = ?",
            array($id), Seance::class);

        if (sizeof($results) == 1) return $results[0];
        else return null;
    }

    public function getSeances()
    {
        return $this->query("SELECT * FROM Seance", null, Seance::class);
    }

    public function addSeance($module, $date, $type, $groupe, $utilisateur)
    {
        $this->pdo->beginTransaction();
        $this->update("INSERT INTO Seance(module, date, type, groupe, utilisateur) VALUES (?,?,?,?,?)",
            array($module, $date, $type, $groupe, $utilisateur));
        $id = $this->pdo->lastInsertId();
        $this->pdo->commit();
        return $id;
    }

    // TODO: Ajouter trigger pour date de modification
    public function updateSeance($id, $module, $date, $type, $groupe)
    {
        return $this->update("UPDATE Seance SET Module = ?, Date = ?, Type = ?, Groupe = ?, Date_Modification = now() WHERE Id = ?",
            array($module, $date, $type, $groupe, $id)) > 0;
    }

    public function deleteSeance($id)
    {
        return $this->update("DELETE FROM Seance WHERE Id = ?", array($id)) > 0;
    }

    // ===== EVENEMENTS =====

    public function getEvenement($id)
    {
        if (empty($id)) return null;

        $results = $this->query("SELECT * FROM Evenement WHERE Id = ?",
            array($id), Evenement::class);

        if (sizeof($results) == 1) return $results[0];
        else return null;
    }

    public function getEvenementsPourSeance($seance)
    {
        return $this->query("SELECT * FROM Evenement WHERE Seance = ?", array($seance), Evenement::class);
    }

    public function getEvenements()
    {
        return $this->query("SELECT * FROM Evenement", null, Evenement::class);
    }

    public function addEvenement($type, $libelle, $date, $duree, $seance)
    {
        $this->pdo->beginTransaction();
        $this->update("INSERT INTO Evenement (type, libelle, duree, echeance, seance) VALUES (?, ?, ?, ?, ?)",
                array($type, $libelle, $duree, $date, $seance));
        $id = $this->pdo->lastInsertId();
        $this->pdo->commit();
        return $id;
    }

    public function updateEvenement($id, $type, $libelle, $date, $duree, $seance)
    {
        $this->update("UPDATE Evenement SET Type = ?, Libelle = ?, Echeance = ?, Duree = ?, Seance = ? WHERE Id = ?",
            array($type, $libelle, $date, $duree, $seance, $id));
        return $id;
    }

    public function deleteEvenement($id)
    {
        try {
            $this->pdo->beginTransaction();
            $this->update("DELETE FROM Piece_Jointe WHERE Evenement = ?", array($id));
            $this->update("DELETE FROM Evenement WHERE Id = ?", array($id));
            $this->pdo->commit();
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    // ===== TYPES SEANCE =====

    public function getTypeSeance($id)
    {
        if (empty($id)) return null;

        $results = $this->query("SELECT * FROM Type_Seance WHERE Id = ?",
            array($id), TypeSeance::class);

        if (sizeof($results) == 1) return $results[0];
        else return null;
    }

    public function getTypesSeance()
    {
        return $this->query("SELECT * FROM Type_Seance ", null, TypeSeance::class);
    }

    public function getTypesSeancePourUtilisateur($login) {

        $user = $this->getUtilisateur($login);

        return $this->getTypesSeanceForRoles($user->getRoles());
    }


    public function getTypesSeanceForRoles($roles)
    {
        if (strpos($roles, 'A') != -1) return $this->query("SELECT * FROM Type_Seance WHERE Actif = true",null, TypeSeance::class);

        $types = [];

        foreach (str_split($roles) as $char)
        {
            array_merge_recursive($types, $this->query("SELECT * FROM Type_Seance WHERE actif != false and Roles LIKE ?",
                array("%$char%"), TypeSeance::class));
        }

        return $types;
    }

    public function addTypeSeance($libelle, $droits)
    {
        return $this->update("INSERT INTO Type_Seance (libelle, roles) VALUES (?,?)",
            array($libelle, $droits)) > 0;
    }

    public function updateTypeSeance($oldId, $libelle, $droits)
    {
        return $this->update("UPDATE Type_Seance SET Libelle = ?, Roles = ? WHERE Id = ?",
            array($libelle, $droits, $oldId)) > 0;
    }

    public function deleteTypeSeance($id)
    {
        return $this->update("UPDATE Type_Seance SET Actif = false WHERE Id = ?", array($id)) > 0;
    }

    // ===== TYPES EVENEMENT =====

    public function getTypesEvenement()
    {
        return $this->query("SELECT * FROM Type_Evenement", null, TypeEvenement::class);
    }

    public function getTypesEvenementPourUtilisateur($login) {

        $user = $this->getUtilisateur($login);

        return $this->getTypesEvenementForRoles($user->getRoles());
    }

    public function getTypeEvenement($id)
    {
        if (empty($id)) return null;

        $results = $this->query("SELECT * FROM Type_Evenement WHERE Id = ?",
            array($id), TypeSeance::class);

        if (sizeof($results) == 1) return $results[0];
        else return null;
    }

    public function getTypesEvenementForRoles($roles)
    {
        if (strpos($roles, 'A') != -1) return $this->query("SELECT * FROM Type_Evenement WHERE Actif = true",null, TypeEvenement::class);

        $types = [];

        foreach (str_split($roles) as $char)
        {
            array_merge_recursive($types, $this->query("SELECT * FROM Type_Evenement WHERE Actif = false and Roles LIKE ?",
                array("%$char%"), TypeEvenement::class));
        }

        return $types;
    }

    public function addTypeEvenement($libelle, $droits)
    {
        return $this->update("INSERT INTO Type_Evenement (libelle, roles) VALUES (?,?)",
                array($libelle, $droits)) > 0;
    }

    public function updateTypeEvenement($oldId, $libelle, $droits)
    {
        return $this->update("UPDATE Type_Evenement SET Libelle = ?, Roles = ? WHERE Id = ?",
                array($libelle, $droits, $oldId)) > 0;
    }

    public function deleteTypeEvenement($id)
    {
        return $this->update("UPDATE Type_Evenement SET Actif = false WHERE Id = ?", array($id)) > 0;
    }

    // ===== PIECES JOINTES =====

    public function getPjPourEvenement($evenement)
    {
        return $this->query("SELECT * FROM Piece_Jointe WHERE Evenement = ?",
            array($evenement), PieceJointe::class);
    }

    public function addPJ($filename, $chemin, $evenement)
    {
        return $this->update("INSERT INTO Piece_Jointe (nom_fichier, chemin, evenement) VALUES (?,?,?)",
            array($filename, $chemin, $evenement))>0;
    }

    // ===== PARAMETRES =====

    public function getParametre($parametre)
    {
        return $this->query("SELECT valeur FROM Parametres WHERE Param = ?", array($parametre),
            null)[0][0];
    }

    public function getParametres() {
        return $this->query("SELECT * FROM Parametres", null, null, PDO::FETCH_KEY_PAIR);
    }

    public function updateParametres($params) {
        foreach ($params as $key=>$value)
        {
            $this->update("insert into Parametres values (?,?)
                on conflict (Param) do update set Valeur = Excluded.Valeur", array($key, $value));
        }
    }

    // ===== SEMAPHORES =====

    public function getSemaphores()
    {
        return $this->query("SELECT * FROM Semaphore", null, Semaphore::class);
    }

    public function getSemaphore($seance, $utilisateur, $null = false) {
        if (empty($seance)) return null;
        if (empty($utilisateur)) return null;

        $results = $this->query("SELECT * FROM Semaphore WHERE Utilisateur = ? and Seance = ?", array($utilisateur, $seance),
            Semaphore::class);

        if (sizeof($results) == 0) {
            if (!$null)
                return new Semaphore($utilisateur, $seance, false);
            else
                return null;
        }
        return $results[0];
    }

    public function updateSemaphore($seance, $utilisateur, $etat)
    {
        if ($this->getSemaphore($seance, $utilisateur, true) != null)
            return $this->update("UPDATE Semaphore SET Marque = ? WHERE Utilisateur = ? and Seance = ?",
                array($etat, $utilisateur, $seance)) > 0;
        else
            return $this->update("INSERT INTO Semaphore(Marque, Utilisateur, Seance) values (?,?,?)",
                array($etat, $utilisateur, $seance)) > 0;
    }
}