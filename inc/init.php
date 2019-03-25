<?php

// Définir le fuseau horaire
date_default_timezone_set('Europe/Paris');

// Session
session_start();

// Connexion BDD
$pdo = new PDO(
    'mysql:host=localhost;dbname=boutique',
    'root',
    '',
    array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING,
        PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    )


// Définition de constantes
define('URL','/10-boutique/');
define('SALT','SOLEIL');

// Définition de variables
$contenu = '';
$contenu_gauche = '';
$contenu_droite = '';

// Inclusion du fichier de fonctions
require_once('functions.php');
