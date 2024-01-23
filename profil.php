<?php

require_once "__inc.php";

$erreurMdp = null;
$erreurNom = null;

if ($_SERVER['REQUEST_METHOD'] == "POST") {

    switch ($_POST['form']) {
        case "nom":

            try {
                $db->updateProfil($_SESSION['login'],
                    $_POST['nom'],
                    $_POST['prenom']);
            } catch (PDOException $e) {
                $erreurNom = "Une erreur est survenue lors de la modification de votre nom.";
            }

            break;
        case "mdp":

            $user = $db->getUtilisateur($_SESSION['login']);

            if (isset($_POST['omdp']) && isset($_POST['nmdp']) && isset($_POST['nmdp2']))
            {
                if ($_POST['omdp']!="" && $_POST['nmdp']!="" && $_POST['nmdp2']!="")
                {
                    if (!password_verify($_POST['omdp'], $user->getMdp()))
                    {
                        $erreur = "Ancien mot de passe incorrect";
                    }
                    else
                    {
                        if (strcmp($_POST['nmdp'],$_POST['nmdp2']) === 0)
                        {
                            $cptMin = 0;
                            $cptMaj = 0;
                            $cptOth = 0;

                            if(strlen($_POST['nmdp']) >= 8)
                            {

                                $str = $_POST['nmdp'];
                                for ($i=0; $i < strlen($str); $i++) {
                                    if (ctype_alpha($str[$i]) && ctype_upper($str[$i]))
                                    {
                                        $cptMaj++;
                                    }
                                    else if(ctype_alpha($str[$i]) && ctype_lower($str[$i]))
                                    {
                                        $cptMin++;
                                    }
                                    else
                                    {
                                        $cptOth++;
                                    }
                                }
                            }
                            if($cptMin >=2 && $cptMaj>=2 && $cptOth>=2)
                            {
                                $db->updateMdp($_SESSION['login'], password_hash($_POST['nmdp'], PASSWORD_DEFAULT));
                                header("Location: /profil.php");
                            }
                        }
                        else
                        {
                            $erreurMdp = "Le champ de confirmation et le nouveau mot de passe ne correspondent pas";
                        }
                    }
                }
                else
                {
                    $erreurMdp = "Tous les champs sont obligatoires";
                }
            }

            break;
        default: break;
    }
}



echo $twig->render("profil.twig", array(
    "titre"=>"Profil",
    "user"=>$db->getUtilisateur($_SESSION['login']),
    "sections"=>getSidebarSections($_SESSION['login']),
    "options"=>getSidebarOptions("profil"),
    "modules"=>$db->getModules(),
    "erreurMdp"=>$erreurMdp,
    "erreurNom"=>$erreurNom
));