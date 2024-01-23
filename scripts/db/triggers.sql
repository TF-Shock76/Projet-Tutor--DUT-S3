CREATE OR REPLACE FUNCTION trig_user_Update() RETURNS TRIGGER AS
$$
BEGIN
    IF (Old.Id = New.Id
        AND Old.Nom = New.Nom
        AND Old.Prenom = New.Prenom
        AND Old.Mdp = New.Mdp
        AND Old.Roles = New.Roles
        AND Old.Groupes = New.Groupes
        AND Old.Date_Creation = New.Date_Creation
        AND Old.Date_Modification = New.Date_Modification)
    THEN

    ELSE
        -- update utilisateur set maj_le = now() where id_utilisateur = OLD.id_utlisateur;
        New.Date_Modification := now();
    END IF;

    RETURN New;
END;
$$ LANGUAGE plpgsql;

DROP TRIGGER Trig_User_Update on Utilisateur;

CREATE TRIGGER Trig_User_Update
    BEFORE UPDATE
    ON Utilisateur
    FOR EACH ROW
EXECUTE PROCEDURE trig_user_Update();