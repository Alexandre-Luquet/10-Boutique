<?php

require_once('inc/init.php');
if ( !isConnected() ){
    header('location:'.  URL . 'connexion.php');
    exit();
}

$contenu = '<h2>Bienvenue '.$_SESSION['membre']['pseudo'].'</h2>';

require_once('inc/header.php');

echo $contenu;
var_dump($_SESSION);

require_once('inc/footer.php');