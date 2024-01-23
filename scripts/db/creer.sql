DROP TABLE IF EXISTS Utilisateur CASCADE;
DROP TABLE IF EXISTS Groupe CASCADE;
DROP TABLE IF EXISTS Type_Seance CASCADE;
DROP TABLE IF EXISTS Type_Evenement CASCADE;
DROP TABLE IF EXISTS Module CASCADE;
DROP TABLE IF EXISTS Affectation CASCADE;
DROP TABLE IF EXISTS Seance CASCADE;
DROP TABLE IF EXISTS Evenement CASCADE;
DROP TABLE IF EXISTS Piece_Jointe CASCADE;
DROP TABLE IF EXISTS Semaphore CASCADE;
DROP TABLE IF EXISTS Parametres CASCADE;

CREATE TABLE Utilisateur
(
    Id                VARCHAR(15) PRIMARY KEY,
    Mdp               VARCHAR(60),
    Nom               VARCHAR(20),
    Prenom            VARCHAR(20),
    Roles             VARCHAR(3),
    Groupes           VARCHAR(1024),
    Date_Creation     DATE DEFAULT now(),
    Date_Modification DATE DEFAULT now(),
    Mdp_New           BOOL DEFAULT TRUE
);

CREATE TABLE Groupe
(
    Nom  VARCHAR(8) PRIMARY KEY,
    Pere VARCHAR(8) -- REFERENCES GROUPE(nom)
);

CREATE TABLE Type_Seance
(
    Id      SERIAL PRIMARY KEY,
    Libelle VARCHAR(10),
    Roles   VARCHAR(3),
    Actif   BOOL DEFAULT TRUE
);

CREATE TABLE Type_Evenement
(
    Id      SERIAL PRIMARY KEY,
    Libelle VARCHAR(10),
    Roles   VARCHAR(3),
    Actif   BOOL DEFAULT TRUE
);

CREATE TABLE Module
(
    Code              VARCHAR(8) PRIMARY KEY,
    Libelle           VARCHAR(20),
    Couleur           VARCHAR(6),
    Droits            VARCHAR(3),
    Date_Creation     DATE DEFAULT now(),
    Date_Modification DATE DEFAULT now()
);

CREATE TABLE Affectation
(
    Utilisateur VARCHAR(15) REFERENCES Utilisateur,
    Module      VARCHAR(8) REFERENCES Module,
    PRIMARY KEY (Utilisateur, Module)
);

CREATE TABLE Seance
(
    Id                SERIAL PRIMARY KEY,
    Module            VARCHAR(8) REFERENCES Module,
    Date              DATE DEFAULT now(),
    Date_Creation     DATE DEFAULT now(),
    Date_Modification DATE DEFAULT now(),
    Type              INT REFERENCES Type_Seance,
    Groupe            VARCHAR(8), -- references GROUPE,
    Utilisateur       VARCHAR(15) REFERENCES Utilisateur
);

CREATE TABLE Evenement
(
    Id       SERIAL PRIMARY KEY,
    Type     INT REFERENCES Type_Evenement,
    Libelle  VARCHAR(90),
    Duree    DOUBLE PRECISION,
    Echeance DATE,
    Seance   INT REFERENCES Seance
);

CREATE TABLE Piece_Jointe
(
    Id          SERIAL PRIMARY KEY,
    Nom_Fichier VARCHAR(256),
    Chemin      VARCHAR(1024),
    Evenement   INT REFERENCES Evenement
);

CREATE TABLE Semaphore
(
    Utilisateur VARCHAR(15) REFERENCES Utilisateur,
    Seance      INT REFERENCES Seance,
    Marque      BOOLEAN,
    PRIMARY KEY (Utilisateur, Seance)
);

CREATE TABLE Parametres
(
    Param  VARCHAR(64) PRIMARY KEY,
    Valeur INT
);

