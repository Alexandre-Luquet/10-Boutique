<?php

require_once('inc/init.php');

// déconnexion
if( isset($_GET['action']) && $_GET['action']=='deconnexion'){
    session_destroy();
}

// Internauté déjà connecté
if( isConnected() ){
    header('location:' . URL . 'compte.php');
    exit();
}
// Formulaire posté ?
if( !empty($_POST) ){
    if(empty($_POST['pseudo']) || empty($_POST['mdp'])){
        $contenu .= '<div class="alert alert-danger">Merci de saisir vos identifiants</div>';
    }
    else{
        $result = execRequete("SELECT * FROM membre WHERE pseudo=:pseudo AND mdp=:mdp",array(
            'pseudo' => $_POST['pseudo'],
            'mdp' => md5($_POST['mdp'] . SALT)
        ));
        if( $result->rowCount() != 0 ){
            // j'ai trouvé le membre et les identifiants sont corrects
            $membre = $result->fetch();
            $_SESSION['membre'] = $membre;
            unset( $_SESSION['membre']['mdp'] ); // on retire un champ de la variable de session
            header('location:' . URL . 'compte.php');
            exit();
        }
        else{
            // pb sur les identifiants ou utilisateur inconnu
            $contenu .='<div class="alert alert-danger">Erreur sur les identifiants ou utilisateur introuvable</div>';
        }
    }
}

require_once('inc/header.php');
echo $contenu;
?>
<h3>Veuillez renseigner vos identifiants :</h3>
<form method="post" action="">
    <div class="form-group">
        <label for="pseudo">Pseudo</label>
        <input type="text" name="pseudo" id="pseudo" class="form-control" value="<?= $_POST['pseudo'] ?? '' ?>">        
    </div>
    <div class="form-group">
        <label for="mdp">Mot de passe</label>
        <input type="password" name="mdp" id="mdp" class="form-control">
    </div>
    <input type="submit" class="btn btn-primary" value="Se connecter">
</form>
<?php
require_once('inc/footer.php');