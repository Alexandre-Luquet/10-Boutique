<?php

require_once('inc/init.php');
$inscription = false;
if( !empty($_POST) ){
    // traitement du formulaire
    // controle des champs vides
    $nb_champs_vides = 0;
    $errors = array();
    foreach($_POST as $valeur){
        if(empty($valeur)) $nb_champs_vides++;
    }
    if( $nb_champs_vides > 0){
        $errors[] = 'Il manque '. $nb_champs_vides . ' information(s)';
    }
    // controle du pseudo
    // utilisation des reg exp (expressions régulières)
    $verif_caracteres = preg_match('#^[\w.-]{3,20}$#',$_POST['pseudo']);
    // \w => a-zA-Z0-9_
    if( !$verif_caracteres ){
        $errors[] = 'Le pseudo doit comporter entre 3 et 20 caractères (a à z, 0 à 9 ._-)';
    }

    // controle du code postal
    $verif_cp = preg_match('#^[0-9]{5}$#',$_POST['code_postal']);
    if ( !$verif_cp ){
        $errors[] = "Le code postal n'est pas valide";
    }

    // controle de l'email
    if( !filter_var($_POST['email'],FILTER_VALIDATE_EMAIL) ){
        $errors[] = "L'adresse mail n'est pas valide";
    }

    // Bilan des contrôles
    if ( empty($errors) ){
        // inscription
        // verifier l'unicité du pseudo
        $membre = execRequete("SELECT * FROM membre WHERE pseudo=:pseudo",array('pseudo' => $_POST['pseudo']));
        if ( $membre->rowCount() > 0 ){
            $contenu .= '<div class="alert alert-warning">Pseudo indisponible, merci d\'en choisir un autre</div>';
        }
        else
        {
            // Tout est ok, on peut insérer en BDD
            extract($_POST);
            execRequete("INSERT INTO membre VALUES (NULL,:pseudo,:mdp,:nom,:prenom,:email,:civilite,:ville,:code_postal,:adresse,0)",array(
                'pseudo'    => $pseudo,
                'mdp'       => md5( $mdp . SALT ),
                'nom'       => $nom,
                'prenom'    => $prenom,
                'email'     => $email,
                'civilite'  => $civilite,
                'ville'     => $ville,
                'code_postal'=> $code_postal,
                'adresse'   => $adresse
            ));
            $inscription = true;
            $contenu .= '<div class="alert alert-success">Vous êtes inscrit ! <a href="'.URL . 'connexion.php">Cliquer ici pour vous connecter</a></div>';
        }

    }
    else
    {
        $contenu .= '<div class="alert alert-danger">'.implode('<br>',$errors).'</div>';
    }


}

require_once('inc/header.php');
echo $contenu;
if ( !$inscription ):
    // formulaire d'inscription affiché tant que l'inscription n'a pas abouti
    ?>
    <h3>Veuillez renseigner le formulaire pour vous inscrire</h3>
    <form method="post" action="">
        <fieldset>
            <legend>Identifiants</legend>
            <div class="form-group">
                <label for="pseudo">Pseudo</label>
                <input type="text" name="pseudo" id="pseudo" class="form-control" value="<?= $_POST['pseudo'] ?? '' ?>">
            </div>
            <div class="form-group">
                <label for="mdp">Mot de passe</label>
                <input type="password" name="mdp" id="mdp" class="form-control" value="">
            </div>
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" class="form-control" value="<?= $_POST['email'] ?? '' ?>">
            </div>
        </fieldset>

        <fieldset>
            <legend>Coordonnées</legend>
            <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="civilite" id="radiom" value="m" <?= ( !isset($_POST['civilite']) || (isset($_POST['civilite']) && $_POST['civilite'] == 'm') ) ? 'checked' : '' ?>>
                <label class="form-check-label" for="radiom">Monsieur</label>
            </div>
            <div class="form-check form-check-inline">
                <input type="radio" class="form-check-input" name="civilite" id="radiof" value="f" <?= ( isset($_POST['civilite']) && $_POST['civilite'] == 'f') ? 'checked' : '' ?>>
                <label class="form-check-label" for="radiof">Madame</label>
            </div>
            <div class="form-row">
                <div class="form-group col-6">
                    <label for="prenom">Prénom</label>
                    <input type="text" class="form-control" name="prenom" id="prenom" value="<?= $_POST['prenom'] ?? '' ?>">
                </div>
                <div class="form-group col-6">
                    <label for="nom">Nom</label>
                    <input type="text" class="form-control" name="nom" id="nom" value="<?= $_POST['nom'] ?? '' ?>">
                </div>
            </div>
            <div class="form-group">
                <label for="adresse">Adresse</label>
                    <input type="text" class="form-control" name="adresse" id="adresse" value="<?= $_POST['adresse'] ?? '' ?>">
            </div>
            <div class="form-row">
                <div class="form-group col-3">
                    <label for="code_postal">Code Postal</label>
                    <input type="text" class="form-control" name="code_postal" id="code_postal" value="<?= $_POST['code_postal'] ?? '' ?>">
                </div>
                <div class="form-group col-9">
                    <label for="ville">Ville</label>
                    <input type="text" class="form-control" name="ville" id="ville" value="<?= $_POST['ville'] ?? '' ?>">
                </div>
            </div>
        </fieldset>
        <input type="submit" class="btn btn-primary" value="S'inscrire">
    </form>
    <?php
endif;
require_once('inc/footer.php');